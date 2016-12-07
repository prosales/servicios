<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
	protected $table = 'empleados';
    protected $fillable = [
        'codigo_empleado',
        'password',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'telefono',
        'email',
        'estado',
        'id_usuario_activo'
    ];

    public function usuario_activo()
    {
    	return $this->hasOne("App\Usuarios", "id", "id_usuario_activo");
    }
}
