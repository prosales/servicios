<?php
	use Carbon\Carbon;
	$mes = [
		"1" => "Enero",
		"2" => "Febrero",
		"3" => "Marzo",
		"4" => "Abril",
		"5" => "Mayo",
		"6" => "Junio",
		"7" => "Julio",
		"8" => "Agosto",
		"9" => "Septiembre",
		"10" => "Octubre",
		"11" => "Noviembre",
		"12" => "Diciembre",
	];

	$fecha = Carbon::now("America/Guatemala")->toDateString();
?>
<style>
	body{
		font-size: 18px;
		font-family: sans-serif;
	}
	p.fecha{
		float: right;
	}
</style>
<body>
	<p class="fecha">Guatemala, {{date("d", strtotime($fecha))}} {{$mes[date("n", strtotime($fecha))]}} {{date("Y", strtotime($fecha))}}</p>
	<br/>
	<br/>
	<br/>
	<p>Yo, <b>{{$nombre}}</b>, con código de empleado <b>{{$codigo}}</b>, autorizo a Intcomex de Guatemala S.A. para que realice del pago de mi planilla los cargos correspondientes por los servicios del lavado de vehículo requeridos por mi persona por medio de la plataforma con mi usuario y contraseña registrado.</p>
	<br/>
	<br/>
	<br/>
	<center><p>__________________________________</p></center>
	<center><p>{{$nombre}}</p></center>
</body>