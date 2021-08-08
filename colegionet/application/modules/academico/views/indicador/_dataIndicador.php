<div class="table-responsive col-md-8 inline">
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Datos del indicador','colspan' => 2),
	);
	if ($indicador->indicador_id != NULL) {
		$columns[] = array(
			array('data' => "<b>".$this->indicador->fields()['indicador_id']."</b>", 'width' => '15%'),
			array('data' => $indicador->indicador_id),
		);
		$columns[] = array(
			array('data' => "<b>".$this->indicador->fields()['indicador']."</b>", 'width' => '15%'),
			array('data' => $indicador->indicador),
		);
	} else {
		$columns[] = array(array('colspan' => '2','data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>