<div class="container">
	<div class="myheader">
		<h1>Actualizar profesor</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/profesor/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php if ($profesor->rbac_user_id == NULL): ?>
			<?php echo anchor('','<span class="fa fa-plus"></span> Crear usuario',array('class'=>'btn btn-default btn-sm','id' => 'crear-usuario')) ?>
			<?php $this->load->view('rbac/user/_formCreateUser',array('tipo' => 'profesor','profesor' => $profesor)) ?>
		<?php endif ?>
		<hr>
		<h3><b>Profesor:</b> <?= $profesor->profesor_id ?></h3>
		<?php $this->load->view('_form',array('mensaje' => $mensaje,'datos' => $profesor)) ?>
		
	</div>
</div>


<?php if ($this->session->flashdata('success')): ?>
	<script type="text/javascript">
		success = true;
		message = '<?= $this->session->flashdata('success') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>
<script type="text/javascript">
	$('#crear-usuario').on('click',function(e){
		e.preventDefault();
		$('#modal-formCreateUser').modal();
	});
</script>