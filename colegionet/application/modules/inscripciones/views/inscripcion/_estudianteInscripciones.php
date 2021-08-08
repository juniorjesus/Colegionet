<div class="table-responsive" id='result'>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Periodo', 'width' => '40%'),
		array('data' => 'Grado', 'width' => '20%'),
		array('data' => 'Fecha','width' => '20%'),
		array('data' => '')
	);
	if (count($inscripciones)>0){
		foreach ($inscripciones as $key => $value) {
			$value['fecha'] = date_format(date_create($value['fecha']),'d-m-Y');
			$arr = array_intersect_key($value, array_flip(array(
						'descripcion','grado','fecha'
					)
				)
			);
			$links = '';
			$links .= anchor('notas/notasestudiante/lapsos/'.$value['inscripcion_id'],'<span class="fa fa-eye"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Ver detalle')
			);
			$links .= anchor('inscripciones/inscripcion/comprobanteInscripcionPDF/'.$value['inscripcion_id'],'<span class="fa fa-file"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Comprobante inscripcion','target' => '_blank')
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