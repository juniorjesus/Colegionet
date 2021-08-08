<?php 
$grado = array(
	'name' => 'grado[grado_id]',
	'class' => 'form-control',
	'placeholder' => 'Grado ID'
);
$descripcion = array(
	'name' => 'grado[grado]',
	'class' => 'form-control',
	'placeholder' => 'Grado'
);
$numero = array(
	'name' => 'grado[numero]',
	'class' => 'form-control',
	'placeholder' => 'Numero',
);
$seccion = array(
	'name' => 'grado[seccion]',
	'class' => 'form-control',
	'placeholder' => 'Seccion',
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'login',
	'value'	=> 'Buscar',
	'title' => 'Buscar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);
?>

<?php echo form_open('academico/grado/leer',array('id' =>'form-buscador','method' => 'get')); ?>
	<div class="form-inline input-group-sm">
		<?php echo form_input($grado) ?>
		<?php echo form_input($descripcion) ?>
		<?php echo form_input($numero) ?>
		<?php echo form_input($seccion) ?>
		<?php echo form_submit($submit) ?>
	</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	$('#form-buscador').on('submit',function(e){
		e.preventDefault();
		url = $(this).attr('action');
		showPleaseWait('Cargando...','info');
		$('#contenido').load(url+' #result',$(this).serialize(),
			function(response,status,xhr){
				if(xhr.status==0){
                    messageinfo('Imposible conectar con el servidor',false);
                }
				$('[data-toggle="tooltip"]').tooltip();
				hidePleaseWait();
			});
	});
</script>