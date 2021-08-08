<?php 

$fechaInicio = array(
	'value' => set_value('lapso[lapso_fecha_inicio]'),
	'id' => 'm_lapso_fecha_inicio',
	'name' => 'lapso[lapso_fecha_inicio]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);
$fechaFin = array(
	'value'	=> set_value('lapso[lapso_fecha_fin]'),
	'id' => 'm_lapso_fecha_fin',
	'name' => 'lapso[lapso_fecha_fin]',
	'class' => 'form-control input-sm',
	'readonly' => 'readonly',
	'data-type' => 'date',
	'required' => 'required'
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'submit',
	'value'	=> 'Actualizar',
	//'title' => 'Registrar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);

$required = '<span class="required">*</span>';
?>
<div class="modal fade" id='modal-lapso'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id='modal-lapso-title'></h4>
			</div>
			<div class="modal-body">
				<p class="alert alert-danger" id='l-summary' style="display: none"></p>
				<?= form_open('academico/lapso/actualizar/',array('class' => 'form-horizontal','id' => 'modal-lapso-form')) ?>
				<div class="form-group">
					<?= form_label('Fecha inicio'.$required,'lapso_fecha_inicio',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-md-6 col-sm-8">
						<?= form_input($fechaInicio) ?>
						<?= form_error('lapso_fecha_inicio','<p class="form_error">','</p>') ?>
					</div>
				</div>
				<div class="form-group">
					<?= form_label('Fecha Fin'.$required,'lapso_fecha_fin',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-md-6 col-sm-8">
						<?= form_input($fechaFin) ?>
						<?= form_error('lapso_fecha_fin','<p class="form_error">','</p>') ?>
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
	$('#modal-lapso-form').on('submit',function(e){
		$('#l-summary').hide();
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
					$('#l-summary').html(data.mensajeError);
					$('#l-summary').show();
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
<script type="text/javascript">
	$('input[data-type=date]').each(function(){
		$(this).datepicker({
			currentText: 'Hoy',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
			'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
			'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
			dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié;', 'Juv', 'Vie', 'Sáb'],
			dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			changeYear: true,
			yearRange: ':+2year',
		})
	});
</script>