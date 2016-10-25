<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flows extends Model
{
	protected $table = 'flows';
    protected $fillable = [
        'descripcion', 
        'fecha_finalizacion',
        'pasos',
        'archivos',
    ];


}
