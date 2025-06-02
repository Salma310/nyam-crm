<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 't_transaksi';
    protected $primaryKey = 'transaksi_id';
    protected $fillable = [
        'transaksi_id',
        'kode_transaksi',
        'agen_id',
        'diskon_transaksi',
        'pajak_transaksi',
        'harga_total',
        'tgl_transaksi',
    ];

    public function agen()
    {
        return $this->belongsTo(Agen::class, 'agen_id', 'agen_id');
    }
    
    public function detailTransaksi()
{
    return $this->hasMany(DetailTransaksi::class, 'transaksi_id', 'transaksi_id');
}
}
