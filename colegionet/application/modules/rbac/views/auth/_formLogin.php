<?php 
$username = array(
	'name'	=> 'login[usuario]',
	'id'	=> 'Login_usuario',
	'class'	=> 'form-control input-sm',
	'placeholder' => 'Usuario o correo'
);
$password = array(
	'name'	=> 'login[clave]',
	'id'	=> 'Login_clave',
	'class'	=> 'form-control input-sm',
	'placeholder' => 'Contraseña'
);
?>
<div class="col-md-12" style="padding-top: 15px">
	<?= form_open('rbac/auth/authentication',array('id'=>'login-form')); ?>
	<div class="form-group">
		<?= form_input($username); ?>
	</div>
	<div class="form-group">
		<?= form_password($password); ?>
	</div>
	<div class="form-group">
		<?= form_submit('submit','Ingresar',array('class'=>'btn btn-primary btn-block')) ?>
	</div>
	<p>
		¿Nuevo? registrate
		<?= anchor('#','Click aqui',array('id' => 'registrarse')) ?>
	</p>
	<?= form_close(); ?>
</div>

<script type="text/javascript">
	$('#login-form').on('submit',function(e){
		e.preventDefault();
		url = $(this).attr('action');
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			data: $(this).serialize(),
			beforeSend: function(){
				showPleaseWait('Verificando','info');
			},
			complete: function(){
				hidePleaseWait();
			},
			success: function(data){
				if (data.success) {
					location.reload();
				} else {
					messageinfo(data.mensajeError,data.success);
				}
			},
			error: function( XHR, Status, error) {
				hidePleaseWait();
				if (XHR.status==401) {
					messageinfo('Usted no se encuentra autorizado para realizar esta accion',false);
				}else if(XHR.status==0){
					messageinfo('Imposible conectar con el servidor',false);
				}else{
					alert(XHR.status+" "+error);
				}
			}
		});
	})


	$('#registrarse').on('click',function(e){
		e.preventDefault();
		$('#modal-formCreateUser').modal();
	})



</script>