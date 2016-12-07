<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiposUsuarios extends Model
{
	protected $table = 'tipos_usuarios';
    protected $fillable = [
        'nombre'
    ];


}
