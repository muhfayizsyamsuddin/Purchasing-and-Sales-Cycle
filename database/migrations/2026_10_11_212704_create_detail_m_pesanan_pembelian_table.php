<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_m_pesanan_pembelian', function (Blueprint $table) {
            $table->id('id_detail');

            $table->string('sku', 255);
            $table->string('no_order_pembelian', 255);

            $table->unsignedInteger('kuantitas');
            $table->decimal('harga_unit', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('sku')
                ->references('sku')
                ->on('m_barang')
                ->restrictOnDelete();

            $table->foreign('no_order_pembelian')
                ->references('no_order_pembelian')
                ->on('pesanan_pembelian')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_m_pesanan_pembelian');
    }
};