<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Empleados;
use DB;
use Exception;
use Auth;

class EmpleadosController extends Controller
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
            $this->records = Empleados::all();
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
                
                $registro = Empleados::where("codigo_empleado", $request->input("codigo_empleado"))->first();
                if($registro)
                    throw new Exception("El codigo de empleado ya existe");

                $registro = Empleados::create(
                [
                    "codigo_empleado" => $request->input("codigo_empleado"),
                    "password" => "",
                    "primer_nombre" => $request->input("primer_nombre"),
                    "segundo_nombre" => $request->input("segundo_nombre", ""),
                    "primer_apellido" => $request->input("primer_apellido"),
                    "segundo_apellido" => $request->input("segundo_apellido", ""),
                    "telefono" => $request->input("telefono"),
                    "email" => $request->input("email"),
                    "estado" => "0",
                    "id_usuario_activo" => "0"
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
            $registro = Empleados::find($id);
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
                
                $registro = Empleados::find($id);

                if(!$registro)
                    throw new Exception("El registro no existe");
                else
                {
                    $registro->codigo_empleado = $request->input("codigo_empleado", $registro->codigo_empleado);
                    $registro->primer_nombre = $request->input("primer_nombre", $registro->primer_nombre);
                    $registro->segundo_nombre = $request->input("segundo_nombre", $registro->segundo_nombre);
                    $registro->primer_apellido = $request->input("primer_apellido", $registro->primer_apellido);
                    $registro->segundo_apellido = $request->input("segundo_apellido", $registro->segundo_apellido);
                    $registro->telefono = $request->input("telefono", $registro->telefono);
                    $registro->email = $request->input("email", $registro->email);
                    $registro->estado = $request->input("estado", $registro->estado);
                    $registro->id_usuario_activo = $request->input("id_usuario_activo", $registro->id_usuario_activo);

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
            $this->records = Empleados::destroy($id);
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

    public function activar($id)
    {
        try
        {
            $actualizarRegistro = DB::transaction( function() use ($id){
                
                $registro = Empleados::find($id);

                if(!$registro)
                    throw new Exception("El registro no existe");
                else
                {
                    $pass = str_random(10);
                    $registro->password = bcrypt( $pass );
                    $registro->estado = 1;

                    $registro->save();

                    $data = [
                        "nombre" => $registro->primer_nombre." ".$registro->primer_apellido,
                        "codigo" => $registro->codigo_empleado,
                        "password" => $pass
                    ];

                    \Mail::send('activacion_cliente', $data, function ($message) use ($registro)
                    {
                        $message->subject("Activación de Cuenta");

                        $message->from('burbujas@researchmobile.co', 'Mr. Burbujas');

                        $message->to($registro->email);

                    });

                    return $registro;  
                }
            });

            $this->message = "Registro activado correctamente";
            $this->result = true;
            $this->records = $actualizarRegistro;
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

    public function cambiar_password(Request $request)
    {
        try
        {
            $actualizarRegistro = DB::transaction( function() use ($request){
                
                $registro = Empleados::find($request->input("id"));

                if(!$registro)
                    throw new Exception("El registro no existe");
                else
                {
                    if( \Hash::check( $request->input("pass_actual"), $registro->password ) )
                    {
                        $registro->password = bcrypt( $request->input("password") );

                        $registro->save();

                        return $registro; 
                    }
                    else
                    {
                        throw new \Exception("Contraseña actual inválida");
                    } 
                }
            });

            $this->message = "Contraseña actualizada correctamente";
            $this->result = true;
            $this->records = $actualizarRegistro;
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
