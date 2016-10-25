<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Puestos extends Model
{
	protected $table = 'puestos';
    protected $fillable = [
        'nombre', 
        'estado',
    ];


}
