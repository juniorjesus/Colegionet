<div class="table-responsive col-md-8 inline">
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Datos del usuario','colspan' => 2),
	);
	if ($user->rbac_user_id != NULL) {
		$columns[] = array(
			array('data' => "<b>".$this->rbac_user->fields()['rbac_user_id']."</b>", 'width' => '15%'),
			array('data' => $user->rbac_user_id),
		);
		$columns[] = array(
			array('data' => "<b>".$this->rbac_user->fields()['usuario']."</b>", 'width' => '15%'),
			array('data' => $user->usuario),
		);
		$columns[] = array(
			array('data' => "<b>".$this->rbac_user->fields()['email']."</b>", 'width' => '15%'),
			array('data' => $user->email),
		);
		$columns[] = array(
			array('data' => "<b>".$this->rbac_user->fields()['clave']."</b>", 'width' => '15%'),
			array('data' => $user->clave),
		);
	} else {
		$columns[] = array(array('colspan' => '2','data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>