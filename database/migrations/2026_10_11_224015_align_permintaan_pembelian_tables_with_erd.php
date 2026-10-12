<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan nama tabel tujuan belum digunakan.
        if (
            Schema::hasTable('permintaan_pembelian') ||
            Schema::hasTable('detail_m_permintaan_pembelian')
        ) {
            throw new \RuntimeException(
                'Tabel tujuan sudah ada. Periksa database sebelum melanjutkan.'
            );
        }

        // Pastikan semua no_faktur pada pesanan yang terisi
        // mengacu ke permintaan yang benar.
        $orphanedOrders = DB::table('pesanan_pembelian as po')
            ->whereNotNull('po.no_faktur')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('t_permintaan_pembelian as pp')
                    ->whereColumn('pp.no_faktur', 'po.no_faktur');
            })
            ->exists();

        if ($orphanedOrders) {
            throw new \RuntimeException(
                'Ada pesanan dengan no_faktur yang tidak ditemukan pada permintaan pembelian.'
            );
        }
        $unmappedDetails = DB::table('detail_t_permintaan_pembelian as d')
            ->leftJoin(
                't_permintaan_pembelian as p',
                'p.id_permintaan',
                '=',
                'd.id_permintaan'
            )
            ->whereNull('p.id_permintaan')
            ->exists();

        if ($unmappedDetails) {
            throw new \RuntimeException(
                'Ada detail yang tidak memiliki permintaan pembelian.'
            );
        }

        // Tambahkan no_faktur di detail lama.
        Schema::table('detail_t_permintaan_pembelian', function (Blueprint $table) {
            $table->string('no_faktur', 255)
                ->nullable()
                ->after('id_permintaan');
        });

        // Salin relasi dari id_permintaan ke no_faktur.
        DB::statement("
            UPDATE detail_t_permintaan_pembelian AS d
            INNER JOIN t_permintaan_pembelian AS p
                ON p.id_permintaan = d.id_permintaan
            SET d.no_faktur = p.no_faktur
        ");

        if (
            DB::table('detail_t_permintaan_pembelian')
                ->whereNull('no_faktur')
                ->exists()
        ) {
            throw new \RuntimeException(
                'Sebagian detail tidak berhasil dipetakan ke no_faktur. Migrasi dihentikan.'
            );
        }

        // Putuskan relasi lama ke id_permintaan dan hapus kolom lamanya.
        DB::statement("
            ALTER TABLE detail_t_permintaan_pembelian
                DROP FOREIGN KEY detail_t_permintaan_pembelian_id_permintaan_foreign,
                DROP INDEX detail_t_permintaan_pembelian_id_permintaan_foreign,
                DROP COLUMN id_permintaan,
                MODIFY no_faktur VARCHAR(255) NOT NULL
        ");
        DB::statement("
            ALTER TABLE t_permintaan_pembelian
            MODIFY id_permintaan BIGINT UNSIGNED NOT NULL
        ");

        // Jadikan no_faktur sebagai primary key header.
        DB::statement("
            ALTER TABLE t_permintaan_pembelian
                DROP INDEX t_permintaan_pembelian_no_faktur_unique,
                DROP PRIMARY KEY,
                DROP COLUMN id_permintaan,
                ADD PRIMARY KEY (no_faktur)
        ");

        // Gunakan nama tabel sesuai ERD.
        Schema::rename(
            't_permintaan_pembelian',
            'permintaan_pembelian'
        );

        Schema::rename(
            'detail_t_permintaan_pembelian',
            'detail_m_permintaan_pembelian'
        );

        // Detail permintaan mengacu ke no_faktur header.
        DB::statement("
            ALTER TABLE detail_m_permintaan_pembelian
                ADD INDEX detail_m_permintaan_pembelian_no_faktur_index (no_faktur),
                ADD CONSTRAINT detail_m_permintaan_pembelian_no_faktur_foreign
                    FOREIGN KEY (no_faktur)
                    REFERENCES permintaan_pembelian (no_faktur)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
        ");

        // Pesanan pembelian mengacu ke permintaan pembelian.
        DB::statement("
            ALTER TABLE pesanan_pembelian
                ADD INDEX pesanan_pembelian_no_faktur_index (no_faktur),
                ADD CONSTRAINT pesanan_pembelian_no_faktur_foreign
                    FOREIGN KEY (no_faktur)
                    REFERENCES permintaan_pembelian (no_faktur)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE
        ");
    }

    public function down(): void
    {
        // Lepas foreign key yang ditambahkan oleh migrasi ini.
        DB::statement("
            ALTER TABLE pesanan_pembelian
                DROP FOREIGN KEY pesanan_pembelian_no_faktur_foreign,
                DROP INDEX pesanan_pembelian_no_faktur_index
        ");

        DB::statement("
            ALTER TABLE detail_m_permintaan_pembelian
                DROP FOREIGN KEY detail_m_permintaan_pembelian_no_faktur_foreign,
                DROP INDEX detail_m_permintaan_pembelian_no_faktur_index
        ");

        // Bangun kembali id_permintaan untuk skema lama.
        DB::statement("
            ALTER TABLE permintaan_pembelian
                ADD COLUMN id_permintaan BIGINT UNSIGNED NULL FIRST
        ");

        DB::statement('SET @request_row_number = 0');

        DB::statement("
            UPDATE permintaan_pembelian
            SET id_permintaan = (@request_row_number := @request_row_number + 1)
            ORDER BY no_faktur
        ");

        DB::statement("
            ALTER TABLE permintaan_pembelian
                MODIFY id_permintaan BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                ADD UNIQUE KEY permintaan_pembelian_id_permintaan_unique (id_permintaan)
        ");

        // Bangun kembali FK lama di detail.
        DB::statement("
            ALTER TABLE detail_m_permintaan_pembelian
                ADD COLUMN id_permintaan BIGINT UNSIGNED NULL AFTER id_detail
        ");

        DB::statement("
            UPDATE detail_m_permintaan_pembelian AS d
            INNER JOIN permintaan_pembelian AS p
                ON p.no_faktur = d.no_faktur
            SET d.id_permintaan = p.id_permintaan
        ");

        DB::statement("
            ALTER TABLE detail_m_permintaan_pembelian
                DROP COLUMN no_faktur,
                MODIFY id_permintaan BIGINT UNSIGNED NOT NULL
        ");

        // Pulihkan primary key lama dan nama tabel.
        DB::statement("
            ALTER TABLE permintaan_pembelian
                DROP PRIMARY KEY,
                DROP INDEX permintaan_pembelian_id_permintaan_unique,
                ADD UNIQUE KEY t_permintaan_pembelian_no_faktur_unique (no_faktur),
                ADD PRIMARY KEY (id_permintaan)
        ");

        Schema::rename(
            'permintaan_pembelian',
            't_permintaan_pembelian'
        );

        Schema::rename(
            'detail_m_permintaan_pembelian',
            'detail_t_permintaan_pembelian'
        );

        DB::statement("
            ALTER TABLE detail_t_permintaan_pembelian
                ADD INDEX detail_t_permintaan_pembelian_id_permintaan_foreign (id_permintaan),
                ADD CONSTRAINT detail_t_permintaan_pembelian_id_permintaan_foreign
                    FOREIGN KEY (id_permintaan)
                    REFERENCES t_permintaan_pembelian (id_permintaan)
                    ON DELETE CASCADE
        ");
    }
};
