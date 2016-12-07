<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Compras;
use App\ServiciosEmpleados;
use DB;
use Exception;
use Auth;

class ComprasController extends Controller
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
            $this->records = Compras::all();
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
                
                $registro = Compras::create(
                [
                    "codigo" => $request->input("codigo"),
                    "id_empleado" => $request->input("id_empleado"),
                    "cantidad" => $request->input("cantidad"),
                    "detalle" => $request->input("detalle"),
                    "total" => $request->input("total")
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
            $registro = Compras::find($id);
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
                
                $registro = Compras::find($id);

                if(!$registro)
                    throw new Exception("El registro no existe");
                else
                {
                    $registro->codigo = $request->input("codigo", $registro->codigo);
                    $registro->id_empleado = $request->input("id_empleado", $registro->id_empleado);
                    $registro->cantidad = $request->input("cantidad", $registro->cantidad);
                    $registro->detalle = $request->input("detalle", $registro->detalle);
                    $registro->total = $request->input("total", $registro->total);

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
            $this->records = Compras::destroy($id);
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
