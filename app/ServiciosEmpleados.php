<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiciosEmpleados extends Model
{
	protected $table = 'servicios_empleados';
    protected $fillable = [
        'id_compra',
        'fecha_ingreso',
        'inicio_lavado',
        'fin_lavado',
        'horas',
        'id_usuario_atendio'
    ];

    public function servicio()
    {
    	return $this->hasOne("App\Compras", "id", "id_compra")->with("empleado");
    }
}
