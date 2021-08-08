<?php 
$reprentantes = array('' =>'Reprentante');
$dataRepresentante = $this->estudiante_representante->getData(array(
		'where' => array(array('estudiante.estudiante_id' => $info->estudiante_id)),
		'select' => array(array('parentesco.*','p2.*','representante.*'))
	),TRUE)->result();
foreach ($dataRepresentante as $key => $value) {
	$reprentantes[$value->representante_id] = $value->apellidos." ".$value->nombres." [".$value->descripcion."]";
}
$representanteExtra = array(
	'class' => 'form-control input-sm',
	'id' => 'evaluacionRepresentante'
);

?>
<div class="container">
	<div class="myheader">
		<h1>Grados asignados</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('notas/notasprofesor/lapsos/'.$info->grado_periodo_id,
				'<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php echo anchor('notas/notasprofesor/obtenerBoletinPDF/'.$evaluacionID,
				'<span class="fa fa-file"></span> Boletin PDF',array('class'=>'btn btn-default btn-sm', 'target' => '_blank')) ?>
		<hr>
		<style type="text/css">
			p{
				margin: 0;
			}
		</style>
		<div id="contenido">
			<p class="text-center"><b>Boletin Informativo</b></p>
			<p class="text-center"><?= $info->lapso ?> Lapso</p>
			<p><b>Nombres y apellido:</b> <u><?= $info->est_nombres." ".$info->est_apellidos ?></u></p>
			<p><b>Cedula escolar y/o cedula de identidad: </b> 
				<u><?= $info->est_identificacion != NULL && $info->est_identificacion != '' ? $info->est_identificacion : $info->matricula?></u>
			</p>
			<?php if ($habilitado): ?>
				<?= form_label('Rrepresentante') ?>
				<div class="col-sm-3 inline">
					<select name="evaluacion[representante_id]" id="evaluacionRepresentante" class="form-control input-sm">
						<option  ci value>Representante</option>
						<?php foreach($dataRepresentante as $key => $value): ?>
							<option ci="<?= $value->identificacion ?>" value="<?= $value->representante_id ?>" 
								<?= $value->identificacion != NULL && $value->identificacion == $info->re_identificacion ? 'selected' : ''  ?>>
								<?= $value->nombres." ".$value->apellidos." [".$value->descripcion."]" ?>
							</option>
						<?php endforeach ?>
					</select>
				</div>
				<?= form_label('C.I.:') ?> <u id="infoIdentificacion"><?= $info->re_identificacion ?></u>
				<br>
			<?php else: ?>
				<p><b>Representante: </b><u><?= $info->re_nombres." ".$info->re_apellidos ?></u> <b>C.I.:</b><u><?= $info->re_identificacion ?></u></p>
			<?php endif ?>
			<p><b>Docente: </b><u><?= $info->pro_nombres." ".$info->pro_apellidos ?></u></p>
			<p><b>Grado: </b><u><?= $info->numero ?></u> <b>Seccion: </b><u><?= $info->seccion ?></u>
				<b>Año escolar: </b><u><?= $info->anio_inicio. " - ".$info->anio_fin ?></u>
			</p>
			<p><b>Nombre del proyecto: </b> <u><?= $info->proyecto ?></u></p>
			<hr>
			<h4 class="text-center">Descripcion del proceso Educativo</h4>
			
			<div id='contenido-eval'>
				<?= $this->load->view('_indicadoresEvaluados',array(
							'info' => $info,
							'evaluacionID' => $evaluacionID,
							'detallesEvaluacion' => $detallesEvaluacion,
							'habilitado' => $habilitado
						)
					) ?>
			</div>
			<div>
				<?= $this->load->view('_observaciones',array(
							'info' => $info,
							'evaluacionID' => $evaluacionID,
							'habilitado' => $habilitado
						)
					) ?>
			</div>
			<?php $this->load->view('notasprofesor/_modalEditarIndicador') ?>
			<?php $this->load->view('notasprofesor/_modalIndicadores') ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	function actualizarDetalleEvaluacionGrid() {
		url = '<?= site_url(uri_string()) ?>';
		$('#contenido-eval').load(url+' #result-eval',
			function(response,status,xhr){
				if(xhr.status==0){
					messageinfo('Imposible conectar con el servidor',false);
					location.reload();
				}
				hidePleaseWait();
			}
		);
	}

	$('#evaluacionRepresentante').on('change',function(){
		val = $(this).val();
		ci = $(this).find('option:selected').attr('ci');
		data = {};
		data['evaluacion'] = {};
		data['evaluacion']['representante_id'] = val;
		url = '<?= site_url('notas/notasprofesor/actualizarEvaluacion/'.$evaluacionID) ?>';
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			data: data,
			beforeSend: function(){
				showPleaseWait('Verificando','info');
			},
			complete: function(){
				hidePleaseWait();
			},
			success: function(data){ 
				if (data.success) {
					$('#infoIdentificacion').html(ci);
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
	})

</script>
