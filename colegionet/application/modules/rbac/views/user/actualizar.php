<div class="container">
	<div class="myheader">
		<h1>Actualizar Usuario</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('rbac/user/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<h3><b>Usuario:</b> <?= $user->rbac_user_id ?></h3>
		<?php $this->load->view('user/_form',array('mensaje' => $mensaje,'datos' => $user)) ?>
		<?php $this->load->view('user/_rolesAsignados',array('roles' => $roles,'userID' => $userID)) ?>
	</div>
</div>

<?php if ($this->session->flashdata('success')): ?>
	<script type="text/javascript">
		success = true;
		message = '<?= $this->session->flashdata('success') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>
