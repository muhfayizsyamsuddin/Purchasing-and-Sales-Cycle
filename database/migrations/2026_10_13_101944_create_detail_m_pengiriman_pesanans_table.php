<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_m_pengiriman_pesanan', function (Blueprint $table) {
            $table->id('id_detail');

            $table->unsignedBigInteger('id_pengiriman_pesanan');
            $table->string('sku');
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('total')->default(0);

            $table->timestamps();

            $table->foreign('id_pengiriman_pesanan')
                ->references('id_pengiriman_pesanan')
                ->on('pengiriman_pesanan')
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
        Schema::dropIfExists('detail_m_pengiriman_pesanan');
    }
};