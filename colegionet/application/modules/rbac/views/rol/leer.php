<div class="container">
	<div class="myheader">
		<h1>Roles</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('rbac/rol/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('rol/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('rol/_roles', array(
						'roles' => $roles,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
