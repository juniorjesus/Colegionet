<?php 
$identificacion = array(
	'id' => 'identificacion',
	'name' => 'persona[identificacion]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$usuario = array(
	'id' => 'usuario',
	'name' => 'user[usuario]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$email = array(
	'id' => 'email',
	'name' => 'user[email]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$clave = array(
	'id' => 'clave',
	'name' => 'user[clave]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$reClave = array(
	'id' => 'reclave',
	'name' => 'user[reclave]',
	'class' => 'form-control input-sm',
	'required' => 'required'
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'submit',
	'value'	=> 'Registrarse',
	'class'	=> 'btn btn-primary btn-sm'
);

$required = '<span class="required">*</span>';
?>
<div class="modal fade" id='modal-formCreateUser'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4>Registrarse</h4>
			</div>
			<div class="modal-body">
				<p class="alert alert-danger" id='createUser-summary' style="display: none"></p>
				
				<?php if (isset($profesor)): ?>
					<?= form_open('rbac/user/crear',array('class' => 'form-horizontal','id' =>'form-createUser'),array('tipo' => $tipo,'profesor' => $profesor->profesor_id)) ?>
				<?php else: ?>
					<?= form_open('rbac/user/crear',array('class' => 'form-horizontal','id' =>'form-createUser'),array('tipo' => $tipo)) ?>
					<div class="form-group">
						<?= form_label('Cedula'.$required,'identificacion',array('class' => 'control-label col-sm-4')) ?>
						<div class="col-md-6 col-sm-8">
							<?= form_input($identificacion) ?>
							<?= form_error('identificacion','<p class="form_error">','</p>') ?>
						</div>
					</div>
				<?php endif ?>

				<div class="form-group">
					<?= form_label('Usuario'.$required,'usuario',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-md-6 col-sm-8">
						<?= form_input($usuario) ?>
						<?= form_error('usuario','<p class="form_error">','</p>') ?>
					</div>
				</div>
				<div class="form-group">
					<?= form_label('Correo'.$required,'email',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-md-6 col-sm-8">
						<?= form_input($email) ?>
						<?= form_error('email','<p class="form_error">','</p>') ?>
					</div>
				</div>
				<div class="form-group">
					<?= form_label('Contraseña'.$required,'clave',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-md-6 col-sm-8">
						<?= form_password($clave) ?>
						<?= form_error('clave','<p class="form_error">','</p>') ?>
					</div>
				</div>
				<div class="form-group">
					<?= form_label('Confirmar Contraseña'.$required,'reclave',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-md-6 col-sm-8">
						<?= form_password($reClave) ?>
						<?= form_error('reclave','<p class="form_error">','</p>') ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-6 col-md-offset-4">
						<?php echo form_submit($submit) ?>
					</div>
				</div>
				<?= form_close() ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#form-createUser').on('submit',function(e){
		$('#createUser-summary').hide();
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
				//hidePleaseWait();
			},
			success: function(data){
				if (data.success) {
					<?php if (isset($profesor)): ?>
						location.reload();
					<?php else: ?>
						iniciarSesion(data);				
					<?php endif ?>
				} else {
					hidePleaseWait();
					$('#createUser-summary').html(data.mensajeError);
					$('#createUser-summary').show();
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
	});

	function iniciarSesion(data){
		dataUser = data.user;
		login = {};
		login['login'] = {};
		login['login']['usuario'] = dataUser.usuario;
		login['login']['clave'] = $('#clave').val();
		$.ajax({
			type: 'post',
			url: '<?= site_url('rbac/auth/authentication') ?>',
			dataType: 'json',
			data: login,
			beforeSend: function(){
				changeMessagePleaseWait('Iniciando sesion','info');
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
	}
</script>