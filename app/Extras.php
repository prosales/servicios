<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extras extends Model
{
	protected $table = 'extras';
    protected $fillable = [
        'nombre',
        'precios'
    ];

    protected $casts = [
        'precios' => 'array'
    ];
}
