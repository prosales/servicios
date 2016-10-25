<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plantillas extends Model
{
	protected $table = 'plantillas';
    protected $fillable = [
        'nombre', 
        'descripcion',
        'usuario_creo',
        'usuario_modifico',
        'pasos',
        'detalle',
    ];

    public function creo()
    {
    	return $this->hasOne("App\Usuarios", "id", "usuario_creo");
    }

    public function modifico()
    {
    	return $this->hasOne("App\Usuarios", "id", "usuario_modifico");
    }
}
