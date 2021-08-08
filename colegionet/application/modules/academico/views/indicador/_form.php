<?php 

$indicador = array(
	'id' => 'indicador',
	'value' => $this->input->post() ? set_value('indicador[indicador]') : (isset($datos) ? $datos->indicador : ''),
	'name' => 'indicador[indicador]',
	'class' => 'form-control input-sm',
	'required' => 'required'
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
		<?= form_label('Indicador'.$required,'indicador',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($indicador) ?>
			<?= form_error('indicador','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<?php echo form_submit($submit) ?>
		</div>
	</div>
	<?= form_close() ?>
	
</div>