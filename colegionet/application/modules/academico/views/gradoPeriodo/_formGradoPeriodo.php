<?php 

$turnosData = $this->turno->getData()->result_array();
$turnos = array('' => 'Seleccione');
foreach ($turnosData as $key => $value) {
	$turnos[$value['turno_id']] = $value['turno'];
}
$turnosExtra = array(
	'class' => 'form-control',
	'placeholder' => 'Turnos',
	'required' => 'required'
);

$gradosData = $this->grado->getData()->result_array();
$grados = array('' => 'Seleccione');
foreach ($gradosData as $key => $value) {
	$grados[$value['grado_id']] = $value['grado'];
}
$gradosExtra = array(
	'class' => 'form-control',
	'placeholder' => 'Grados',
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
<div class="col-md-6 inline" style="vertical-align: top;" id="formGradoPeriodo">
	<h3>Agregar grados a periodo</h3>
	<p class="alert alert-info">Todos los campos marcados con asterico ( <?= $required ?> ) son obligatorios</p>
	<?= form_open(uri_string().'#formGradoPeriodo',array('class' => 'form-horizontal')) ?>

	<?php if (count($mensajeGradoPeriodo) > 0): ?>
		<div class="alert alert-danger">
			<h4>Corriga lo siguiente errores:</h4>
			<?php 
				foreach ($mensajeGradoPeriodo as $key => $value) {
					echo "<p>$value</p>";
				}
			?>
		</div>
	<?php endif ?>
	
	<div class="form-group">
		<?= form_label('Grado'.$required,'grado',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-4 col-sm-8">
			<?= form_dropdown('gradoPeriodo[grado_id]',$grados,set_value('gradoPeriodo[grado_id]'),$gradosExtra) ?>
			<?= form_error('grado_id','<p class="form_error">','</p>') ?>
		</div>
	</div>
	
	<div class="form-group">
		<?= form_label('Turno'.$required,'turno',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-4 col-sm-8">
			<?= form_dropdown('gradoPeriodo[turno_id]',$turnos,set_value('gradoPeriodo[turno_id]'),$turnosExtra) ?>
			<?= form_error('turno_id','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
			<?php echo form_submit($submit) ?>
		</div>
	</div>
	<?= form_close() ?>
	
</div>
