<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agen extends Model
{
    use HasFactory;

    protected $table = 'm_agen';
    protected $primaryKey = 'agen_id';
    protected $fillable = [
        'nama',
        'email',
        'no_telf',
        'alamat',
        'kecamatan',
        'kota',
        'provinsi',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'agen_id', 'agen_id');
    }
    public function hargaAgen()
    {
        return $this->hasMany(HargaAgen::class, 'agen_id', 'agen_id');
    }
}
