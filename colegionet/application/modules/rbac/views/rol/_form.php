<?php 
$nombre = array(
	'id' => 'nombre',
	'value' => $this->input->post() ? set_value('rol[nombre]') : (isset($datos) ? $datos->nombre : ''),
	'name' => 'rol[nombre]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$descripcion = array(
	'id' => 'descripcion',
	'name' => 'rol[descripcion]',
	'class' => 'form-control input-sm',
	'value' => $this->input->post() ? set_value('rol[descripcion]') : (isset($datos) ? $datos->descripcion : ''),
	'rows' => 4,
	'maxlength' => 500,
);


$submit = array(
	'name'	=> 'submit',
	'id'	=> 'submit',
	'value'	=> isset($datos) ? 'Actualizar' : 'Registrar',
	//'title' => 'Registrar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);

$required = '<span class="required">*</span>';
?>
<div class="col-md-6 inline">
	<p class="alert alert-info">Todos los campos marcados con asterico ( <?= $required ?> ) son obligatorios</p>
	<?= form_open('',array('class' => 'form-horizontal')) ?>

	<?php if (validation_errors() || count($mensaje) > 0): ?>
		<div class="alert alert-danger">
			<h4>Corriga lo siguiente errores:</h4>
			<?= validation_errors() ?>
			<?php 
				foreach ($mensaje as $key => $value) {
					echo "<p>$value</p>";
				}
			?>
		</div>
	<?php endif ?>
	
	<div class="form-group">
		<?= form_label('Nombre'.$required,'nombre',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-4 col-sm-8">
			<?= form_input($nombre) ?>
			<?= form_error('nombre','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Descripcion'.$required,'Descripcion',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_textarea($descripcion) ?>
			<?= form_error('descripcion','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<?php echo form_submit($submit) ?>
		</div>
	</div>
	<?= form_close() ?>
	
</div>
