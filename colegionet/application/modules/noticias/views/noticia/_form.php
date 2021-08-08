<?php 
$titulo = array(
	'id' => 'titulo',
	'value' => $this->input->post() ? set_value('noticia[titulo]') : (isset($datos) ? $datos->titulo : ''),
	'name' => 'noticia[titulo]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);
$imagen = array(
	'id' => 'imagen',
	'value' => $this->input->post() ? set_value('noticia[imagen]') : (isset($datos) ? $datos->imagen : ''),
	'name' => 'imagen',
	'class' => 'form-control input-sm',
	'type'	=> 'file',
);

$contenido = array(
	'id' => 'seccion',
	'value' => $this->input->post() ? set_value('noticia[contenido]') : (isset($datos) ? $datos->contenido : ''),
	'name' => 'noticia[contenido]',
	'class' => 'form-control input-sm',
	'required' => 'required',
	'rows' => 8,
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
<div class="col-md-8 inline">
	<p class="alert alert-info">Todos los campos marcados con asterico ( <?= $required ?> ) son obligatorios</p>
	<?= form_open_multipart('',array('class' => 'form-horizontal')) ?>

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
		<?= form_label('Titulo'.$required,'titulo',array('class' => 'control-label col-sm-2')) ?>
		<div class="col-md-4 col-sm-8">
			<?= form_input($titulo) ?>
			<?= form_error('titulo','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<?php if (isset($datos) && $datos->imagen != NULL): ?>
		<div class="col-sm-offset-2">
			<img src=" <?= base_url('noticias/'.$datos->imagen) ?> " style="max-width: 50%;">
		</div>
	<?php endif ?>
	<div class="form-group">
		<?= form_label('Imagen'.$required,'imagen',array('class' => 'control-label col-sm-2')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_input($imagen) ?>
			<?= form_error('imagen','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<p class="alert alert-warning">Solo extenciones: <b>JPG,PNG</b>, tamaño max.: <b>200 kb</b>, resolucion max: <b>1024 x 768</b></p>
	<div class="form-group">
		<?= form_label('Contenido'.$required,'seccion',array('class' => 'control-label col-sm-2')) ?>
		<div class="col-sm-10">
			<?= form_textarea($contenido) ?>
			<?= form_error('contenido','<p class="form_error">','</p>') ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-md-offset-2">
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