<?php 
$identificacion = array(
	'id' => 'identificacion',
	'value' => $this->input->post() ? set_value('persona[identificacion]') : (isset($datos) ? $datos->identificacion : ''),
	'name' => 'persona[identificacion]',
	'class' => 'form-control input-sm',
);
$nombres = array(
	'id' => 'nombres',
	'value' => $this->input->post() ? set_value('persona[nombres]') : (isset($datos) ? $datos->nombres : ''),
	'name' => 'persona[nombres]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);
$apellidos = array(
	'id' => 'apellidos',
	'value' => $this->input->post() ? set_value('persona[apellidos]') : (isset($datos) ? $datos->apellidos : ''),
	'name' => 'persona[apellidos]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);
$sexo = array(
	'' => 'Seleccione',
	'F' => 'Femenino',
	'M' => 'Masculino'
);
$sexoExtra = array(
	'class' => 'form-control input-sm',
	'id'	=> 'sexo',
	'required' => 'required'
);
$sexoSelected = $this->input->post() ? set_value('persona[sexo]') : (isset($datos) ? $datos->sexo : '');

$fechaNac = array(
	'id' => 'fecha_nac',
	'value' => $this->input->post() ? set_value('persona[fecha_nac]') : (isset($datos) ? date('d-m-Y',strtotime($datos->fecha_nac)) : ''),
	'name' => 'persona[fecha_nac]',
	'readonly' => 'readonly',
	'class' => 'form-control input-sm',
	'data-type' => 'date',
	'required' => 'required',
);

$telefonoHab = array(
	'id' => 'telefono_hab',
	'value' => $this->input->post() ? set_value('persona[telefono_hab]') : (isset($datos) ? $datos->telefono_hab : ''),
	'name' => 'persona[telefono_hab]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$telefonoMov = array(
	'id' => 'telefono_mov',
	'value' => $this->input->post() ? set_value('persona[telefono_mov]') : (isset($datos) ? $datos->telefono_mov : ''),
	'name' => 'persona[telefono_mov]',
	'class' => 'form-control input-sm',
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

	<?php if (!isset($datos) || (isset($datos) && ($datos->identificacion == null || $datos->identificacion == ''))): ?>
		<div class="form-group">
			<?= form_label('Identificacion','identificacion',array('class' => 'control-label col-sm-4')) ?>
			<div class="col-md-4 col-sm-8">
				<?= form_input($identificacion) ?>
				<?= form_error('identificacion','<p class="form_error">','</p>') ?>
			</div>
		</div>
	<?php elseif(isset($datos)): ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Identificacion: </label>
			<div class="col-md-4 col-sm-8">
				<p style="padding-top: 7px; margin: 0;"><?= $datos->identificacion ?></p>
			</div>
		</div>
	<?php endif ?>

	<div class="form-group">
		<?= form_label('Nombres'.$required,'nombres',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($nombres) ?>
			<?= form_error('nombres','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Apellidos'.$required,'apellidos',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($apellidos) ?>
			<?= form_error('apellidos','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Sexo'.$required,'sexo',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_dropdown('persona[sexo]',$sexo,$sexoSelected,$sexoExtra) ?>
			<?= form_error('sexo','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Fecha Nac.'.$required,'fecha_nac',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($fechaNac) ?>
			<?= form_error('fecha_nac','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Telefono Hab.'.$required,'telefono_hab',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($telefonoHab) ?>
			<?= form_error('telefono_hab','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Telefono mov.','telefono_mov',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($telefonoMov) ?>
			<?= form_error('telefono_mov','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<?php echo form_submit($submit) ?>
		</div>
	</div>
	<?= form_close() ?>
	
</div>
<script type="text/javascript">
	$('input[data-type=date]').each(function(){
		$(this).datepicker({
			currentText: 'Hoy',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
			'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
			'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
			dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié;', 'Juv', 'Vie', 'Sáb'],
			dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			changeYear: true,
			yearRange: '1900:',
			maxDate: '0'
		})
	});
</script>