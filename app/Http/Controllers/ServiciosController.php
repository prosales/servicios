<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Servicios;
use App\Extras;
use App\TiposVehiculos;
use DB;
use Exception;
use Auth;

class ServiciosController extends Controller
{

    public $message = "Oops! Algo salio mal.";
    public $result = false;
    public $records = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {
            $this->message = "Consulta exitosa";
            $this->result = true;
            $this->records = Servicios::all();
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Error al consultar registros";
            $this->result = false;
        }
        finally
        {
            $response = [
                "message" => $this->message,
                "result" => $this->result,
                "records" => $this->records
            ];

            return response()->json($response);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $nuevoRegistro = DB::transaction( function() use($request){
                
                $registro = Servicios::create(
                [
                    "nombre" => $request->input("nombre"),
                    "precios" => json_decode($request->input("precios")),
                    "cantidad" => $request->input("cantidad"),
                    "es_combo" => $request->input("es_combo")
                ]);

                if(!$registro)
                    throw new Exception("Ocurrio un problema al crear el registro");
                else
                    return $registro;
            });

            $this->message = "Registro creado";
            $this->result = true;
            $this->records = $nuevoRegistro;
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Error al crear registro";
            $this->result = false;
        }
        finally
        {
            $response = [
                "message" => $this->message,
                "result" => $this->result,
                "records" => $this->records
            ];

            return response()->json($response);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       try
        {
            $registro = Servicios::find($id);
            if($registro)
            {
                $this->message = "Consulta exitosa";
                $this->result = true;
                $this->records = $registro;
            }
            else
            {
                $this->message = "El registro no existe";
                $this->result = false;
            }
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Error al consultar registro";
            $this->result = false;
        }
        finally
        {
            $response = [
                "message" => $this->message,
                "result" => $this->result,
                "records" => $this->records
            ];

            return response()->json($response);
        } 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        {
            $actualizarRegistro = DB::transaction( function() use($request, $id){
                
                $registro = Servicios::find($id);

                if(!$registro)
                    throw new Exception("El registro no existe");
                else
                {
                    $registro->nombre = $request->input("nombre", $registro->nombre);
                    $registro->precios = json_decode($request->input("precios"));
                    $registro->cantidad = $request->input("cantidad", $registro->cantidad);
                    $registro->es_combo = $request->input("es_combo", $registro->es_combo);

                    $registro->save();

                    return $registro;  
                }
            });

            $this->message = "Registro actualizado";
            $this->result = true;
            $this->records = $actualizarRegistro;
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Error al actualizar registro";
            $this->result = false;
        }
        finally
        {
            $response = [
                "message" => $this->message,
                "result" => $this->result,
                "records" => $this->records
            ];

            return response()->json($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $this->message = "Registro eliminado";
            $this->result = true;
            $this->records = Servicios::destroy($id);
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Error al eliminar registro";
            $this->result = false;
        }
        finally
        {
            $response = [
                "message" => $this->message,
                "result" => $this->result,
                "records" => $this->records
            ];

            return response()->json($response);
        }
    }

    public function servicios_clientes()
    {
        try
        {
            $tipos_vehiculos = TiposVehiculos::all();
            $servicios = Servicios::all();
            $extras = Extras::all();
            $data = [];
            $data2 = [];
            $registros = [];
            
            foreach($servicios as $item)
            {
                foreach($item->precios as $tipo)
                {
                    if(!isset($data[$tipo["nombre"]]))
                    {
                        $data[$tipo["nombre"]] = [];
                        if($tipo["precio"]!=0)
                            array_push($data[$tipo["nombre"]], array("id"=>$item->id, "nombre"=> $item->nombre, "precio"=> $tipo["precio"], "cantidad"=>$item->cantidad));
                    }
                    else
                    {
                        if($tipo["precio"]!=0)
                            array_push($data[$tipo["nombre"]], array("id"=>$item->id,"nombre"=> $item->nombre, "precio"=> $tipo["precio"], "cantidad"=>$item->cantidad));
                    }
                }
            }

            foreach($extras as $item)
            {
                foreach($item->precios as $tipo)
                {
                    if(!isset($data2[$tipo["nombre"]]))
                    {
                        $data2[$tipo["nombre"]] = [];
                        if($tipo["precio"]!=0)
                            array_push($data2[$tipo["nombre"]], array("id"=>$item->id, "nombre"=> $item->nombre, "precio"=> $tipo["precio"]));
                    }
                    else
                    {
                        if($tipo["precio"]!=0)
                            array_push($data2[$tipo["nombre"]], array("id"=>$item->id, "nombre"=> $item->nombre, "precio"=> $tipo["precio"]));
                    }
                }
            }

            foreach($tipos_vehiculos as $item)
            {
                $objeto = array("id"=>$item->id, "nombre"=> $item->nombre, "servicios"=>$data[$item->nombre], "extras"=> $data2[$item->nombre]);
                array_push($registros, $objeto);
            }

            $this->records = $registros;
            $this->message = "Registros consultados";
            $this->result = true;
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Error al eliminar registro";
            $this->result = false;
        }
        finally
        {
            $response = [
                "message" => $this->message,
                "result" => $this->result,
                "records" => $this->records
            ];

            return response()->json($response);
        }
    }

}
