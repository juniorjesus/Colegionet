<div class="container">
	<div class="myheader">
		<h1>Indicadores de evaluacion</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/indicador/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('indicador/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('indicador/_indicadores', array(
						'indicadores' => $indicadores,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
