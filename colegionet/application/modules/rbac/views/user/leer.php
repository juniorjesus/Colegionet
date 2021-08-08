<div class="container">
	<div class="myheader">
		<h1>Roles</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('rbac/user/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('user/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('user/_usuarios', array(
						'usuarios' => $usuarios,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
