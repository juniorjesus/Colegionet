<div class="table-responsive col-md-6 inline" id='gradosPeriodo' style="vertical-align: top;">
	<h4>Lapsos del periodo</h4>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->lapso->fields()['numero'],'width' => '10%'),
		array('data' => $this->lapso->fields()['lapso_fecha_inicio'],'width' => '10%'),
		array('data' => $this->lapso->fields()['lapso_fecha_fin'],'width' => '10%'),
		array('data' => '','width' => '10%'),
	);
	if (count($lapsos)>0){
		foreach ($lapsos as $key => $value) {
			$value['lapso_fecha_inicio'] = date_format(date_create($value['lapso_fecha_inicio']),'d-m-Y');
			$value['lapso_fecha_fin'] = date_format(date_create($value['lapso_fecha_fin']),'d-m-Y');
			$arr = array_intersect_key($value, array_flip(array(
						'numero','lapso_fecha_inicio','lapso_fecha_fin'
					)
				)
			);
			$links = '';
			$links .= anchor('academico/lapso/borrar/'.$value['lapso_id'],'<span class="fa fa-close"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Eliminar','onclick' => 'borrarLapso(this)')
			);
			$links .= anchor('academico/lapso/actualizar/'.$value['lapso_id'],'<span class="fa fa-pencil"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Eliminar','onclick' => 'actualizarLapso(this)',
				'fechaIni' => $value['lapso_fecha_inicio'], 'fechaFin' => $value['lapso_fecha_fin'], 'num' => $value['numero'])
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
<?php $this->load->view('lapso/_modalForm') ?>
<script type="text/javascript">
	function actualizarLapso(link){
		event.preventDefault();
		title = 'Actualizar lapso #'+$(link).attr('num');
		fechaIni = $(link).attr('fechaIni');
		fechaFin = $(link).attr('fechaFin');
		url = $(link).attr('href');
		$('#modal-lapso-form').attr('action',url);
		$('#modal-lapso-title').html(title);
		$('#m_lapso_fecha_inicio').val(fechaIni);
		$('#m_lapso_fecha_fin').val(fechaFin);
		$('#modal-lapso').modal();
	}

	function borrarLapso(boton){
		event.preventDefault();
		url = $(boton).attr('href');
		bootbox.confirm('¿Seguro que desea eliminar el lapso?, esta accion no se puede deshacer',
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