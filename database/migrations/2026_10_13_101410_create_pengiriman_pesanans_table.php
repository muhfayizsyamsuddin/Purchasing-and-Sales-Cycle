<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_pesanan', function (Blueprint $table) {
            $table->id('id_pengiriman_pesanan');

            $table->string('no_pesanan');
            $table->date('tanggal');
            $table->string('nama_pengirim');

            $table->timestamps();

            $table->foreign('no_pesanan')
                ->references('no_pesanan')
                ->on('pesanan_penjualan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_pesanan');
    }
};