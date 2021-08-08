<div class="table-responsive" id='result'>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->periodo->fields()['periodo_id'], 'width' => '10%'),
		array('data' => $this->periodo->fields()['descripcion'], 'width' => '20%'),
		array('data' => $this->periodo->fields()['fecha_inicio'], 'width' => '10%'),
		array('data' => $this->periodo->fields()['fecha_fin'], 'width' => '10%'),
		array('data' => $this->periodo->fields()['fecha_inicio_inscripcion'], 'width' => '17%'),
		array('data' => $this->periodo->fields()['fecha_fin_inscripcion'], 'width' => '17%'),
		array('data' => $this->estado_periodo->fields()['estado'],'width' => '7%'),
		array('data' => '','width' => '9%'),
	);
	if (count($periodos)>0){
		foreach ($periodos as $key => $value) {
			$value['fecha_inicio'] = date_format(date_create($value['fecha_inicio']),'d-m-Y');
			$value['fecha_fin'] = date_format(date_create($value['fecha_fin']),'d-m-Y');
			$value['fecha_inicio_inscripcion'] = date_format(date_create($value['fecha_inicio_inscripcion']),'d-m-Y');
			$value['fecha_fin_inscripcion'] = date_format(date_create($value['fecha_fin_inscripcion']),'d-m-Y');
			$arr = array_intersect_key($value, array_flip(array(
						'periodo_id','descripcion','fecha_inicio','fecha_fin','fecha_inicio_inscripcion','fecha_fin_inscripcion','estado'
					)
				)
			);
			$links = array();
			$links[] = 	array('title'=>'Ver','icon' => 'fa fa-eye','url' => array('academico','periodo','ver',$value['periodo_id']));
			if ($value['estado_periodo_id'] != 2) {//if periodo finalizado
				$links[] = array('title'=>'Actualizar','icon' => 'fa fa-pencil','url' => ['academico','periodo','actualizar',$value['periodo_id']]);
			}
			
			$columns[] = array_merge($arr,array(array('data' =>my_link($links), 'align' => 'center')));
		}
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => count($columns[0]),'data' => ''));
	}
	echo $this->table->generate($columns);
?>
	<div align="center">
		<ul class="pagination pagination-sm" id="pagination-digg" >
	    	<?php echo $pagNums ?>
		</ul>
	</div>
</div>
<script type="text/javascript">
	$(document).on("click","#pagination-digg li a",function(e){
		e.preventDefault();
		href = $(this).attr("href");
		showPleaseWait('Buscando','info');
		$('#contenido').load(href+' #result',
			function(response,status,xhr){
				if (xhr.status == 0) {
					messageinfo('Imposible conectar con el servidor',false);
				}
				hidePleaseWait();
			}
		)
	})
</script>