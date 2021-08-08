<div class="container">
	<div class="myheader">
		<h1>Datos del estudiante</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/estudiante/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('personas/estudiante/actualizar/'.$estudiante->estudiante_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('estudiante/_dataEstudiante',array('estudiante' => $estudiante)) ?>
		<hr>
		<?php $this->load->view('estudiante/_dataRepresentantes',array('estudianteRepresentante' => $estudianteRepresentante))  ?>
	</div>
</div>