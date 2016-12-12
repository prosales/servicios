<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Compras;
use App\ServiciosEmpleados;
use App\Empleados;
use DB;
use Exception;
use Auth;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        try
        {
            if($request->input("id_empleado"))
            {
                $registros = Compras::where("id_empleado", $request->input("id_empleado"))->get();
            }
            else
            {
                $registros = Compras::all();
            }

            $this->message = "Consulta exitosa";
            $this->result = true;
            $this->records = $registros;
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
                    "codigo" => "",
                    "id_empleado" => $request->input("id_empleado"),
                    "cantidad" => $request->input("cantidad"),
                    "detalle" => json_decode($request->input("detalle")),
                    "total" => $request->input("total"),
                    "fecha" => Carbon::now("America/Guatemala")->toDateString()
                ]);

                if(!$registro)
                {
                    throw new Exception("Ocurrio un problema al crear el registro");
                }
                else
                {
                    $codigo = $registro->id.str_random(5);
                    $registro->codigo = $codigo;
                    $registro->save();
                    $empleado = Empleados::find($request->input("id_empleado"));

                    $data = [
                        "nombre" => $empleado->primer_nombre." ".$empleado->primer_apellido,
                        "codigo" => $codigo
                    ];

                    \Mail::send('servicio', $data, function ($message) use ($empleado)
                    {
                        $message->subject("Compra de Servicio");

                        $message->from('burbujas@researchmobile.co', 'Mr. Burbujas');

                        $message->to($empleado->email);

                    });

                    return $registro;
                }
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
    
    public function consultar_servicio(Request $request)
    {
        try
        {
            $registro = Compras::where("codigo", $request->input("codigo"))->first();
            if($registro)
            {
                if($registro->cantidad > 0)
                {
                    $registro->empleado;

                    $this->message = "Registro consultado";
                    $this->result = true;
                    $this->records = $registro;
                }
                else
                {
                    $this->message = "El codigo ya fue utilizado y no cuenta con cupo";
                    $this->result = false;
                }
            }
            else
            {
                $this->message = "El codigo ingresado no existe";
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

    public function agregar_servicio(Request $request)
    {
        try
        {
            $nuevoRegistro = DB::transaction( function() use($request){

                $registro = ServiciosEmpleados::create(
                [
                    "id_compra" => $request->input("id_compra"),
                    "fecha_ingreso" => Carbon::now("America/Guatemala")->toDateTimeString(),
                    "inicio_lavado" => NULL,
                    "fin_lavado" => NULL,
                    "horas" => "",
                    "id_usuario_atendio" => $request->input("id_usuario")
                ]);

                if(!$registro)
                {
                    throw new Exception("Ocurrio un problema al crear el registro");
                }
                else
                {
                    $compra = Compras::find($request->input("id_compra"));
                    $compra->cantidad -= 1;
                    $compra->save();

                    return $registro;
                }
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

    public function consultar_colas($id)
    {
        try
        {
            $colas = ServiciosEmpleados::with("servicio")->whereRaw("id_usuario_atendio = ? AND fin_lavado IS NULL", [$id])->get();

            $this->message = "Registros consultados";
            $this->result = false;
            $this->records = $colas;
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

    public function lavado(Request $request)
    {
        try
        {
            $registro = ServiciosEmpleados::find($request->input("id"));
            if($request->input("inicio") == "1")
                $registro->inicio_lavado = Carbon::now("America/Guatemala")->toDateTimeString();
            else
                $registro->fin_lavado = Carbon::now("America/Guatemala")->toDateTimeString();
            $registro->save();

            $this->message = "Registro actualizado";
            $this->result = true;
            $this->records = $registro;
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
}
