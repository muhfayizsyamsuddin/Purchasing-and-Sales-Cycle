<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_penjualan', function (Blueprint $table) {
            $table->string('no_retur')->primary();

            $table->unsignedBigInteger('id_pengiriman_pesanan');
            $table->date('tanggal');

            $table->string('nama_pembeli');
            $table->text('alamat_pembeli');
            $table->string('nama_penjual');
            $table->text('alamat_penjual');

            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('PPN', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);

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
        Schema::dropIfExists('retur_penjualan');
    }
};