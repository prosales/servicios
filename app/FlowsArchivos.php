<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowsArchivos extends Model
{
	protected $table = 'flows_archivos';
    protected $fillable = [
        'idflow', 
        'archivo',
    ];


}
