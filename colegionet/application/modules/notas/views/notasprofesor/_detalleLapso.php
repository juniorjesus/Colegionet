<div id='<?= $lapsoID ?>' class="col-md-12 inline relative tab-pane fade 
				<?= $activo != NULL ? ($mostrar ? 'in active' : '') : ($ver != NULL ? '' : 'blocked') ?>"> 
	<div id='result-<?= $lapsoID ?>'>
		<br>
		<p class="alert alert-info">Disponible desde: 
						<?= date_format(date_create($lapso->lapso_fecha_inicio),'d-m-Y') .
						" hasta: ". date_format(date_create($lapso->lapso_fecha_fin),'d-m-Y') ?> </p>
		<?php 
			$class = '';
			if ($proyecto != NULL) {
				$this->load->view('notasprofesor/_detalleProyecto',array('proyecto' => $proyecto));
			}else{
				$this->load->view('notasprofesor/_formProyecto',array('lapsoID' => $lapsoID,'gradoPeriodoID' => $gradoPeriodoID));
				$class = 'blocked';
			}
		?>
		<hr>
		<div class="relative">
			<div class="<?= $class ?>" id ="<?=$gradoPeriodoID.$lapsoID ?>">
				<?php
					$this->load->view('notasprofesor/_estudiantesEnGrado',array(
							'gradoPeriodoID' => $gradoPeriodoID,
							'estudiantes'=>$estudiantes,
							'proyecto' => $proyecto,
							'lapsoID' => $lapsoID,
							'ver' => $ver
						)
					);
				?>
			</div>
		</div>
	</div>
</div>