<div class="container">
	<div class="myheader">
		<h1>Datos del profesor</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/profesor/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('personas/profesor/actualizar/'.$profesor->profesor_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?= $this->load->view('profesor/_dataProfesor',array('profesor' => $profesor)) ?>
		<?php if ($profesor->rbac_user_id != NULL): ?>
			<?= $this->load->view('rbac/user/_dataUser',array('user' => $user)) ?>
		<?php else: ?>
			<p class="alert alert-info">El profesor no posee usuario</p>
		<?php endif ?>
	</div>
</div>