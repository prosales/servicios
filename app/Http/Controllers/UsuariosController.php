<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Usuarios;

class UsuariosController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;

    public function index()
    {
        try
        {
            $this->records     =   Usuarios::with("puesto")->get();
            $this->message     =   "Consulta exitosa";
            $this->result      =   true;
            $this->statusCode  =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result       =   false;
        }
        finally
        {
            $response = 
            [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function store(request $request)
    {
        try
        {
            $registro   =   DB::transaction(function() use ($request)
            {
                $registro = Usuarios::create
                ([
                    'nombre'        =>  $request->input( 'nombre' ),
                    'usuario'       =>  $request->input( 'usuario' ),
                    'password'      =>  bcrypt($request->input('password')),
                    'estado'        =>  $request->input( 'estado', '1' ),
                    'email'         =>  $request->input( 'email',''),
                    'idpuesto'      =>  $request->input( 'idpuesto' ),
                    'idrol'         =>  $request->input( 'idrol', '1' )
                ]);

                if( !$registro )
                    throw new \Exception('Registro no se creo');
                else
                    return $registro;
            });

            $this->records      =   $registro;
            $this->statusCode   =   200;
            $this->message      =   "Registro creado exitosamente";
            $this->result       =   true;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result       =   false;
        }
        finally
        {
            $response = 
            [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function show($id)
    {
        try
        {
            $registro           =   Usuarios::find($id);
            if($registro)
            {
                $this->records  =   $registro;
                $this->message      =   "Consulta exitosa";
                $this->result       =   true;
                $this->statusCode   =   200;
            }
            else
            {
                $this->message      =   "El registro no existe";
                $this->result       =   false;
                $this->statusCode   =   200;
            }
        }
        catch (\Exception $e)
        {
            $this->message      =   "Registro no existe";
            $this->result       =   false;
            $this->statusCode   =   200;
        }
        finally
        {
            $response = 
            [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function update($id, Request $request)//Actualizar un registro - Metodo PUT (Si) enviando el id y enviando datos
    {
        try
        {
            $registro   =   DB::transaction(function() use ($request,$id)
            {
                $record                            =   Usuarios::find($id);
                $record->nombre                    =   $request->input( 'nombre', $record->nombre );
                $record->usuario                   =   $request->input( 'usuario', $record->usuario );
                $record->estado                    =   $request->input( 'estado', $record->estado );
                $record->email                     =   $request->input( 'email', $record->email );
                $record->idpuesto                  =   $request->input( 'idpuesto', $record->idpuesto );
                $record->idrol                     =   $request->input( 'idrol', $record->idrol );
                if($request->input('password'))
                    $record->password              =   bcrypt($request->input( 'password'));
                
                $record->save();

                return $record;                                 
            });

            $this->records  =   $registro;
            $this->message  =   "Actualizacion exitosa";
            $this->result   =   true;
            $this->statusCode       =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se actualizo';
            $this->result       =   false;
        }
        finally
        {
            $response = 
            [
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function destroy($id)
    {
        try
        {
            $this->result       =   Usuarios::destroy($id);
            $this->message      =   "Eliminado correctamente";
            $this->statusCode   =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se elimino';
            $this->result       =   false;
        }
        finally
        {
            $response = 
            [
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function usuarios_puestos($id)
    {
        try
        {
            $this->records  =   Usuarios::where("idpuesto", $id)->get();
            $this->message      =   "Consulta exitosa";
            $this->result       =   true;
            $this->statusCode   =   200;
        }
        catch (\Exception $e)
        {
            $this->message      =   "No existen usuarios";
            $this->result       =   false;
            $this->statusCode   =   200;
        }
        finally
        {
            $response = 
            [
                'records'   =>  $this->records,
                'message'   =>  $this->message,
                'result'    =>  $this->result,
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function login(Request $request)
    {
        
        $rules = array(
            'usuario'    => 'required', 
            'password' => 'required|min:3'
        );

        
        $validator = \Validator::make($request->all(), $rules);
        $response = [];
        
        if ($validator->fails()) 
        {
            $response = 
            [
                'message'   =>  "Todos los campos son obligatorios",
                'result'    =>  false,
                'records'   => []
            ];
        } 
        else
        {

            $userdata = array(
                'usuario'     => $request->input('usuario'),
                'password'    => $request->input('password')
            );

            
            if ( Auth::attempt($userdata) )
            {
                $response = 
                [
                    'message'   =>  "Bienvenido",
                    'result'    =>  true,
                    'records'   => Auth::user()
                ];
            }
            else
            {        
                $response = 
                [
                    'message'   =>  "Credenciales incorrectas",
                    'result'    =>  false,
                    'records'   => []
                ];
            }

        }
        return response()->json($response, $this->statusCode);
    }

}
