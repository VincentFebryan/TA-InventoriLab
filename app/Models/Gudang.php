<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $table = 'gudangs';
    
    protected $fillable = [
        'kode_gudang',
        'nama',
        'alamat_lengkap',
        'jenis_gudang',
        'keterangan',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'jenis_barang_id');
    }
}
