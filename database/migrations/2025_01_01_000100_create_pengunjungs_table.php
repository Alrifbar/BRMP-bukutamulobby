<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengunjungs', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->unsignedInteger('usia')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('instansi', 150)->nullable();
            $table->string('yang_ditemui', 100)->nullable();
            $table->unsignedTinyInteger('keperluan_kategori');
            $table->string('keperluan_lainnya', 255)->nullable();
            $table->timestamp('tanggal_kunjungan')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengunjungs');
    }
};
