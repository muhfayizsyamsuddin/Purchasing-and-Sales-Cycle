<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_pembelian', function (Blueprint $table) {
            $table->string('no_retur', 255)->primary();
            $table->string('no_penerimaan', 255);
            $table->date('tanggal');
            $table->string('nama_pembeli', 255);
            $table->text('alamat_pembeli');
            $table->string('nama_penjual', 255);
            $table->text('alamat_penjual');
            $table->decimal('jumlah', 15, 2);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2);

            $table->timestamps();

            $table->foreign('no_penerimaan')
                ->references('no_penerimaan')
                ->on('penerimaan_barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::create('detail_m_retur_pembelian', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('no_retur', 255);
            $table->string('sku', 255);
            $table->unsignedInteger('jumlah');
            $table->decimal('harga_unit', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2);

            $table->timestamps();

            $table->foreign('no_retur')
                ->references('no_retur')
                ->on('retur_pembelian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('sku')
                ->references('sku')
                ->on('m_barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_m_retur_pembelian');
        Schema::dropIfExists('retur_pembelian');
    }
};
