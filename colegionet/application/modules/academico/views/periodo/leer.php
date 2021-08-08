<div class="container">
	<div class="myheader">
		<h1>Periodos</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/periodo/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('periodo/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('periodo/_periodos', array(
						'periodos' => $periodos,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
