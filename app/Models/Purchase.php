<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 't_transaksi_masuk';
    protected $primaryKey = 'transaksi_masuk_id';
    protected $fillable = [
        'transaksi_masuk_id',
        'kode_transaksi_masuk',
        'diskon_transaksi',
        'pajak_transaksi',
        'harga_total',
        'tgl_transaksi',
    ];

    public function detailPurchase()
    {
        return $this->hasMany(DetailPurchase::class, 'transaksi_masuk_id', 'transaksi_masuk_id');
    }
}
