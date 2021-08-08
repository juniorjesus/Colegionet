<div class="table-responsive" id='result'>
	<h4>Representantes del estudiante</h4>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	if (count($estudianteRepresentante) > 0) {
		foreach ($estudianteRepresentante as $key => $value) {
			echo '<div class="col-sm-6 inline">';
//			$linkDelete = anchor('personas/estudiante/borrarEstudianteRepresentante/'.$value->estudiante_representante_id,
//				'<span class="fa fa-close"></span> Eliminar',array(
//					'style' => 'color: inherit;padding: 2px;','title' => 'Lista PDF','onclick' => 'eliminarRepresentante(this)'));
			$columns[0] = array(
				array('data' => 'Datos','colspan' => '2'),
				//array('data' => $linkDelete,'class' => 'text-right'),
			);
			$columns[] = array(
				array('data' => "<b>Parentesco</b>", 'width' => '15%'),
				array('data' => $value->descripcion),
			);
			$columns[] = array(
				array('data' => "<b>".$this->persona->fields()['identificacion']."</b>", 'width' => '15%'),
				array('data' => $value->identificacion),
			);
			$columns[] = array(
				array('data' => "<b>".$this->persona->fields()['nombres']."</b>", 'width' => '15%'),
				array('data' => $value->nombres),
			);
			$columns[] = array(
				array('data' => "<b>".$this->persona->fields()['apellidos']."</b>", 'width' => '15%'),
				array('data' => $value->apellidos),
			);
			$columns[] = array(
				array('data' => "<b>".$this->persona->fields()['sexo']."</b>", 'width' => '15%'),
				array('data' => $value->sexo),
			);
			$columns[] = array(
				array('data' => "<b>".$this->persona->fields()['fecha_nac']."</b>", 'width' => '15%'),
				array('data' => date_format(date_create($value->fecha_nac),'d-m-Y')),
			);
			$columns[] = array(
				array('data' => "<b>".$this->persona->fields()['telefono_hab']."</b>", 'width' => '15%'),
				array('data' => $value->telefono_hab),
			);
			$columns[] = array(
				array('data' => "<b>".$this->persona->fields()['telefono_mov']."</b>", 'width' => '15%'),
				array('data' => $value->telefono_mov),
			);
		
			echo $this->table->generate($columns);
			echo "</div>";
			unset($columns);
		}
	} else {
		echo 'No se han encontrado resultados';
	}
?>