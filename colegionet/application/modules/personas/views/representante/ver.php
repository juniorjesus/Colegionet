<div class="container">
	<div class="myheader">
		<h1>Datos del representante</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/representante/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('personas/representante/actualizar/'.$representante->representante_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?= $this->load->view('representante/_dataRepresentante',array('representante' => $representante)) ?>
	</div>
</div>