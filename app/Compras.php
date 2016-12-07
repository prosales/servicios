<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiposUsuarios extends Model
{
	protected $table = 'tipos_usuarios';
    protected $fillable = [
        'codigo',
        'id_empleado',
        'cantidad',
        'detalle',
        'total'
    ];

    public function empleado()
    {
    	return $this->hasOne("App\Empleados", "id", "id_empleado");
    }
}
