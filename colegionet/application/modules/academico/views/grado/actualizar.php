<div class="container">
	<div class="myheader">
		<h1>Actualizar grado</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/grado/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<h3><b>Grado:</b> <?= $grado->grado ?></h3>
		<?php $this->load->view('_form',array('mensaje' => $mensaje,'datos' => $grado)) ?>
	</div>
</div>


<?php if ($this->session->flashdata('success')): ?>
	<script type="text/javascript">
		success = true;
		message = '<?= $this->session->flashdata('success') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>
