<div class="table-responsive col-md-8 inline">
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Datos del rol','colspan' => 2),
	);
	if ($rol->rbac_rol_id != NULL) {
		$columns[] = array(
			array('data' => "<b>".$this->rbac_rol->fields()['rbac_rol_id']."</b>", 'width' => '15%'),
			array('data' => $rol->rbac_rol_id),
		);
		$columns[] = array(
			array('data' => "<b>".$this->rbac_rol->fields()['nombre']."</b>", 'width' => '15%'),
			array('data' => $rol->nombre),
		);
		$columns[] = array(
			array('data' => "<b>".$this->rbac_rol->fields()['descripcion']."</b>", 'width' => '15%'),
			array('data' => $rol->descripcion),
		);
	} else {
		$columns[] = array(array('colspan' => '2','data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>