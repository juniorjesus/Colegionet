<div class="container">
	<div class="myheader">
		<h1>Datos del periodo</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/periodo/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php if ($periodo->estado_periodo_id != 2): //periodo finalizado?>
			<?php echo anchor('academico/periodo/actualizar/'.$periodo->periodo_id,'Actualizar datos',array('class'=>'btn btn-default btn-sm')) ?>
		<?php endif ?>

		<?php if ($periodo->estado_periodo_id == 3): //si esta por activar ?>
			<?php echo anchor('academico/periodo/activar/'.$periodo->periodo_id,'Activar periodo',
					array('class'=>'btn btn-danger btn-sm','id' => 'boton-activar')) ?>
		<?php endif ?>
		
		<hr>
		<?php $this->load->view('periodo/_dataPeriodo',array('periodo' => $periodo)) ?>

		<hr>
		<?php $this->load->view('lapso/_dataLapsos',array('lapsos' => $lapsos)) ?>

		<hr>
		<?php $this->load->view('gradoPeriodo/_dataGradosPeriodo',array('gradosPeriodo' => $gradosPeriodo)) ?>
	</div>
</div>
<script type="text/javascript">
	$('#boton-activar').on('click',function(e){
		e.preventDefault();
		url = $(this).attr('href');
		bootbox.confirm('¿Seguro que desea activar el periodo, esto finalizara el periodo actual?, esta accion no se puede deshacer',
			function(result){
				if(result){
					$.ajax({
						url: url,
						dataType: 'json',
						data: {activar:true},
						type: 'post',
						beforeSend: function(){
							showPleaseWait('Activando..','info');
						},
						complete: function(){
							hidePleaseWait();
						},
						success: function(data){
							if (data.success) {
								location.reload();
							}else{
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
				}
			}
		);
	});
</script>