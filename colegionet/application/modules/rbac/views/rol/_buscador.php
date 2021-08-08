<?php 
$rbacRolID = array(
	'name' => 'rol[rbac_rol_id]',
	'class' => 'form-control',
	'placeholder' => 'Grado ID'
);
$nombre = array(
	'name' => 'rol[nombre]',
	'class' => 'form-control',
	'placeholder' => 'Nombre'
);
$descripcion = array(
	'name' => 'rol[descripcion]',
	'class' => 'form-control',
	'placeholder' => 'descripcion',
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

<?php echo form_open('rbac/rol/leer',array('id' =>'form-buscador','method' => 'get')); ?>
	<div class="form-inline input-group-sm">
		<?php echo form_input($rbacRolID) ?>
		<?php echo form_input($nombre) ?>
		<?php echo form_input($descripcion) ?>
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