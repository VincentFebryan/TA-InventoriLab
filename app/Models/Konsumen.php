<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsumen Extends model
{
    protected $table = 'konsumens';

    protected $fillable = [
        'nama',
        'alamat',
        'kota',
        'telepon',
        'email',
        'status',
    ];
}