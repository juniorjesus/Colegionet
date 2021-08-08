<title>Reporte Inscritos <?= $periodo->periodo_id != null ? $periodo->descripcion : '' ?></title>
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
	tr:last-child td,thead th{
		background-color: #5bc0de;
	}
</style>
<?php 
	$this->load->view('headerPDF')
?>
<div id='footer'>

</div>
<div class="text-center">
	<h3>Reporte inscritos.</h3>
	<p><b>Periodo academico: </b><?= $periodo->periodo_id != null ? $periodo->anio_inicio." - ".$periodo->anio_fin : 'periodo no encontrado'?></p>
	<br><br>
</div>
<div>
	<?php 
		$template = array(
			'table_open' => '<table class="table table-striped table-condensed table-hover">',
			'thead_open' => '<thead class="thead-header">'
		);
		$this->table->set_template($template);
		$this->table->set_empty('<i>Sin información</i>');

		$columns[0] = array(
			array('data' => 'Grado','width' => '35%'),
			array('data' => 'Turno','width' => '15%'),
			array('data' => 'Profesor','width' => '45%'),
			array('data' => 'Cant.','width' => '5%')
		);
		foreach ($datos as $key => $value) {
			$columns[] = array(
				array('data' => $value['grado']),
				array('data' => $value['turno']),
				array('data' => $value['profesor']),
				array('data' => $value['cant'],'style' => 'text-align: right;'),
			);
		}
		$columns[] = array(
			array('data' => 'Total','colspan' => 3,'style' => 'text-align: right;'),
			array('data' => $total,'style' => 'text-align: right;')
		);
		echo $this->table->generate($columns);
	?>
</div>
<?php 
	foreach ($datosInscritos as $key => $value) {
		?>
		<hr style="page-break-after: always;">
		<h3><b>Grado:</b> <?= $value['grado'] ?></h3>
		<p><b>Numero: </b> <?= $value['numero'] ?>. <b>Seccion: </b> <?= $value['seccion'] ?>.</p>
		<p><b>Turno:</b> <?= $value['turno'] ?>. <b>Profesor:</b> <?= $value['profesor'] != '' ? $value['profesor'] : '<i>Sin información</i>' ?></p>
		<?php
		unset($columns);
		$columns[0] = array(
			array('data' => 'Num.','width' => '5%'),
			array('data' => 'Nombres')
		);
		
		$inscritos = ordenarArr($value['inscritos'],'nombres');
		
		foreach ($inscritos as $key2 => $value2) {
			$columns[] = array(
				array('data' => $key2+1),
				array('data' => $value2['nombres'])
			);
		}
		$total = count($value['inscritos']);
		$columns[] = array(
			array('data' => 'Total: '.$total,'colspan' => 2,'style' => 'text-align: right;')
		);
		echo $this->table->generate($columns);
	}
?>
<hr>