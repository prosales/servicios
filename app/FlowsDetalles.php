<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowsDetalles extends Model
{
	protected $table = 'flows_detalles';
    protected $fillable = [
        'idflow', 
        'proceso',
        'documento',
        'usuario_aprobo',
        'comentario',
        'aprobado',
        'fecha_aprobo',
        'orden'
    ];

    public function flow()
    {
    	return $this->hasOne("App\Flows", "id", "idflow");
    }

    public function files()
    {
        return $this->hasMany("App\FlowsArchivos", "idflow", "idflow");
    }

    public function usuario_aprobador()
    {
        return $this->hasOne("App\Usuarios", "id", "usuario_aprobo");
    }
}
