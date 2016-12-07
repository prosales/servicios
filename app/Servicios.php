<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{
	protected $table = 'servicios';
    protected $fillable = [
        'nombre',
        'precios',
        'cantidad',
        'es_combo'
    ];

    protected $casts = [
        'precios' => 'array'
    ];
}
