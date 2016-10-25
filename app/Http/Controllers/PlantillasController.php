<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Plantillas;

class PlantillasController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;

    public function index()
    {
        try
        {
            $this->records     =   Plantillas::with("creo","modifico")->get();
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
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records,
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
                $registro = Plantillas::create
                ([
                    'nombre'            =>  $request->input( 'nombre' ),
                    'descripcion'       =>  $request->input( 'descripcion' ),
                    'usuario_creo'      =>  $request->input( 'usuario_creo' ),
                    'usuario_modifico'  =>  $request->input( 'usuario_modifico' ),
                    'pasos'             =>  $request->input( 'pasos' ),
                    'detalle'           =>  $request->input( 'detalle' )
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
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records,
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

    public function show($id)
    {
        try
        {
            $registro           =   Plantillas::find($id);
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
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'records'   =>  $this->records,
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
                $record                            =   Plantillas::find($id);
                $record->nombre                    =   $request->input( 'nombre', $record->nombre );
                $record->descripcion               =   $request->input( 'descripcion', $record->descripcion );
                $record->usuario_creo              =   $request->input( 'usuario_creo', $record->usuario_creo );
                $record->usuario_modifico          =   $request->input( 'usuario_modifico', $record->usuario_modifico );
                $record->pasos                     =   $request->input( 'pasos', $record->pasos );
                $record->detalle                   =   $request->input( 'detalle', $record->detalle );
                
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
            $this->result       =   Plantillas::destroy($id);
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

}
