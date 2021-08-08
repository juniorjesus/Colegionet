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
	if ($grado->grado_id != NULL) {
		$columns[] = array(
			array('data' => "<b>".$this->grado->fields()['grado_id']."</b>", 'width' => '15%'),
			array('data' => $grado->grado_id),
		);
		$columns[] = array(
			array('data' => "<b>".$this->grado->fields()['grado']."</b>", 'width' => '15%'),
			array('data' => $grado->grado),
		);
		$columns[] = array(
			array('data' => "<b>".$this->grado->fields()['numero']."</b>", 'width' => '15%'),
			array('data' => $grado->numero),
		);
		$columns[] = array(
			array('data' => "<b>".$this->grado->fields()['seccion']."</b>", 'width' => '15%'),
			array('data' => $grado->seccion),
		);
	} else {
		$columns[] = array(array('colspan' => '2','data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>