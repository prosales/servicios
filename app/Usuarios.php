<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuarios extends Authenticatable 
{
	protected $table = 'usuarios';
    protected $fillable = [
    	'nombre', 
    	'usuario', 
    	'password', 
    	'email',
        'estado',
    	'id_tipo_usuario',
    	'remember_token'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    public function tipo_usuario()
    {
        return $this->hasOne("App\TiposUsuarios", "id", "id_tipo_usuario");
    }

}