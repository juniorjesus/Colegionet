<div class="table-responsive" id='result'>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Periodo','width' => '40%'),
		array('data' => 'Grado','width' => '20%'),
		array('data' => 'Turno','width' => '20%'),
		array('data' => 'Inscritos','width' => '10%'),
		array('data' => '','width' => '10%'),
	);
	if (count($asignacionProfesor)>0){
		foreach ($asignacionProfesor as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'descripcion','grado','turno','inscritos'
					)
				)
			);
			$links = '';
			$links .= anchor('notas/notasprofesor/lapsos/'.$value['grado_periodo_id'],'<span class="fa fa-eye"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Ver detalle')
			);
			$links .= anchor('notas/notasprofesor/obtenerListaPDF/'.$value['grado_periodo_id'],'<span class="fa fa-file"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Lista PDF', 'target' => '_blank')
			);
			$columns[] = array_merge($arr,array(array('data' =>$links, 'align' => 'center')));
		}
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => count($columns[0]),'data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>