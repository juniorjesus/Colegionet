<?php 

$turnosData = $this->turno->getData()->result_array();
$turnos = array('' => 'Seleccione');
foreach ($turnosData as $key => $value) {
	$turnos[$value['turno_id']] = $value['turno'];
}
$turnosExtra = array(
	'class' => 'form-control',
	'placeholder' => 'Turnos',
	'required' => 'required',
	'id' => 'modalTurnos'
);

$profesoresData = $this->profesor->getData(array(),true)->result_array();
$profesores = array('' => 'Seleccione');
foreach ($profesoresData as $key => $value) {
	$profesores[$value['profesor_id']] = $value['apellidos']." ".$value['nombres'];
}
$profesoresExtra = array(
	'class' => 'form-control',
	'placeholder' => 'Profesores',
	'required' => 'required',
	'id' => 'modalProfesores'
);



$submit = array(
	'name'	=> 'submit',
	'value'	=> 'Actualizar',
	//'title' => 'Registrar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);

$required = '<span class="required">*</span>';
?>

<div class="modal fade" id ='modalDialog'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3>Actualizar grado periodo</h3>
				<p id='data-name'></p>
			</div>
			<div class="modal-body">
				<p class="alert alert-danger" id='summary' style="display: none"></p>
				<?= form_open('',array('id' => 'modalForm','class' => 'form-horizontal')) ?>
					<div class="form-group">
						<?= form_label('Turno'.$required,'',array('class' => 'control-label col-sm-4')) ?>
						<div class="col-md-4 col-sm-8">
							<?= form_dropdown('gradoPeriodo[turno_id]',$turnos,'',$turnosExtra) ?>
							<?= form_error('turno_id','<p class="form_error">','</p>') ?>
						</div>
					</div>
					<div class="form-group">
						<?= form_label('Profesor'.$required,'',array('class' => 'control-label col-sm-4')) ?>
						<div class="col-md-4 col-sm-8">
							<?= form_dropdown('gradoPeriodo[profesor_id]',$profesores,'',$profesoresExtra) ?>
							<?= form_error('profesor_id','<p class="form_error">','</p>') ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 col-md-offset-4">
							<?php echo form_submit($submit) ?>
						</div>
					</div>
				<?= form_close() ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#modalForm').on('submit',function(e){
		$('#summary').hide();
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
					location.reload();
				} else {
					$('#summary').html(data.mensajeError);
					$('#summary').show();
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