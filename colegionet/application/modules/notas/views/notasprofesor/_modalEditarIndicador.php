<div class="modal fade" id='modal-editar'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id='modal-editar-title'></h4>
			</div>
			<div class="modal-body">
				<p class="alert alert-danger" id='modal-editar-summary' style="display: none"></p>
				<?= form_open('',array('id' => 'modal-editar-form','class' => 'form-horizontal')) ?>
				<p>Coloque la letra de calificacion segun la Leyenda</p><br>
				<div class="form-group">
					<?= form_label('Calificacion','modal-input-editar',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-sm-2">
						<?= form_input(
								array(
									'name' => 'indicador[calificacion]',
									'value' => '',
									'id' => 'modal-editar-input',
									'class' => 'form-control'
								)
							) ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-6 col-sm-offset-4">
						<?= form_submit(
								array(
									'name'	=> 'submit',
									'value'	=> 'Actualizar',
									//'title' => 'Registrar',
									//'data-toggle' => 'tooltip',
									'class'	=> 'btn btn-primary btn-sm'
								)
							) ?>
					</div>
				</div>
				<?= form_close() ?>
			</div>
			<p class="alert alert-info"><b>Leyenda: </b><br>E: Excelente, B: Bueno, R: Regular, M: Mejorable</p>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#modal-editar-form').on('submit',function(e){
		e.preventDefault();
		$('#modal-editar-summary').hide();
		url = $(this).attr('action');
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
					$('#modal-editar').modal('hide');
					actualizarDetalleEvaluacionGrid();
				} else {
					$('#modal-editar-summary').html(data.mensajeError);
					$('#modal-editar-summary').show();
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
	});
</script>