<div class="container">
	<div class="myheader">
		<h1>Datos del indicador</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/indicador/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('academico/indicador/actualizar/'.$indicador->indicador_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?= $this->load->view('indicador/_dataIndicador',array('indicador' => $indicador)) ?>
	</div>
</div>