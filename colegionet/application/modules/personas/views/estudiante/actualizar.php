<div class="container">
	<div class="myheader">
		<h1>Actualizar estudiante</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/estudiante/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<h3><b>Matricula:</b> <?= $estudiante->matricula ?></h3>
		<?php $this->load->view('_form',array('mensaje' => $mensaje,'datos' => $estudiante)) ?>
		<hr>
		<?php echo anchor('personas/estudiante/leer','<span class="fa fa-plus"></span> Agregar Representante',
						array('class'=>'btn btn-primary btn-sm','id' =>'agregar-representante')) ?>
		<hr>
		<div id='contenido'>
			<?php $this->load->view('_representantes',array(
					'estudianteRepresentante' => $estudianteRepresentante
				)) ?>
		</div>
	</div>
</div>
<?php $this->load->view('estudiante/_modalAgregarRepresentante',
	array('estudiante' => $estudiante)
); ?>

<?php if ($this->session->flashdata('success')): ?>
	<script type="text/javascript">
		success = true;
		message = '<?= $this->session->flashdata('success') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>

<script type="text/javascript">
	$('#agregar-representante').on('click',function(e){
		e.preventDefault();
		$('#modal-agregar-representante').modal();
	})
</script>