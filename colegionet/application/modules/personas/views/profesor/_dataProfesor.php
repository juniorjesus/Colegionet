<div class="table-responsive col-md-8 inline">
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Datos del profesor','colspan' => 2),
	);
	if ($profesor != NULL) {
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['identificacion']."</b>", 'width' => '15%'),
			array('data' => $profesor->identificacion),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['nombres']."</b>", 'width' => '15%'),
			array('data' => $profesor->nombres),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['apellidos']."</b>", 'width' => '15%'),
			array('data' => $profesor->apellidos),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['sexo']."</b>", 'width' => '15%'),
			array('data' => $profesor->sexo),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['fecha_nac']."</b>", 'width' => '15%'),
			array('data' => date_format(date_create($profesor->fecha_nac),'d-m-Y')),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['telefono_hab']."</b>", 'width' => '15%'),
			array('data' => $profesor->telefono_hab),
		);
		$columns[] = array(
			array('data' => "<b>".$this->persona->fields()['telefono_mov']."</b>", 'width' => '15%'),
			array('data' => $profesor->telefono_mov),
		);
	} else {
		$columns[] = array(array('colspan' => '2','data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>