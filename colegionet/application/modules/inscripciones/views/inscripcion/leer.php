<div class="container">
	<div class="myheader">
		<h1>Inscripciones</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('inscripciones/inscripcion/inscribir','Inscribir',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('inscripcion/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('inscripcion/_inscripciones', array(
						'inscripciones' => $inscripciones,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
