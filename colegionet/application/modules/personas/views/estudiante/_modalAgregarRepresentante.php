<?php 
$dataParentescos = $this->parentesco->getData()->result();
$parentescos = array('' => 'Parentesco');
foreach ($dataParentescos as $key => $value) {
	$parentescos[$value->parentesco_id] = $value->descripcion;
}
$parentescoExtra = array(
	'class' => 'form-control input-sm',
	'id'	=> 'parentesco',
	'required' => 'required'
);

$identificacion = array(
		'name' => 'persona[identificacion]',
		'value' => '',
		'id' => 'modal-editar-input',
		'class' => 'form-control',
		'required' => 'required'
	);
?>


<div class="modal fade" id='modal-agregar-representante'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4>Agregar representante al estudiante</h4>
			</div>
			<div class="modal-body">
				<p class="alert alert-danger" id='modal-representante-summary' style="display: none"></p>
				<?= form_open('personas/estudiante/agregarEstudianteRepresentante/'.$estudiante->estudiante_id,
								array('id' => 'modal-representante-form','class' => 'form-horizontal')) ?>				
				<div class="form-group">
					<?= form_label('Cedula','',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-sm-6">
						<?= form_input($identificacion) ?>
					</div>
				</div>
				<div class="form-group">
					<?= form_label('Parentesco','',array('class' => 'control-label col-sm-4')) ?>
					<div class="col-sm-6">
						<?= form_dropdown('persona[parentesco_id]',$parentescos,'',$parentescoExtra) ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-6 col-sm-offset-4">
						<?= form_submit(
								array(
									'name'	=> 'submit',
									'value'	=> 'Registrar',
									//'title' => 'Registrar',
									//'data-toggle' => 'tooltip',
									'class'	=> 'btn btn-primary btn-sm'
								)
							) ?>
					</div>
				</div>
				<?= form_close() ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#modal-representante-form').on('submit',function(e){
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
					$('#modal-agregar-representante').modal('hide');
					$('#modal-representante-form').trigger('reset');
					$('#contenido').load('<?= site_url(uri_string()) ?>'+' #result',
						function(response,status,xhr){
							if(xhr.status==0){
			                    messageinfo('Imposible conectar con el servidor',false);
			                }
							$('[data-toggle="tooltip"]').tooltip();
							hidePleaseWait();
						}
					);			
				} else {
					$('#modal-representante-summary').html(data.mensajeError);
					$('#modal-representante-summary').show();
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