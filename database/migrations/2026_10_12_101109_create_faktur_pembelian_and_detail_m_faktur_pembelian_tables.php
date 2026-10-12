<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faktur_pembelian', function (Blueprint $table) {
            $table->string('no_faktur', 255)->primary();

            $table->string('nama', 255);
            $table->string('no_telp', 50);
            $table->text('alamat');
            $table->date('tanggal');
            $table->string('jangka_waktu_pembayaran', 255);

            $table->decimal('jumlah', 15, 2);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2);

            $table->string('no_penerimaan', 255);

            $table->timestamps();

            $table->foreign('no_penerimaan')
                ->references('no_penerimaan')
                ->on('penerimaan_barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::create('detail_m_faktur_pembelian', function (Blueprint $table) {
            $table->id('id_detail');

            $table->string('no_faktur', 255);
            $table->string('sku', 255);
            $table->unsignedInteger('kuantitas');
            $table->decimal('harga_unit', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2);

            $table->timestamps();

            $table->foreign('no_faktur')
                ->references('no_faktur')
                ->on('faktur_pembelian')
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
        Schema::dropIfExists('detail_m_faktur_pembelian');
        Schema::dropIfExists('faktur_pembelian');
    }
};