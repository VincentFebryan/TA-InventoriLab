<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyeks';

    protected $fillable = [
        'no_otomatis',
        'kode_bom',
        'nama_bom',
        'keterangan',
        'status_bom',
    ];
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function konsumen()
    {
        return $this->belongsTo(Konsumen::class);
    }

    public function penggunaanBarang()
    {
        return $this->hasMany(PenggunaanBarang::class);
    }
    
    public function bom()
    {
        return $this->belongsTo(BillOfMaterial::class, 'bom_id');
    }
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

}

    