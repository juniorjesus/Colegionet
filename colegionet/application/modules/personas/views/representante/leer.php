<div class="container">
	<div class="myheader">
		<h1>Representantes</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('personas/representante/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('representante/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('representante/_representantes', array(
						'representantes' => $representantes,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
