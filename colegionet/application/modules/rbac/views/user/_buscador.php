<?php 
$rbacUserID = array(
	'name' => 'user[rbac_user_id]',
	'class' => 'form-control',
	'placeholder' => 'Usuario ID'
);
$usuario = array(
	'name' => 'user[usuario]',
	'class' => 'form-control',
	'placeholder' => 'Usuario'
);
$email = array(
	'name' => 'user[email]',
	'class' => 'form-control',
	'placeholder' => 'Correo',
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

<?php echo form_open('rbac/user/leer',array('id' =>'form-buscador','method' => 'get')); ?>
	<div class="form-inline input-group-sm">
		<?php echo form_input($rbacUserID) ?>
		<?php echo form_input($usuario) ?>
		<?php echo form_input($email) ?>
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