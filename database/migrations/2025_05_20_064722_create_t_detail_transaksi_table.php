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
        Schema::create('t_detail_transaksi', function (Blueprint $table) {
            $table->id('detail_transaksi_id');
            $table->unsignedBigInteger('transaksi_id')->nullable();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();

            $table->foreign('transaksi_id')->references('transaksi_id')->on('t_transaksi');
            $table->foreign('barang_id')->references('barang_id')->on('m_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_detail_transaksi');
    }
};
