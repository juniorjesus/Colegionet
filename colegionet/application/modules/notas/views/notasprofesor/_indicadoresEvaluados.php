<div class="table-responsive" id='result-eval'>
	
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data'=>'<h4>Indicadores evaluados durante el lapso</h4>','colspan' => 2),
		array('data'=>'<h5>ITEMS</h5>','colspan' => 2),
	);
	$num = 1;
	foreach ($detallesEvaluacion as $key => $value) {
		if ($habilitado) {
			$asLink = anchor(
				'notas/notasprofesor/editarDetalleEvaluacion/'.$value['detalle_evaluacion_id'],$value['indicador'],
				array(
					'title' => 'Haga click para editar',
					'indicador' => $value['indicador'],
					'calificacion' => $value['calificacion'],
					'onclick' => 'editarItem(this)',
					'class' =>'btn-block'
				)
			);
			$input = form_input(
				array(
					'name' => 'indicador[calificacion]',
					'value' => $value['calificacion'],
					'detail' => $value['detalle_evaluacion_id'],
					'class' => 'form-control input-sm',
					'onchange' => 'actualizarItem(this)',
					'maxlength' => 1,
				)
			);
		} else {
			$indicador = $value['indicador'];
			$calificacion = $value['calificacion'];
		}
		$linkDelete = anchor('notas/notasprofesor/borrarDetalleEvaluacion/'.$value['detalle_evaluacion_id'],
						'<span class="fa fa-close"></span>',array('onclick' => 'eliminarItem(this)'));
		$columns[] =array(
			array('data' => $num++,'width' => '2%'),
			array('data' => $habilitado ? $asLink : $indicador,'width' => '90%'),
			array('data' => $habilitado ? $input : $calificacion),
			array('data' => $habilitado ? $linkDelete : '<i></i>','width' => '2%')
		);
	}
	for ($i = $num; $i <= 12  ; $i++) {
		$data = $habilitado ? '<a href="#" onclick="agregarItems()" class="btn-block"><i>Agregar</i></a>' : '';
		$columns[] =array(
			array('data' => $i,'width' => '2%'),
			array('data' => $data,'width' => '90%'),
			array('data' => '--'),
			array('data' => '<i></i>','width' => '2%')
		);
	}
	echo $this->table->generate($columns);
?>
<p class="alert alert-info"><b>Leyenda: </b><br>E: Excelente, B: Bueno, R: Regular, M: Mejorable</p>
</div>

<script type="text/javascript">
	function editarItem(link) {
		event.preventDefault();
		url = $(link).attr('href');
		indicador = $(link).attr('indicador');
		calificacion = $(link).attr('calificacion');			
		$('#modal-editar-title').html('Indicador: '+indicador);
		$('#modal-editar-input').val(calificacion);
		$('#modal-editar-form').attr('action',url);
		$('#modal-editar').modal();
	}

	function eliminarItem(link){
		event.preventDefault();
		url = $(link).attr('href');
		bootbox.confirm('¿Seguro que desea eliminar este indicador?, esta accion no se puede deshacer',
			function(result){
				if (result) {
					$.ajax({
						type: 'post',
						url: url,
						dataType: 'json',
						data: {eliminar:true},
						beforeSend: function(){
							showPleaseWait('Verificando','info');
						},
						complete: function(){
							hidePleaseWait();
						},
						success: function(data){
							if (data.success) {
								messageinfo(data.mensaje,data.success);
								actualizarDetalleEvaluacionGrid();
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
				}
			}
		);
		
	}

	function actualizarItem(input){
		event.preventDefault()
		
		regExp = new RegExp("[EBRM]");
		detail = $(input).attr('detail');
		calificacion = $(input).val();
		
		if (!regExp.test(calificacion)) {
			messageinfo('Solo se permiten valores E,B,R,M segun la leyenda',false);
			return;
		}

		indicador = {};
		indicador['indicador'] = {};
		indicador['indicador']['calificacion'] = calificacion
		$.ajax({
			type: 'post',
			url: '<?= site_url('notas/notasprofesor/editarDetalleEvaluacion/') ?>'+detail,
			dataType: 'json',
			data: indicador,
			beforeSend: function(){
				showPleaseWait('Verificando','info');
			},
			complete: function(){
				hidePleaseWait();
			},
			success: function(data){
				if (data.success) {
					messageinfo(data.mensaje,data.success);
					$('#modal-editar').modal('hide');
					actualizarDetalleEvaluacionGrid();
				} else {
					$('#modal-editar-summary').html(data.mensajeError);
					$('#modal-editar-summary').show();
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

	function agregarItems(){
		event.preventDefault();
		myurl = '<?= site_url('notas/notasprofesor/obtenerIndicadoresNoUsados/'.$evaluacionID) ?>';
		$.ajax({
			type: 'post',
			url: myurl,
			dataType: 'json',
			data: {obtener: true},
			beforeSend: function(){
				showPleaseWait('Obteniendo informacion','info');
			},
			complete: function(){
				hidePleaseWait();
			},
			success: function(data){
				if (data.success) {
					$('#modal-indicadores-body').html(data.table);
					$('#modal-indicadores').on('show.bs.modal', function () {
						$('.modal-content').css('max-height',$( window ).height()*0.9);
						$('.modal-content').css('overflow-y','auto');
					});
					$('#modal-indicadores').modal();
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
	}
</script>