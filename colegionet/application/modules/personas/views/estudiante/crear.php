<div class="container">
	<div class="myheader">
		<h1>Registrar estudiante</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/estudiante/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('_form',array('mensaje' => $mensaje)) ?>
	</div>
</div>