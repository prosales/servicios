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
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods : GET,POST,PUT,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

Route::get('/', function () {
    return Redirect::to("src");
});

Route::group(["prefix" => "ws"], function()
{
	Route::post( "login",					"UsuariosController@login" );
	Route::post( "upload",					"FlowsController@upload" );
	Route::get( "usuarios_puestos/{id}",	"UsuariosController@usuarios_puestos" );
	Route::get( "flows_pendientes", 		"FlowsController@flows_pendientes" );
	Route::post( "aprobar_flow",			"FlowsController@aprobar_flow" );

	Route::resource( "usuarios",		"UsuariosController" );
	Route::resource( "puestos",			"PuestosController" );
	Route::resource( "plantillas",		"PlantillasController" );
	Route::resource( "flows",			"FlowsController" );
});
