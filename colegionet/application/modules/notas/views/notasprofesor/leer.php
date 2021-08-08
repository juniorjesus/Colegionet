<div class="container">
	<div class="myheader">
		<h1>Grados asignados</h1>
	</div>
	<div class="mybody relative">
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('notasprofesor/_asignaciones', array(
						'asignacionProfesor' => $asignacionProfesor,
					)
				);
			?>
		</div>
	</div>
</div>
