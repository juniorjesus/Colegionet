<div class="container">
	<div class="myheader">
		<h1>Estudiantes</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/estudiante/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('estudiante/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('estudiante/_estudiantes', array(
						'estudiantes' => $estudiantes,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
