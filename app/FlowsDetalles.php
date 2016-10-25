<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowsDetalles extends Model
{
	protected $table = 'flows_detalles';
    protected $fillable = [
        'idflow', 
        'usuario_aprobo',
        'comentario',
        'aprobado',
        'fecha_aprobo',
    ];


}
