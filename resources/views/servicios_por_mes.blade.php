<?php
    
    use App\Http\Requests;
    use App\Compras;
    $registros = Compras::whereRaw("YEAR(fecha) = ? AND MONTH(fecha) = ?", [date("Y"), Request::input("mes")])->with("empleado")->orderBy('id_empleado', 'desc')->get();
?>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <meta charset="utf-8"/>
</head>
<style>
    table.restaurantes{
        text-align: left;
        border-collapse: collapse;
    }
    table.restaurantes th{
        text-align: left;
        text-transform: uppercase;
        font-size: 10px;
        color: #6B6B6B;
        background: #FAFAFA;
        border-bottom: 1px solid #F0F0F0;
    }
    table.restaurantes td {
        padding: 5px;
        color: #666666;
        border-bottom: 1px solid #F0F0F0;
    }
    td.rojo{
        color: #E91E63 !important;
    }
</style>

<table class="restaurantes">
    <thead>
        <tr>        
            <th>Fecha</th>
            <th>Codigo de Empleado</th>
            <th>Nombre Completo</th>
            <th>Tipo Vehiculo</th>
            <th>Servicio</th>
            <th>Extras</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $item)
        <tr>        
            <td>{{$item->fecha}}</td>
            <td>{{$item->empleado->codigo_empleado}}</td>
            <td>{{$item->empleado->primer_nombre." ".$item->empleado->segundo_nombre." ".$item->empleado->primer_apellido." ".$item->empleado->segundo_apellido}}</td>
            <td>{{$item->detalle["tipo_vehiculo"]["nombre"]}}</td>
            <td>{{$item->detalle["servicio"]["nombre"]}}</td>
            <td>@foreach($item->detalle["extras"] as $extra) {{$extra["nombre"]}}<br/> @endforeach</td>
            <td class="rojo">{{$item->total}}</td>
        </tr>
        @endforeach
    </tbody>
</table>