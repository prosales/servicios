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
Route::get('/view/cliente', function () {
    return view("activacion_cliente");
});

Route::group(["prefix" => "api"], function()
{
	Route::any("/login",					"UsuariosController@login");
	Route::any("/login_cliente",			"UsuariosController@login_cliente");
	Route::get("/tipos_usuarios",			"UsuariosController@tipos_usuarios");
	Route::get("/empleados/activar/{id}",		"EmpleadosController@activar");
	Route::post("/empleados/cambiar_password",		"EmpleadosController@cambiar_password");
	Route::get("/servicios/clientes",				"ServiciosController@servicios_clientes");
	Route::get("/consultar_servicio",				"ComprasController@consultar_servicio");
	Route::get("/agregar_servicio",					"ComprasController@agregar_servicio");
	Route::get("/consultar_colas/{id}",				"ComprasController@consultar_colas");
	Route::get("/lavado",							"ComprasController@lavado");

	Route::resource("/usuarios",			"UsuariosController");
	Route::resource("/tipos_vehiculos",		"TiposVehiculosController");
	Route::resource("/servicios",			"ServiciosController");
	Route::resource("/extras",				"ExtrasController");
	Route::resource("/empleados",			"EmpleadosController");
	Route::resource("/compras",				"ComprasController");
});
