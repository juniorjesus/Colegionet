<div id='<?= $lapso->lapso_id ?>' class="col-md-12 inline relative tab-pane fade <?= $class ?>"> 
	<div id='result-<?= $lapso->lapso_id ?>'>
		<?php if ($evaluacion != NULL): ?>
			<br>
			<p><b>Proyecto: </b><?= $evaluacion->proyecto ?></p>

			<?php 
				$template = array(
					'table_open' => '<table class="table table-striped table-condensed table-hover">',
					'thead_open' => '<thead class="thead-header">'
				);
				$this->table->set_template($template);
				$this->table->set_empty('<i>Sin información</i>');

				$columns[0] = array(
					array('data' => 'Indicadores evaluados','colspan' => 2),
					array('data' => 'Items','width' => '10%')
				);
				if (count($detalleEvaluacion) > 0){
					foreach ($detalleEvaluacion as $key => $value) {
						$arr = array_intersect_key($value, array_flip(array(
									'indicador','calificacion'
								)
							)
						);
						$columns[] = array_merge(array(array('data' =>$key+1, 'align' => 'center')),$arr);
					}
				}else{
					//cuando no hay resultados
					$columns[] = array(array('colspan' => 3,'data' => ''));
				}
				echo $this->table->generate($columns);
			?>
			<p><b>Observaciones: </b><?= nl2br($evaluacion->observaciones) ?></p>
			<p><b>Inasistencias: </b><?= nl2br($evaluacion->inasistencias) ?></p>
			<p class="alert alert-info"><b>Leyenda: </b><br>E: Excelente, B: Bueno, R: Regular, M: Mejorable</p>
		<?php else: ?>
			<h3>No se ha encontrado evaluacion para el lapso</h3>
		<?php endif ?>
	</div>
</div>