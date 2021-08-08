<div class="container">
	<div class="myheader">
		<h1>Grados</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/grado/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('grado/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('grado/_grados', array(
						'grados' => $grados,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
