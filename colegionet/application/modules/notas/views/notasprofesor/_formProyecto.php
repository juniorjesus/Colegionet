<?php 
$proyecto = array(
	'name'	=> 'proyecto[proyecto]',
	'class'	=> 'form-control input-sm',
	'placeholder' => 'Nombre proyecto'
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'submit',
	'value'	=> 'Guardar',
	'class'	=> 'btn btn-primary btn-sm'
);

?>
<h4>Proyecto</h4>
	<?= form_open('notas/notasprofesor/crearproyecto/'.$gradoPeriodoID.'/'.$lapsoID,array('class' => 'form-inline', 'id' => 'form-'.$lapsoID)) ?>
	<div class="form-group">
		<?= form_label('Nombre Proyecto','') ?>
		<?= form_input($proyecto) ?>
		<?= form_submit($submit) ?>
	</div>
	<?= form_close() ?>
<script type="text/javascript">
	$('#form-<?= $lapsoID ?>').on('submit',function(e){
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
				//hidePleaseWait();
			},
			success: function(data){ 
				if (data.success) {
					messageinfo(data.mensaje,data.success);
					thisURL = '<?= site_url(uri_string()) ?>';
					changeMessagePleaseWait('Actualizando');
					$('#<?= $lapsoID ?>').load(thisURL+' #result-<?= $lapsoID ?>',
						function(response,status,xhr){
							if(xhr.status==0){
								messageinfo('Imposible conectar con el servidor',false);
								location.reload();
							}
							hidePleaseWait();
						}
					);

				} else {
					hidePleaseWait();
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