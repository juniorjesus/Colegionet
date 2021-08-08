<?php
$gradosExtra = array(
	'class' => 'form-control',
	'id'	=> 'grado',
	'required'	=> 'required'
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'submit',
	'value'	=> 'Inscribir',
	//'title' => 'Registrar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);
?>
<div class="col-md-6 inline">
	<?php echo form_open('inscripciones/inscripcion/procesarInscripcion',array('id' => 'formInscripcion','class' => 'form-horizontal'),
			array(
				'inscripcion[estudiante_id]' => $datosEstudiante->estudiante_id,
				'inscripcion[periodo_id]'	=> $periodo->periodo_id,
			)
		); ?>
	<div class="form-group">
		<?= form_label('Grados disponibles','grado',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-md-6 col-sm-8">
			<?= form_dropdown('inscripcion[grado_periodo_id]',$grados,'',$gradosExtra); ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6 col-sm-offset-4">
			<?php echo form_submit($submit) ?>
		</div>
	</div>
	<?= form_close(); ?>
</div>
<script type="text/javascript">
	$('#formInscripcion').on('submit',function(e){
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
					$('#result').slideUp('slow');
					window.open('<?= site_url('inscripciones/inscripcion/comprobanteInscripcionPDF/') ?>'+data.inscripcionID);
					setTimeout('location.reload()',2000);
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