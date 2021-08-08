<div class="table-responsive col-md-8 inline">
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Datos del estudiante','colspan' => 2),
	);
	if ($estudiante != NULL) {
		$columns[] = array(
			array('data' => "<b>".$this->estudiante->fields()['matricula']."</b>", 'width' => '15%'),
			array('data' => $estudiante->matricula),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['identificacion']."</b>", 'width' => '15%'),
			array('data' => $estudiante->identificacion),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['nombres']."</b>", 'width' => '15%'),
			array('data' => $estudiante->nombres),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['apellidos']."</b>", 'width' => '15%'),
			array('data' => $estudiante->apellidos),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['sexo']."</b>", 'width' => '15%'),
			array('data' => $estudiante->sexo),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['fecha_nac']."</b>", 'width' => '15%'),
			array('data' => date_format(date_create($estudiante->fecha_nac),'d-m-Y')),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['telefono_hab']."</b>", 'width' => '15%'),
			array('data' => $estudiante->telefono_hab),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['telefono_mov']."</b>", 'width' => '15%'),
			array('data' => $estudiante->telefono_mov),
		);
	} else {
		$columns[] = array(array('colspan' => '2','data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>