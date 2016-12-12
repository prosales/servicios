<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
	protected $table = 'compras';
    protected $fillable = [
        'codigo',
        'id_empleado',
        'cantidad',
        'detalle',
        'total',
        'fecha'
    ];

    protected $casts = [
        'detalle' => 'array'
    ];

    public function empleado()
    {
    	return $this->hasOne("App\Empleados", "id", "id_empleado");
    }
}
