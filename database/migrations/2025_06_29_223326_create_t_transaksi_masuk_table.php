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
        Schema::create('t_transaksi_masuk', function (Blueprint $table) {
             $table->id('transaksi_masuk_id');
            $table->string('kode_transaksi_masuk',50)->unique()->nullable();
            $table->double('diskon_transaksi')->nullable();
            $table->double('pajak_transaksi')->nullable();
            $table->double('harga_total')->nullable();
            $table->date('tgl_transaksi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_transaksi_masuk');
    }
};
