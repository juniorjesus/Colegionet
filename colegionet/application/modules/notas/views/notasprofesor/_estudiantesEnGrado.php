<div class="table-responsive">
	<h4>Estudiantes</h4>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Matricula','width' => '40%'),
		array('data' => 'Identificacion','width' => '20%'),
		array('data' => 'Apellidos','width' => '20%'),
		array('data' => 'Nombres','width' => '10%'),
		array('data' => '','width' => '10%'),
	);
	if (count($estudiantes)>0){
		foreach ($estudiantes as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'matricula','identificacion','apellidos','nombres'
					)
				)
			);
			$links = '';
			$url = '';
			if ($ver != NULL && $value['evaluacion_id'] != NULL){
				$url = 'notas/notasprofesor/obtenerBoletinPDF/'.$value['evaluacion_id'];
				$links .= anchor($url,'<span class="fa fa-file"></span>',
					array('style' => 'color: inherit;padding: 2px;','title' => 'Boletin', 'target' => '_blank')
				);
			} elseif ($value['evaluacion_id'] != NULL) {
				$url = 'notas/notasprofesor/cargarEvaluacion/'.$value['evaluacion_id'];
				$links .= anchor($url,'<span class="fa fa-upload"></span>',
					array('style' => 'color: inherit;padding: 2px;','title' => 'Cargar Notas')
				);
				//decargar boletin
				$url = 'notas/notasprofesor/obtenerBoletinPDF/'.$value['evaluacion_id'];
				$links .= anchor($url,'<span class="fa fa-file"></span>',
					array('style' => 'color: inherit;padding: 2px;','title' => 'Boletin', 'target' => '_blank')
				);
			} elseif ($proyecto != NULL && $ver == NULL) {
				$url = 'notas/notasprofesor/crearEvaluacion/'.$value['inscripcion_id'].'/'.$proyecto->proyecto_id;
				$links .= anchor($url,'<span class="fa fa-upload"></span>',
					array('style' => 'color: inherit;padding: 2px;','title' => 'Cargar Notas')
				);
			}

			$columns[] = array_merge($arr,array(array('data' =>$links, 'align' => 'center')));
		}
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => count($columns[0]),'data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>