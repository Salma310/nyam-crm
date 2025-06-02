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
            $table->unsignedBigInteger('agen_id')->nullable();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->double('harga', 12, 2)->nullable();
            $table->double('diskon', 10, 2)->default(0)->nullable();
            $table->double('diskon_persen', 5, 2)->default(0)->nullable();
            $table->double('pajak', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('agen_id')->references('agen_id')->on('m_agen');
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
