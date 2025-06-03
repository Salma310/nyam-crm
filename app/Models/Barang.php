<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id';
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kalori',
        'komposisi',
        'kandungan',
        'ukuran',
        'pic',
        'hpp',
        'stok'
    ];

    public function detailTransaksi()
    {
        return $this->hasMany(\App\Models\DetailTransaksi::class, 'barang_id', 'barang_id')
            ->with('transaksi');
    }

    public function detailTransaksiMasuk()
    {
        return $this->hasMany(\App\Models\DetailPurchase::class, 'barang_id', 'barang_id')
            ->with('Purchase');
    }
}
