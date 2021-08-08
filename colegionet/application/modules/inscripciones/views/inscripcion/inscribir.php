<?php 
$identificacion = array(
	'id' => 'identificacion',
	'name' => 'identificacion',
	'placeholder' => 'Matricula o identificacion',
	'class' => 'form-control input-sm',
);

$submit = array(
	'name'	=> 'submit',
	'value'	=> 'Buscar',
	'title' => 'Buscar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);
?>
<div class="container">
	<div class="myheader">
		<h1>Generar inscripcion</h1>
	</div>
	<div class="mybody relative">
		<hr>
		<?= form_open('inscripciones/inscripcion/obtenerDatosEstudiante',array('id' => 'formBuscador', 'class' => 'form-inline')) ?>
		<?= form_input($identificacion) ?>
		<?= form_submit($submit) ?>
		<?= form_close() ?>
		<hr>

		<div id="result">
			
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#formBuscador').on('submit',function(e){
		$('#result').fadeOut();
		e.preventDefault();
		datoAbuscar = $('#identificacion').val();
		if (datoAbuscar != '') {
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
						$('#result').html(data.view);
						$('#result').fadeIn();
						//location.reload();
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
			})
		}
	})
</script>