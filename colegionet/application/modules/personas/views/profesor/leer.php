<div class="container">
	<div class="myheader">
		<h1>Profesores</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/profesor/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('profesor/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('profesor/_profesores', array(
						'profesores' => $profesores,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
