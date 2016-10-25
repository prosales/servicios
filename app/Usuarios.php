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
    	'remember_token',
        'estado',
    	'email',
    	'idpuesto',
    	'idrol'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

}