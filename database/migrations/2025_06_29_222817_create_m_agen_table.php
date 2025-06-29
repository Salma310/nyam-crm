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
        Schema::create('m_agen', function (Blueprint $table) {
            $table->id('agen_id');
            $table->unsignedBigInteger('level_agen_id')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('no_telf', 50)->nullable();
            $table->timestamps();

            $table->foreign('level_agen_id')->references('level_agen_id')->on('m_level_agen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_agen');
    }
};
