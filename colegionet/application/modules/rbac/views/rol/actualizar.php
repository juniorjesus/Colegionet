<div class="container">
	<div class="myheader">
		<h1>Actualizar rol</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('rbac/rol/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<h3><b>Rol:</b> <?= $rol->rbac_rol_id ?></h3>
		<?php $this->load->view('_form',array('mensaje' => $mensaje,'datos' => $rol)) ?>
	</div>
</div>


<?php if ($this->session->flashdata('success')): ?>
	<script type="text/javascript">
		success = true;
		message = '<?= $this->session->flashdata('success') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>
