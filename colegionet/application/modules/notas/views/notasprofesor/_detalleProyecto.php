<div class="table-responsive col-md-6 inline no-padding">
	<h4>Proyecto</h4>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Datos del proyecto','colspan' => 2),
	);
	if ($proyecto != NULL){
		$columns[] = array(
			array('data' => '<b>Nombre del proyecto</b>', 'width' => '20%'),
			array('data' => $proyecto->proyecto)
		);
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => 2,'data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>