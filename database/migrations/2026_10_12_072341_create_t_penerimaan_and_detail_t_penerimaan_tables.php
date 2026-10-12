<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_barang', function (Blueprint $table) {
            $table->string('no_penerimaan', 255)->primary();
            $table->date('tanggal_penerimaan');
            $table->string('no_order_pembelian', 255);

            $table->timestamps();

            $table->foreign('no_order_pembelian')
                ->references('no_order_pembelian')
                ->on('pesanan_pembelian')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::create('detail_m_penerimaan_barang', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('no_penerimaan', 255);
            $table->string('sku', 255);
            $table->unsignedInteger('kuantitas');
            $table->string('kondisi_barang', 255);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->foreign('no_penerimaan')
                ->references('no_penerimaan')
                ->on('penerimaan_barang')
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
        Schema::dropIfExists('detail_m_penerimaan_barang');
        Schema::dropIfExists('penerimaan_barang');
    }
};