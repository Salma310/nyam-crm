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
        'agen_id',
        'nama',
        'email',
        'no_telf',
        'alamat',
        'kecamatan',
        'kota',
        'provinsi',
        'status',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'kode_transaksi', 'agen_id');
    }

}
