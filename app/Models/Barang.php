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
}
