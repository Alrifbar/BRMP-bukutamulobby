<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            $table->integer('edit_attempts')->default(3)->after('keperluan_lainnya');
            $table->string('unique_token')->nullable()->after('edit_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            $table->dropColumn(['edit_attempts', 'unique_token']);
        });
    }
};
