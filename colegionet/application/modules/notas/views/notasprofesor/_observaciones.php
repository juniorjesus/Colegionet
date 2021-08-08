<?php 
$observaciones = array(
	'name' => 'evaluacion[observaciones]',
	'class' => 'form-control',
	'value' => $info->observaciones,
	'rows' => 4,
	'maxlength' => 500,
	'id' => 'evaluacionObservaciones'
);
$inasistencia = array(
	'name' => 'evaluacion[inasistencias]',
	'class' => 'form-control',
	'value' => $info->inasistencias,
	'style' => 'width: 100px',
	'type' => 'number',
	'min' => 0,
	'max' => 99
);
?>
<?php if ($habilitado): ?>
	<?= form_open('notas/notasprofesor/actualizarEvaluacion/'.$evaluacionID,array('class' => 'form-horizontal','id' => 'form-observacion')) ?>
		<div class="form-group">
			<?= form_label('Observaciones','',array('class' => 'control-label col-sm-2')) ?>
			<div class="col-sm-8 relative">
				<?= form_textarea($observaciones) ?>
				<p style="position: absolute; right: 20px;"><i id='number-counter'><?= strlen($info->observaciones) ?></i> - 500</p>
			</div>
		</div>
		<div class="form-group">
			<?= form_label('Inasistencias','',array('class' => 'control-label col-sm-2')) ?>
			<div class="col-sm-2">
				<?= form_input($inasistencia) ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-2">
				<?= form_submit(
						array(
							'name'	=> 'submit',
							'value'	=> 'Actualizar',
							'class'	=> 'btn btn-primary btn-sm'
						)
					) ?>
			</div>
		</div>
	<?= form_close() ?>
<?php else:  ?>
	<p><b>Observaciones:</b></p>
	<p><?= nl2br($info->observaciones) ?></p>
	<p><b>Inasistencias: </b> <?= $info->inasistencias ?></p>
<?php endif ?>
<script type="text/javascript">
	$('#evaluacionObservaciones').on('keyup',function(){
		cant = $(this).val().length;
		$('#number-counter').html(cant);
	});

	$('#form-observacion').on('submit',function(e){
		e.preventDefault();
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
				} else {
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