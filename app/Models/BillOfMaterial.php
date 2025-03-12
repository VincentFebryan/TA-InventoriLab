<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfMaterial extends Model
{
    use HasFactory;

    protected $table = 'bill_of_materials';
    
    protected $fillable = [
        'kode_bom',
        'nama_material',
        'jumlah',
        'satuan',
        'harga_per_unit',
        'total_harga'
    ];
}

