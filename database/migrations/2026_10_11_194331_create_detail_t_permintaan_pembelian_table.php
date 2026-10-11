<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_t_permintaan_pembelian', function (Blueprint $table) {
            $table->id('id_detail');

            $table->unsignedBigInteger('id_permintaan');
            $table->string('sku', 255);
            $table->unsignedInteger('kuantitas');
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->foreign('id_permintaan')
                ->references('id_permintaan')
                ->on('t_permintaan_pembelian')
                ->cascadeOnDelete();

            $table->foreign('sku')
                ->references('sku')
                ->on('m_barang')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_t_permintaan_pembelian');
    }
};