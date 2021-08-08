<div class="table-responsive col-md-8 inline">
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Datos del periodo','colspan' => 2),
	);
	if ($periodo != NULL) {
		$columns[] = array(
			array('data' => "<b>".$this->periodo->fields()['periodo_id']."</b>", 'width' => '15%'),
			array('data' => $periodo->periodo_id),
		);
		$columns[] = array(
			array('data' => "<b>".$this->periodo->fields()['descripcion']."</b>", 'width' => '15%'),
			array('data' => $periodo->descripcion),
		);
		$columns[] = array(
			array('data' => "<b>".$this->periodo->fields()['fecha_inicio']."</b>", 'width' => '15%'),
			array('data' => date_format(date_create($periodo->fecha_inicio),'d-m-Y')),
		);
		$columns[] = array(
			array('data' => "<b>".$this->periodo->fields()['fecha_fin']."</b>", 'width' => '15%'),
			array('data' => date_format(date_create($periodo->fecha_fin),'d-m-Y')),
		);
		$columns[] = array(
			array('data' => "<b>".$this->periodo->fields()['fecha_inicio_inscripcion']."</b>", 'width' => '15%'),
			array('data' => date_format(date_create($periodo->fecha_inicio_inscripcion),'d-m-Y')),
		);
		$columns[] = array(
			array('data' => "<b>".$this->periodo->fields()['fecha_fin_inscripcion']."</b>", 'width' => '15%'),
			array('data' => date_format(date_create($periodo->fecha_fin_inscripcion),'d-m-Y')),
		);
		$columns[] = array(
			array('data' => "<b>".$this->estado_periodo->fields()['estado']."</b>", 'width' => '15%'),
			array('data' => $periodo->estado),
		);
	} else {
		$columns[] = array(array('colspan' => '2','data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>