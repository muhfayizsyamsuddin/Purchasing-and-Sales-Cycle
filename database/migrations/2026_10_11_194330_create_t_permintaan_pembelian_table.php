<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_permintaan_pembelian', function (Blueprint $table) {
            $table->id('id_permintaan');
            $table->string('no_faktur')->unique();
            $table->date('tanggal');
            $table->date('tanggal_diperlukan');
            $table->string('status')->default('Draft');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_permintaan_pembelian');
    }
};