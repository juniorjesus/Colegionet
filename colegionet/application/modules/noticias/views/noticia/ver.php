<div class="container">
	<div class="myheader">
		<h1>Datos del grado</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/grado/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('academico/grado/actualizar/'.$grado->grado_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?= $this->load->view('grado/_dataGrado',array('grado' => $grado)) ?>
	</div>
</div>