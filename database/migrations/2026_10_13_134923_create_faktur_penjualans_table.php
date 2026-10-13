<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faktur_penjualan', function (Blueprint $table) {
            $table->string('no_faktur')->primary();

            $table->string('nama');
            $table->string('no_telp');
            $table->text('alamat');
            $table->date('tanggal');
            $table->string('jangka_waktu_pembayaran');

            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('PPN', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);

            $table->unsignedBigInteger('id_pengiriman_pesanan');

            $table->timestamps();

            $table->foreign('id_pengiriman_pesanan')
                ->references('id_pengiriman_pesanan')
                ->on('pengiriman_pesanan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faktur_penjualan');
    }
};