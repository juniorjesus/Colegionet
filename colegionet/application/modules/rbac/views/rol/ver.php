<div class="container">
	<div class="myheader">
		<h1>Datos del rol</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('rbac/rol/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('rbac/rol/actualizar/'.$rol->rbac_rol_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?= $this->load->view('rol/_dataRol',array('rol' => $rol)) ?>
	</div>
</div>