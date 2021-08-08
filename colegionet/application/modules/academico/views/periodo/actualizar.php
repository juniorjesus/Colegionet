<div class="container">
	<div class="myheader">
		<h1>Actualizar periodo</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('academico/periodo/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		
		<?php if ($periodo->estado_periodo_id == 3): //si esta por activar ?>
			<?php echo anchor('academico/periodo/activar/'.$periodo->periodo_id,'Activar periodo',
					array('class'=>'btn btn-danger btn-sm','id' => 'boton-activar')) ?>
		<?php endif ?>
		
		<hr>
		<h3><b>Periodo:</b> <?= $periodo->periodo_id ?></h3>
		<h4><b>Estado:</b> <?= $periodo->estado ?></h4>
		<?php $this->load->view('_form',array('mensajePeriodo' => $mensaje['periodo'],'datosPeriodo' => $periodo)) ?>
		<hr>
		<!--lapso periodo info-->
		<?php $this->load->view('lapso/_formLapso',array('mensajeLapso' => $mensaje['lapso'])) ?>
		<?php $this->load->view('lapso/_lapsos',array('lapsos' => $lapsos)) ?>
		<!-- -->
		<hr>
		<!--grado periodo info -->
		<?php $this->load->view('gradoPeriodo/_formGradoPeriodo',array('mensajeGradoPeriodo' => $mensaje['gradoPeriodo'])) ?>
		<?php $this->load->view('gradoPeriodo/_gradosPeriodo',array('gradosPeriodo' => $gradosPeriodo)) ?>
		<!-- -->
	</div>
</div>


<?php if ($this->session->flashdata('success')): ?>
	<script type="text/javascript">
		success = true;
		message = '<?= $this->session->flashdata('success') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>
<script type="text/javascript">
	$('#boton-activar').on('click',function(e){
		e.preventDefault();
		url = $(this).attr('href');
		bootbox.confirm('¿Seguro que desea activar el periodo, esto finalizara el periodo actual?',
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