<div class="container">
	<div class="myheader">
		<h1>Datos del usuario</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('rbac/user/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('rbac/user/actualizar/'.$user->rbac_user_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?= $this->load->view('user/_dataUser',array('user' => $user)) ?>
	</div>
</div>