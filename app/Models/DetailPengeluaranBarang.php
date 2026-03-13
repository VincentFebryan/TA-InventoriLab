<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengeluaranBarang extends Model
{
    protected $table = 'detail_pengeluaran_barangs';

    protected $fillable = [
        'master_pengeluaran_barang_id',
        'barang_id',
        'jumlah_keluar',
        'harga',
        'total_harga',

    ];

    public function PengeluaranBarang()
    {
        return $this->belongsTo(PengeluaranBarang::class, 'master_pengeluaran_barang_id');
    }
    public function barang()
    {
        return $this->belongsTo(barang::class, 'barang_id');
    }
    public function proyek()
    {
        return $this->belongsTo(proyek::class, 'proyek_id');
    }
    public function supplier()
    {
        return $this->belongsTo(supplier::class, 'supplier_id');
    }
    public function konsumen()
    {
        return $this->belongsTo(konsumen::class, 'konsumen_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function jenispengeluaranbarang()
    {
        return $this->belongsTo(JenisPengeluaran::class, 'jenis_id');
    }
    public function masterPengeluaran()
    {
        return $this->belongsTo(PengeluaranBarang::class, 'pengeluaran_barang_id');
    }

}
