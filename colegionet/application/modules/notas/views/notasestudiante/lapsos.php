<div class="container">
	<div class="myheader">
		<h1>Lapsos</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('inscripciones/inscripcion/consultarInscripciones',
				'<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<hr>
		<div id="contenido">
			<?php 
				$ul = '<ul class="nav nav-tabs">';
				$div = '<div class="tab-content">';
				$activo = TRUE;
				foreach ($datos as $key => $value) {
					$class = '';
					if ($activo) {
						$activo = FALSE;
						$class = 'in active';
					}
					$ul .= '<li class="'.$class.'">
							<a data-toggle="tab" href="#'.$value['lapso']->lapso_id.'">Lapso '.$value['lapso']->numero.'</a></li>';
					$div .= $this->load->view('notasestudiante/_detalleLapso',array(
									'lapso' => $value['lapso'],
									'evaluacion' => $value['evaluacion'],
									'detalleEvaluacion' => $value['detalleEvaluacion'],
									'class' => $class
								),TRUE
							);
				}
				$div .= '</div>';
				$ul .= '</ul>';
			?>
			<?= $ul ?>
			<?= $div ?>
		</div>
	</div>
</div>
<?php if ($this->session->flashdata('danger')): ?>
	<script type="text/javascript">
		success = false;
		message = '<?= $this->session->flashdata('danger') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>
