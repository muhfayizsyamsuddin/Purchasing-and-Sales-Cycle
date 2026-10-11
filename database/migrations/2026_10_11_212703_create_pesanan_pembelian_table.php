<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_pembelian', function (Blueprint $table) {
            $table->string('no_order_pembelian', 255)->primary();
            $table->date('tanggal');
            $table->date('tanggal_dibutuhkan');

            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);

            $table->unsignedBigInteger('id_vendor');
            $table->string('no_faktur', 255)->nullable();

            $table->timestamps();

            $table->foreign('id_vendor')
                ->references('id_vendor')
                ->on('m_vendor')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_pembelian');
    }
};