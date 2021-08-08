<div class="table-responsive col-md-6 inline" id='gradosPeriodo' style="vertical-align: top;">
	<h4>Grados asociados al periodo</h4>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->grado->fields()['grado'],'width' => '20%'),
		array('data' => $this->grado->fields()['numero'],'width' => '10%'),
		array('data' => $this->grado->fields()['seccion'],'width' => '10%'),
		array('data' => $this->turno->fields()['turno'],'width' => '10%'),
		array('data' => 'Profesor','width' => '20%'),
		array('data' => '','width' => '10%'),
	);
	if (count($gradosPeriodo)>0){
		foreach ($gradosPeriodo as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'grado','numero','seccion','turno','profesor'
					)
				)
			);
			$links = '';
			$links .= anchor('academico/gradoPeriodo/borrar/'.$value['grado_periodo_id'],'<span class="fa fa-close"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Eliminar','onclick' => 'borrarGradoPeriodo(this)')
			);
			$links .= anchor('academico/gradoPeriodo/actualizar/'.$value['grado_periodo_id'],'<span class="fa fa-pencil"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Actualizar','onclick' => 'actualizarGradoPeriodo(this)',
					'turno' => $value['turno_id'], 'profesor' => $value['profesor_id'],'data-name' => $value['grado'])
			);

			$columns[] = array_merge($arr,array(array('data' =>$links, 'align' => 'center')));
		}
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => count($columns[0]),'data' => ''));
	}
	echo $this->table->generate($columns);
?>
</div>
<?php $this->load->view('gradoPeriodo/_modalForm'); ?>
<script type="text/javascript">

	function actualizarGradoPeriodo(boton) {
		event.preventDefault();
		turnoID = $(boton).attr('turno');
		profesorID = $(boton).attr('profesor');
		nombre = $(boton).attr('data-name');
		url = $(boton).attr('href');
		$('#modalForm').attr('action',url);
		$('#data-name').html(nombre);
		$('#modalProfesores option[value="'+profesorID+']"').attr('selected',true);
		$('#modalTurnos option[value="'+turnoID+']"').attr('selected',true);
		$('#modalDialog').modal();
	}

	function borrarGradoPeriodo(boton){
		event.preventDefault();
		url = $(boton).attr('href');
		bootbox.confirm('¿Seguro que desea eliminar la asociacion del grado a periodo?, esta accion no se puede deshacer',
			function(result){
				if (result) {
					$.ajax({
						type: 'post',
						url: url,
						dataType: 'json',
						data: {delete:true},
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
			}
		);
	}

</script>