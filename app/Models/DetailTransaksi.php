<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 't_detail_transaksi';
    protected $primaryKey = 'detail_transaksi_id';
    protected $fillable = [
        'detail_transaksi_id',
        'transaksi_id',
        'barang_id',
        'qty',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }
    
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

     public function hargaAgen()
    {
        return $this->belongsTo(HargaAgen::class, 'harga_agen_id', 'harga_agen_id');
    }
   
}
