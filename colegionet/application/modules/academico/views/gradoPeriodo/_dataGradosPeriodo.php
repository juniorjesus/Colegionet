<div class="table-responsive col-md-8 inline">
	<h4>Grados</h4>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->grado->fields()['grado']),
		array('data' => $this->grado->fields()['numero']),
		array('data' => $this->grado->fields()['seccion']),
		array('data' => $this->turno->fields()['turno']),
		array('data' => 'Profesor'),
	);
	if (count($gradosPeriodo)>0){
		foreach ($gradosPeriodo as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'grado','numero','seccion','turno','profesor'
					)
				)
			);

			$columns[] = $arr;
		}
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => count($columns[0]),'data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>