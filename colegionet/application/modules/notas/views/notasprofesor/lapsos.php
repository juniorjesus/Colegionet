<div class="container">
	<div class="myheader">
		<h1>Lapsos</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('notas/notasprofesor/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('notas/notasprofesor/obtenerListaPDF/'.$grado->grado_periodo_id,
				'<span class="fa fa-file"></span> Lista PDF',array('class'=>'btn btn-default btn-sm', 'target' => '_blank')) ?>
		<h3><b>Grado:</b> <?= $grado->grado ?></h3>
		<p class="alert alert-info">Los lapsos solo estaran habilitador dentro de las fechas establecidas para cada uno.</p>
		<hr>
		<div id="contenido">
			<?php 
				$ul = '<ul class="nav nav-tabs">';
				$div = '<div class="tab-content">';
				$activo = FALSE;
				foreach ($datos as $key => $value) {
					$mostrar = FALSE;
					$class = '';
					if (!$activo && $value['activo'] != NULL) {
						$activo = TRUE;
						$class = "active";
						$mostrar = TRUE;
					}
					$ul .= '<li class="'.$class.'"><a data-toggle="tab" href="#'.$value['lapsoID'].'">'.$value['nombre'].'</a></li>';
					$div .= $this->load->view('notasProfesor/_detalleLapso',array(
									'gradoPeriodoID' => $value['gradoPeriodoID'],
									'estudiantes' => $value['estudiantes'],
									'proyecto'	=> $value['proyecto'],
									'lapsoID' => $value['lapsoID'],
									'activo' => $value['activo'],
									'ver' => $value['lapso']->ver,
									'mostrar' => $mostrar,
									'lapso' => $value['lapso'],
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
