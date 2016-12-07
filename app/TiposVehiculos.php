<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiposVehiculos extends Model
{
	protected $table = 'tipos_vehiculos';
    protected $fillable = [
        'nombre'
    ];


}
