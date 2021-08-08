<?php 
$grado = array(
	'id' => 'grado',
	'value' => $this->input->post() ? set_value('grado[grado]') : (isset($datos) ? $datos->grado : ''),
	'name' => 'grado[grado]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);
$numero = array(
	'id' => 'numero',
	'value' => $this->input->post() ? set_value('grado[numero]') : (isset($datos) ? $datos->numero : ''),
	'name' => 'grado[numero]',
	'class' => 'form-control input-sm',
	'type'	=> 'number',
	'required' => 'required'
);

$seccion = array(
	'id' => 'seccion',
	'value' => $this->input->post() ? set_value('grado[seccion]') : (isset($datos) ? $datos->seccion : ''),
	'name' => 'grado[seccion]',
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
		<?= form_label('Descripcion'.$required,'grado',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-4 col-sm-8">
			<?= form_input($grado) ?>
			<?= form_error('grado','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Numero'.$required,'numero',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($numero) ?>
			<?= form_error('numero','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Seccion'.$required,'seccion',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($seccion) ?>
			<?= form_error('seccion','<p class="form_error">','</p>') ?>
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
			yearRange: ':+2year',
		})
	});
</script>