<div class="container">
	<div class="myheader">
		<h1>Inscripciones</h1>
	</div>
	<div class="mybody relative">
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('inscripcion/_estudianteInscripciones', array(
						'inscripciones' => $inscripciones,
					)
				);
			?>
		</div>
	</div>
</div>
