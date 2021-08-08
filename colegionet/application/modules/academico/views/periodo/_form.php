<?php 
$descripcion = array(
	'id' => 'descripcion',
	'value' => $this->input->post('periodo') ? set_value('periodo[descripcion]') : (isset($datosPeriodo) ? $datosPeriodo->descripcion : ''),
	'name' => 'periodo[descripcion]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);
$fechaInicio = array(
	'id' => 'fecha_inicio',
	'value' => $this->input->post('periodo') ? set_value('periodo[fecha_inicio]') : (isset($datosPeriodo) ?  date('d-m-Y',strtotime($datosPeriodo->fecha_inicio)) : ''),
	'name' => 'periodo[fecha_inicio]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);
$fechaFin = array(
	'id' => 'fecha_fin',
	'value' => $this->input->post('periodo') ? set_value('periodo[fecha_fin]') : (isset($datosPeriodo) ?  date('d-m-Y',strtotime($datosPeriodo->fecha_fin)) : ''),
	'name' => 'periodo[fecha_fin]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);
$fechaInicioIns = array(
	'id' => 'fecha_inicio_inscripcion',
	'value' => $this->input->post('periodo') ? set_value('periodo[fecha_inicio_inscripcion]') : (isset($datosPeriodo) ? 
										date('d-m-Y',strtotime($datosPeriodo->fecha_inicio_inscripcion)) : ''),
	'name' => 'periodo[fecha_inicio_inscripcion]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);
$fechaFinIns = array(
	'id' => 'fecha_fin_inscripcion',
	'value' => $this->input->post('periodo') ? set_value('periodo[fecha_fin_inscripcion]') : (isset($datosPeriodo) ? 
										date('d-m-Y',strtotime($datosPeriodo->fecha_fin_inscripcion)) : ''),
	'name' => 'periodo[fecha_fin_inscripcion]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'submit',
	'value'	=> isset($datosPeriodo) ? 'Actualizar' : 'Registrar',
	//'title' => 'Registrar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);

$required = '<span class="required">*</span>';
?>
<div class="col-md-6 inline">
	<p class="alert alert-info">Todos los campos marcados con asterico ( <?= $required ?> ) son obligatorios</p>
	<?= form_open('',array('class' => 'form-horizontal')) ?>

	<?php if (count($mensajePeriodo) > 0): ?>
		<div class="alert alert-danger">
			<h4>Corriga lo siguiente errores:</h4>
			<?php 
				foreach ($mensajePeriodo as $key => $value) {
					echo "<p>$value</p>";
				}
			?>
		</div>
	<?php endif ?>
	
	<div class="form-group">
		<?= form_label('Descripcion'.$required,'descripcion',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-4 col-sm-8">
			<?= form_input($descripcion) ?>
			<?= form_error('descripcion','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Fecha inicio'.$required,'fecha_inicio',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($fechaInicio) ?>
			<?= form_error('fecha_inicio','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Fecha Fin'.$required,'fecha_fin',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($fechaFin) ?>
			<?= form_error('fecha_fin','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Fecha inicio inscripcion'.$required,'fecha_inicio_inscripcion',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($fechaInicioIns) ?>
			<?= form_error('fecha_inicio_inscripcion','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<?= form_label('Fecha Fin inscripcion'.$required,'fecha_fin_inscripcion',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($fechaFinIns) ?>
			<?= form_error('fecha_fin_inscripcion','<p class="form_error">','</p>') ?>
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