<title>Boletin <?= $estudiante->estudiante_id.$lapso->lapso_id.$evaluacion->proyecto_id." ".date('d-m-Y h:i:s') ?></title>
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
<div id='footer'>
	<table>
		<tr>
			<td width="10%"></td>
			<td align="center"><p style="border-bottom: 1px solid black"></p><br><b>DOCENTE</b></td>
			<td width="10%"></td>
			<td align="center" width="20%"><p style="border-bottom: 1px solid black"></p><br><b>REPRESENTANTE</b></td>
			<td width="10%"></td>
			<td align="center"><p style="border-bottom: 1px solid black"></p><br><b>DIRECTOR</b></td>
			<td width="10%"></td>
		</tr>
	</table>
</div>
<div>
	<p align="center"><b>Boletin Informativo</b><br><b><?= $lapso->numero ?> LAPSO</b></p>
	<p><b>Nombre y apellido: </b> <u> <?= $estudiante->nombres." ".$estudiante->apellidos ?> </u></p>
	<p><b>Cedula escolar y/o Cedula de Identidad: </b> <u> <?= $estudiante->identificacion != NULL ? $estudiante->identificacion : $estudiante->matricula ?> </u></p>
	<p><b>Representante: </b> <u> <?= $evaluacion->nombres." ".$evaluacion->apellidos ?> </u> <b>C.I.: </b> <u> <?= $evaluacion->identificacion ?> </u></p>
	<p><b>Docente: </b> <u> <?= $this->session->persona->nombres." ".$this->session->persona->apellidos ?> </u></p>
	<p><b>Grado: </b><u> <?= $grado->numero ?> </u> <b>Seccion: </b><u> <?= $grado->seccion ?> </u> <b>Periodo: </b> <u> <?= $periodo->anio_inicio." - ".$periodo->anio_fin ?> </u></p>
	<p><b>Nombre del proyecto: </b> <u> <?= $evaluacion->proyecto ?> </u></p>
</div>
<hr>
<div class="text-center">
	<h4>DESCRIPCION DEL PROCESO EDUCATIVO</h4>
</div>
<br><br>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => '','width' => '5%'),
		array('data' => 'Indicadores evaluados'),
		array('data' => 'Items','width' => '10%')
	);
	$num = 1;
	if (count($detallesEvaluacion) > 0){
		foreach ($detallesEvaluacion as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'indicador','calificacion'
					)
				)
			);
			$columns[] = array_merge(array(array('data' =>$num++, 'align' => 'center')),$arr);
		}
	}
	for ($i = $num; $i <= 12 ; $i++) { 
		$columns[] = array(
			array('data' => $i,'align' => 'center'),
			array('data' => ''),
			array('data' => '--'),
		);
	}
	
	echo $this->table->generate($columns);
?>
<h4 class="text-center" style="margin: 0">LEYENDA: E (EXCELENTE) B (BUENO) R (REGULAR) M (MEJORABLE)</h4>
<div style="page-break-inside: avoid">
	<div class="text-center">
		<h5>RECOMENDACIONES Y OBSERVACIONES</h5>
	</div><br><br>
	<p><?= $evaluacion->observaciones != '' ? nl2br($evaluacion->observaciones) : '<i>Sin informacion</i>' ?></p>
	<br>
	<p><b>Inasistencias: </b> <?= $evaluacion->inasistencias ?></p>
</div>