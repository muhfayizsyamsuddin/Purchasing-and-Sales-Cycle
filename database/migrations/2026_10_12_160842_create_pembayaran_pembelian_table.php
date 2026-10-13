<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_pembelian', function (Blueprint $table) {
            $table->string('no_pembayaran', 255)->primary();
            $table->date('tanggal');
            $table->string('terima_dari', 255);
            $table->text('terbilang');
            $table->text('untuk_pembayaran');
            $table->decimal('jumlah', 15, 2);
            $table->string('no_faktur', 255);

            $table->timestamps();

            $table->foreign('no_faktur')
                ->references('no_faktur')
                ->on('faktur_pembelian')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pembelian');
    }
};