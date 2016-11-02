<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Flows;
use App\FlowsDetalles;
use App\FlowsArchivos;
use Carbon\Carbon;

class FlowsController extends Controller
{
    public $message = "";
    public $result = false;
    public $records = array();
    public $statusCode =    200;

    public function index()
    {
        try
        {
            $this->records     =   Flows::with("detalle", "files")->get();
            $this->message     =   "Consulta exitosa";
            $this->result      =   true;
            $this->statusCode  =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se consulto';
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

    public function store(Request $request)
    {
        try
        {
            $registro   =   DB::transaction(function() use ($request)
            {
                $registro = Flows::create
                ([
                    'descripcion'           =>  $request->input( 'descripcion' ),
                    'fecha_finalizacion'    =>  NULL,
                    'pasos'                 =>  $request->input( 'pasos' ),
                    'archivos'              =>  $request->input( 'archivos' ),
                    'aprobador'             =>  "1"
                ]);

                if( !$registro )
                    throw new \Exception('Registro no se creo');

                $archivos = json_decode($request->input("files"), true);
                foreach($archivos as $item)
                {
                    $registro_archivo = FlowsArchivos::create
                    ([
                        'idflow'    => $registro->id,
                        'archivo'   => $item
                    ]);

                    if( !$registro_archivo )
                        throw new \Exception('Registro no se creo');
                }

                $i = 1;
                $detalle = json_decode($request->input("detalle"), true);
                foreach($detalle as $item)
                {
                    $registro_detalle = FlowsDetalles::create
                    ([
                        'idflow'            => $registro->id,
                        'proceso'           => $item["proceso"],
                        'documento'         => $item["documento"],
                        'usuario_aprobo'    => $item["idusuario"],
                        'comentario'        => '',
                        'aprobado'          => '0',
                        'fecha_aprobo'      => NULL,
                        'orden'             => $i
                    ]);

                    $i++;

                    if( !$registro_detalle )
                        throw new \Exception('Registro no se creo');
                }

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
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se creo';
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
            $registro           =   Flows::find($id);
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

    public function update($id, Request $request)
    {
        try
        {
            $registro   =   DB::transaction(function() use ($request,$id)
            {
                $record                            =   Flows::find($id);
                $record->descripcion               =   $request->input( 'descripcion', $record->descripcion );
                $record->fecha_finalizacion        =   $request->input( 'fecha_finalizacion', $record->fecha_finalizacion );
                $record->pasos                     =   $request->input( 'pasos', $record->pasos );
                $record->archivos                  =   $request->input( 'archivos', $record->archivos );
                
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
            $this->result       =   Flows::destroy($id);
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

    public function flows_pendientes(Request $request)
    {
        try
        {
            $registros = FlowsDetalles::select(DB::raw("flows_detalles.*"))
                                ->leftJoin("flows", "flows.id", "=", "flows_detalles.idflow")
                                ->whereRaw("flows.fecha_finalizacion is null AND flows.aprobador = flows_detalles.orden AND flows_detalles.usuario_aprobo = ?", [$request->input("idusuario")])
                                ->with("flow", "files")
                                ->get();
                                

            $this->records     =   $registros;
            $this->message     =   "Consulta exitosa";
            $this->result      =   true;
            $this->statusCode  =   200;
        }
        catch (\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registros no se consultaron';
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

    public function aprobar_flow(Request $request)
    {
        try
        {
            $registro   =   DB::transaction(function() use ($request)
            {
                $record                            =   FlowsDetalles::find($request->input("id"));
                $record->comentario                =   $request->input( 'comentario', '' );
                $record->aprobado                  =   $request->input( 'aprobado', $record->aprobado );
                $record->fecha_aprobo              =   Carbon::now("America/Guatemala")->toDateTimeString();
                $record->save();

                if($record->aprobado == 1)
                    $orden = $record->orden + 1;
                else if($record->aprobado == 2)
                    $orden = 0;

                $flow = Flows::find($record->idflow);
                
                if($flow->pasos < $orden)
                {
                    $flow->fecha_finalizacion = Carbon::now("America/Guatemala")->toDateTimeString();
                }
                else if($orden > 0)
                {
                    $flow->aprobador = $orden;
                }
                else
                {
                    $flow->aprobador = "1";
                    FlowsDetalles::where("idflow", $flow->id)->update(["comentario"=>"", "aprobado"=>"0", "fecha_aprobo"=>NULL]);
                }

                $flow->save(); 

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
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registros no se consultaron';
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

    public function upload(Request $request)
    {
        try
        {
            $archivo = $request->file("file");
            if($archivo)
            {
                $nombre_archivo = str_random(12) . "." . $archivo->getClientOriginalExtension();
                $destino = public_path() . "/files/";
                $subio = $archivo->move( $destino, $nombre_archivo );
                if($subio)
                {
                    $this->message = "Subida exitosa";
                    $this->result = true;
                    $this->records = "files/" . $nombre_archivo;
                }
                else
                    throw new \Exception("Ocurrio un problema al subir tu archivo");
            }
            else
                throw new \Exception("No viene el archivo");
        }
        catch(\Exception $e)
        {
            $this->statusCode   =   200;
            $this->message      =   env('APP_DEBUG')?$e->getMessage():'Registro no se creo';
            $this->result       =   false;
        }
        finally
        {
            $response = 
            [
                'message'   =>  $this->message,
                'result'    =>  $this->result,
                'url'   =>  $this->records
            ];
            
            return response()->json($response, $this->statusCode);
        }
    }

}
