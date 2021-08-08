<?php 
$usuario = array(
	'id' => 'usuario',
	'value' => $this->input->post() ? set_value('user[usuario]') : (isset($datos) ? $datos->usuario : ''),
	'name' => 'user[usuario]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$email = array(
	'id' => 'email',
	'name' => 'user[email]',
	'class' => 'form-control input-sm',
	'value' => $this->input->post() ? set_value('user[email]') : (isset($datos) ? $datos->email : ''),
);

$clave = array(
	'id' => 'clave',
	'name' => 'user[clave]',
	'class' => 'form-control input-sm',
	'value' => set_value('user[clave]')
);

$reClave = array(
	'id' => 'reclave',
	'name' => 'user[reclave]',
	'class' => 'form-control input-sm',
	'value' => set_value('user[reclave]')
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
		<?= form_label('Usuario'.$required,'usuario',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-sm-8">
			<?= form_input($usuario) ?>
			<?= form_error('usuario','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Correo'.$required,'email',array('class' => 'control-label col-sm-4')) ?>
		<div class=" col-sm-8">
			<?= form_input($email) ?>
			<?= form_error('email','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Contraseña'.$required,'clave',array('class' => 'control-label col-sm-4')) ?>
		<div class=" col-sm-8">
			<?= form_input($clave) ?>
			<?= form_error('clave','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Confirmar contraseña'.$required,'reclave',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-sm-8">
			<?= form_input($reClave) ?>
			<?= form_error('reclave','<p class="form_error">','</p>') ?>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<?php echo form_submit($submit) ?>
		</div>
	</div>
	<?= form_close() ?>
	
</div>
