<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggunaanBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_id',
        'barang_id',
        'jumlah_digunakan',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}