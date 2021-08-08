<div class="table-responsive col-md-8 inline">
	<h4>Lapsos</h4>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->lapso->fields()['numero']),
		array('data' => $this->lapso->fields()['lapso_fecha_inicio']),
		array('data' => $this->lapso->fields()['lapso_fecha_fin']),
	);
	if (count($lapsos)>0){
		foreach ($lapsos as $key => $value) {
			$value['lapso_fecha_inicio'] = date_format(date_create($value['lapso_fecha_inicio']),'d-m-Y');
			$value['lapso_fecha_fin'] = date_format(date_create($value['lapso_fecha_fin']),'d-m-Y');
			$arr = array_intersect_key($value, array_flip(array(
						'numero','lapso_fecha_inicio','lapso_fecha_fin'
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