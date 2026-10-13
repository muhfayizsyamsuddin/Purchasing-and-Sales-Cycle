<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_penjualan', function (Blueprint $table) {
            $table->string('no_pembayaran')->primary();

            $table->date('tanggal');
            $table->string('terima_dari');
            $table->text('terbilang');
            $table->text('untuk_pembayaran');
            $table->decimal('jumlah', 15, 2)->default(0);

            $table->string('no_faktur');

            $table->timestamps();

            $table->foreign('no_faktur')
                ->references('no_faktur')
                ->on('faktur_penjualan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_penjualan');
    }
};