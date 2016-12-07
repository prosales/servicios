<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiciosEmpleados extends Model
{
	protected $table = 'servicios_empleados';
    protected $fillable = [
        'id_compra',
        'id_empleado',
        'fecha_ingreso',
        'inicio_lavado',
        'fin_lavado',
        'horas',
        'detalle',
        'id_usuario_atendio'
    ];
}
