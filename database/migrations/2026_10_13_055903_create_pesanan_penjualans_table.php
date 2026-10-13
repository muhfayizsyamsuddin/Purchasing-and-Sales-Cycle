<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_penjualan', function (Blueprint $table) {
            $table->string('no_pesanan')->primary();
            $table->date('tanggal');
            $table->string('nama_pembeli');
            $table->text('alamat');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('PPN', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_penjualan');
    }
};