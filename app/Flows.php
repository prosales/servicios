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
        'aprobador'
    ];

    public function detalle()
    {
    	return $this->hasMany("App\FlowsDetalles", "idflow", "id")->with("usuario_aprobador");
    }

    public function files()
    {
    	return $this->hasMany("App\FlowsArchivos", "idflow", "id");
    }
}
