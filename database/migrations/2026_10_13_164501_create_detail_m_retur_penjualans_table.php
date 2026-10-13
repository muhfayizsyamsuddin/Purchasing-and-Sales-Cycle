<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_m_retur_penjualan', function (Blueprint $table) {
            $table->id('id_detail');

            $table->string('no_retur');
            $table->string('sku');

            $table->unsignedInteger('jumlah');
            $table->decimal('harga_unit', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('no_retur')
                ->references('no_retur')
                ->on('retur_penjualan')
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
        Schema::dropIfExists('detail_m_retur_penjualan');
    }
};