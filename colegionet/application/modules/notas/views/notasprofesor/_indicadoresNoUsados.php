<div class="table-responsive">
	
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Indicadores sin usar','colspan' => 2),
		array('data' => 'Calificacion'),
	);

	if (count($indicadores)>0){
		foreach ($indicadores as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'indicador'
					)
				)
			);

			$checkBox = form_checkbox(
				array(
					'name' => 'detalleEvaluacion['.$value['indicador_id'].'][indicador_id]',
					'value' => $value['indicador_id']
				)
			);

			$input = form_input(
				array(
					'name' => 'detalleEvaluacion['.$value['indicador_id'].'][calificacion]',
					'class' => 'form-control input-sm',
					'disabled' => 'disabled'
				)
			);

			$columns[] = array_merge(
							array(array('data' => $checkBox,'align' => 'center','width'=>'10%')),
							$arr,
							array(array('data' =>$input, 'align' => 'center','width' => '10%'))
						);
		}
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => 3,'data' => ''));
	}
	echo form_open('notas/notasprofesor/agregarDetallesEvaluacion/'.$evaluacionID,array('id' => 'indicadoresNoUsados-form'));
	?>
	<p class="alert alert-danger" id='modal-indicadores-summary' style="display: none"></p>
	<?php
	echo $this->table->generate($columns);
	?>
	<div class="form-group">
		<div>
			<?= form_submit(
					array(
						'name'	=> 'submit',
						'value'	=> 'Cargar',
						'class'	=> 'btn btn-primary btn-sm'
					)
				) ?>
		</div>
	</div>
	<?php
	echo form_close();
?>
</div>
<script type="text/javascript">
	$('input[type=checkbox]').unbind().on('click',function(){
		disabled = true;
		if ($(this).is(':checked')) {
			disabled = false;
		}
		inputCalificacion = $(this).parents('tr').children('td:last').find('input');
		inputCalificacion.attr('disabled',disabled);
		inputCalificacion.val('');
	});

	$('#indicadoresNoUsados-form').on('submit',function(e){
		$('#modal-indicadores-summary').hide();
		e.preventDefault();
		url = $(this).attr('action');

		inputscheckeds = $('#modal-indicadores-body').find('table').find('input[type=checkbox]:checked').length;
		inputsMax = 12 - $('#contenido-eval').find('table').find('input').length;
		if (inputscheckeds == 0) {
			$('#modal-indicadores-summary').html('Debe seleccionar al menos 1 indicador');
			$('#modal-indicadores-summary').show();
			return;
		}else if (inputsMax < inputscheckeds) {
			$('#modal-indicadores-summary').html('Supera el maximo de indicadores posible para la evaluacion');
			$('#modal-indicadores-summary').show();
			return;
		}
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			data: $(this).serialize(),
			beforeSend: function(){
				showPleaseWait('Verificando','info');
			},
			complete: function(){
				hidePleaseWait();
			},
			success: function(data){
				if (data.success) {
					messageinfo(data.mensaje,data.success);
					$('#modal-indicadores').modal('hide');
					actualizarDetalleEvaluacionGrid();
				} else {
					$('#modal-indicadores-summary').html(data.mensajeError);
					$('#modal-indicadores-summary').show();
					messageinfo(data.mensajeError,data.success);
				}
			},
			error: function( XHR, Status, error) {
				hidePleaseWait();
				if (XHR.status==401) {
					messageinfo('Usted no se encuentra autorizado para realizar esta accion',false);
				}else if(XHR.status==0){
					messageinfo('Imposible conectar con el servidor',false);
				}else{
					alert(XHR.status+" "+error);
				}
			}
		});
	})
</script>
