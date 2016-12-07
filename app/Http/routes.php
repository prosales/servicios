<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/*header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods : GET,POST,PUT,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");*/

Route::get('/', function () {
    return Redirect::to("index.html");
});

Route::group(["prefix" => "api"], function()
{
	Route::any("/login",					"UsuariosController@login");
	Route::get("/tipos_usuarios",			"UsuariosController@tipos_usuarios");

	Route::resource("/usuarios",			"UsuariosController");
	Route::resource("/tipos_vehiculos",		"TiposVehiculosController");
	Route::resource("/servicios",			"ServiciosController");
	Route::resource("/extras",				"ExtrasController");
	Route::resource("/empleados",			"EmpleadosController");
	Route::resource("/compras",				"ComprasController");
});
