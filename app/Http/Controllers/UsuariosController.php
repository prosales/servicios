<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Usuarios;
use App\Empleados;
use App\TiposUsuarios;
use DB;
use Exception;
use Auth;

class UsuariosController extends Controller
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
            $this->records = Usuarios::with("tipo_usuario")->get();
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
                
                $registro = Usuarios::create(
                [
                    "nombre" => $request->input("nombre"),
                    "usuario" => $request->input("usuario"),
                    "password" => bcrypt($request->input("password")),
                    "email" => $request->input("email"),
                    "estado" => "1",
                    "id_tipo_usuario" => $request->input("id_tipo_usuario"),
                    "remember_token" => ""
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
            $registro = Usuarios::find($id);
            if($registro)
            {
                $registro->tipo_usuario;

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
                
                $registro = Usuarios::find($id);

                if(!$registro)
                    throw new Exception("El registro no existe");
                else
                {
                    $registro->nombre = $request->input("nombre", $registro->nombre);
                    $registro->usuario = $request->input("usuario", $registro->usuario);
                    if($request->input("password"))
                        $registro->password = bcrypt($request->input("password"));
                    $registro->id_tipo_usuario = $request->input("id_tipo_usuario", $registro->id_tipo_usuario);

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
            $this->records = Usuarios::destroy($id);
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

    public function tipos_usuarios()
    {
        try
        {
            $this->message = "Consulta exitosa";
            $this->result = true;
            $this->records = TiposUsuarios::all();
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

    public function login(Request $request)
    {
        try
        {
            $rules = array(
                'usuario'  => 'required', 
                'password' => 'required|min:3'
            );

            $validator = \Validator::make($request->all(), $rules);
            
            if ($validator->fails()) 
            {

                throw new Exception("Todos los campos son obligatorios");
            } 
            else
            {

                $userdata = array(
                    'usuario'     => $request->input('usuario'),
                    'password'    => $request->input('password')
                );

                
                if ( Auth::attempt($userdata) )
                {
                    $this->message = "Bienvenido";
                    $this->result = true;
                    $this->records = Auth::user();
                }
                else
                {        
                    $this->message = "Usuario o password incorrecto";
                    $this->result = false;
                }

            }
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Ocurrio un problema al iniciar sesion";
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

    public function login_cliente(Request $request)
    {
        try
        {
            $rules = array(
                'codigo_empleado'  => 'required', 
                'password' => 'required|min:3'
            );

            $validator = \Validator::make($request->all(), $rules);
            
            if ($validator->fails()) 
            {

                throw new Exception("Todos los campos son obligatorios");
            } 
            else
            {

                $userdata = array(
                    'codigo_empleado'   => $request->input('codigo_empleado'),
                    'password'          => $request->input('password')
                );

                $empleado = Empleados::where("codigo_empleado", $userdata["codigo_empleado"])->first();
                if ( $empleado )
                {
                    if( \Hash::check( $userdata["password"], $empleado->password ) )
                    {
                        $this->message = "Bienvenido";
                        $this->result = true;
                        $this->records = $empleado;
                    }
                    else
                    {
                        $this->message = "Password incorrecto";
                        $this->result = false;
                    }
                }
                else
                {        
                    $this->message = "Usuario incorrecto";
                    $this->result = false;
                }

            }
        }
        catch(\Exception $e)
        {
            $this->message = env("APP_DEBUG") ? $e->getMessage() : "Ocurrio un problema al iniciar sesion";
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
