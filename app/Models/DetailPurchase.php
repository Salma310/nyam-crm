<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPurchase extends Model
{
    use HasFactory;

    protected $table = 't_detail_transaksi_masuk';
    protected $primaryKey = 'detail_transaksi_masuk_id';
    protected $fillable = [
        'detail_transaksi_masuk_id',
        'transaksi_masuk_id',
        'barang_id',
        'qty'
    ];
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'transaksi_masuk_id', 'transaksi_masuk_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
