<?php 
$identificacion = array(
	'name' => 'profesor[identificacion]',
	'class' => 'form-control',
	'placeholder' => 'Identificacion'
);
$nombres = array(
	'name' => 'profesor[nombres]',
	'class' => 'form-control',
	'placeholder' => 'Nombres'
);
$apellidos = array(
	'name' => 'profesor[apellidos]',
	'class' => 'form-control',
	'placeholder' => 'Apellidos'
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

<?php echo form_open('personas/profesor/leer',array('id' =>'form-buscador','method' => 'get')); ?>
	<div class="form-inline input-group-sm">
		<?php echo form_input($identificacion) ?>
		<?php echo form_input($nombres) ?>
		<?php echo form_input($apellidos) ?>
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