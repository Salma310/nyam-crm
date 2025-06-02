<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaAgen extends Model
{
    use HasFactory;

    protected $table = 'm_harga_agen';
    protected $primaryKey = 'harga_agen_id';
    protected $fillable = [
        'harga_agen_id',
        'agen_id',
        'barang_id',
        'harga',
        'diskon',
        'diskon_persen',
        'pajak',
    ];

    public function agen()
    {
        return $this->belongsTo(Agen::class, 'agen_id', 'agen_id');
    }
    
   public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
