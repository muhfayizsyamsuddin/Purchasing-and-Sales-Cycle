<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faktur_penjualan', function (Blueprint $table) {
            $table->renameColumn('PPN', 'ppn');
        });
    }

    public function down(): void
    {
        Schema::table('faktur_penjualan', function (Blueprint $table) {
            $table->renameColumn('ppn', 'PPN');
        });
    }
};