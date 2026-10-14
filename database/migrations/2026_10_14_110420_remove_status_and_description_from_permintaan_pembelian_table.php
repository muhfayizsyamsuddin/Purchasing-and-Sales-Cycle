
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_pembelian', function (Blueprint $table) {
            $table->dropColumn(['status', 'description']);
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_pembelian', function (Blueprint $table) {
            $table->string('status')->default('Draft');
            $table->text('description')->nullable();
        });
    }
};
