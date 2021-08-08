<title>Inscripcion <?= $inscripcion->inscripcion_id." ".date('d-m-Y h:i:s') ?></title>
<style type="text/css"><?= file_get_contents('css/bootstrap/css/bootstrap.min.css') ?></style>
<style type="text/css"><?= file_get_contents('css/style.css') ?></style>
<style type="text/css">
	h1,h2,h3,h4,h5,p{
		color: black!important;
	}
	table{
		font-size: 10pt;
		margin-bottom: 0;
	}
	#header{
		position:fixed; 
		right:0; 
		left:0; 
		top:-100px;
		text-align: center;
	}
	#footer{
		position: fixed;
		bottom: -30px;
	}
	@page {
		border-bottom: 10px solid black;
		margin-top: 100px;
		margin-bottom: 100px;
	}
	p{
		margin: 0;
	}
</style>
<?php 
	$this->load->view('headerPDF')
?>
<div>
	<h3 class="text-center">Comprobante de inscripcion</h3>
	<br><br>
	<p class="text-justify">
		Se hace constar por medio de la presente la inscripcion del alumno <b><?= $inscripcion->apellidos." ".$inscripcion->nombres ?></b>,
		con la cedula estudiantil y/o cedula de identidad N° <b><?= $inscripcion->identificacion != NULL ? $inscripcion->identificacion : $inscripcion->matricula ?></b>,
		en el grado <b><?= $inscripcion->numero ?></b>, seccion <b><?= $inscripcion->seccion ?></b>, para el turno <b><?= $inscripcion->turno ?></b>,
		en el periodo academico <b><?= $inscripcion->anio_inicio." - ".$inscripcion->anio_fin ?></b>, a la fecha 
		<b><?= date_format(date_create($inscripcion->fecha),'d-m-Y') ?></b>
	</p>
	<br><br>
	<p>Fecha de impresion: <b><?= date('d-m-Y') ?></b></p>
</div>