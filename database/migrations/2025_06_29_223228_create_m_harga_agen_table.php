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
        Schema::create('m_harga_agen', function (Blueprint $table) {
            $table->id('harga_agen_id');
            $table->unsignedBigInteger('level_agen_id')->nullable();
            $table->string('kota')->nullable();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->double('harga', 12, 2)->nullable();
            $table->double('diskon', 10, 2)->default(0)->nullable();
            $table->double('diskon_persen', 5, 2)->default(0)->nullable();
            $table->timestamps();

            $table->foreign('level_agen_id')->references('level_agen_id')->on('m_level_agen');
            $table->foreign('barang_id')->references('barang_id')->on('m_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_harga_agen');
    }
};
