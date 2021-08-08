<div class="container">
	<div class="myheader">
		<h1>Noticias</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('noticias/noticia/crear','Registar nuevo',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<?php $this->load->view('noticia/_buscador') ?>
		<hr>
		<div id="contenido">
			<?php 
				$this->load->view('noticia/_noticias', array(
						'noticias' => $noticias,
						'pagNums'	=> $pagNums
					)
				);
			?>
		</div>
	</div>
</div>
