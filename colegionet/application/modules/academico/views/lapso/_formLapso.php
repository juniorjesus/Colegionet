<?php 
$numero = array(
	'value'	=> set_value('lapso[numero]'),
	'id' => 'numero',
	'name' => 'lapso[numero]',
	'class' => 'form-control input-sm',
	'type'	=> 'number',
	'required' => 'required'
);
$fechaInicio = array(
	'value' => set_value('lapso[lapso_fecha_inicio]'),
	'id' => 'lapso_fecha_inicio',
	'name' => 'lapso[lapso_fecha_inicio]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);
$fechaFin = array(
	'value'	=> set_value('lapso[lapso_fecha_fin]'),
	'id' => 'lapso_fecha_fin',
	'name' => 'lapso[lapso_fecha_fin]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'submit',
	'value'	=> 'Agregar',
	//'title' => 'Registrar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);

$required = '<span class="required">*</span>';
?>
<div class="col-md-6 inline" style="vertical-align: top;" id="formLapso">
	<h3>Agregar lapsos a periodo</h3>
	<p class="alert alert-info">Todos los campos marcados con asterico ( <?= $required ?> ) son obligatorios</p>
	<?= form_open(uri_string().'#formLapso',array('class' => 'form-horizontal')) ?>

	<?php if (count($mensajeLapso) > 0): ?>
		<div class="alert alert-danger">
			<h4>Corriga lo siguiente errores:</h4>
			<?php 
				foreach ($mensajeLapso as $key => $value) {
					echo "<p>$value</p>";
				}
			?>
		</div>
	<?php endif ?>
	
	<div class="form-group">
		<?= form_label('Numero'.$required,'numero',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-4 col-sm-8">
			<?= form_input($numero) ?>
			<?= form_error('numero','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Fecha inicio'.$required,'lapso_fecha_inicio',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($fechaInicio) ?>
			<?= form_error('lapso_fecha_inicio','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Fecha Fin'.$required,'lapso_fecha_fin',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($fechaFin) ?>
			<?= form_error('lapso_fecha_fin','<p class="form_error">','</p>') ?>
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