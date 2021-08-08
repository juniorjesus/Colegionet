<div class="container">
	<div class="myheader">
		<h1>Permisos del rol </h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('rbac/rol/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<h2>Rol: <?= $rol->nombre ?></h2>
		<hr>
		<?php $this->load->view('assignItem/_groupedItems',array('items' => $items,'rolID' => $rolID)) ?>
	</div>
</div>