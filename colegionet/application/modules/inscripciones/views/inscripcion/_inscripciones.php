<div class="table-responsive" id='result'>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Periodo', 'width' => '20%'),
		array('data' => 'Matricula', 'width' => '10%'),
		array('data' => 'identificacion', 'width' => '10%'),
		array('data' =>	'Nombres','width' => '30%'),
		array('data' => 'Grado', 'width' => '10%'),
		array('data' => 'Fecha','width' => '10%'),
	);
	if (count($inscripciones)>0){
		foreach ($inscripciones as $key => $value) {
			$value['fecha'] = date_format(date_create($value['fecha']),'d-m-Y');
			$value['nombres'] = $value['apellidos']." ".$value['nombres'];
			$arr = array_intersect_key($value, array_flip(array(
						'descripcion','matricula','identificacion','nombres','grado','fecha'
					)
				)
			);
			$links = '';
			$links .= anchor('inscripciones/inscripcion/comprobanteInscripcionPDF/'.$value['inscripcion_id'],'<span class="fa fa-file"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Comprobante inscripcion','target' => '_blank')
			);
			//$links = array();
			//$links[] =	array('title'=>'Ver','icon' => 'fa fa-eye','url' => ['academico','grado','ver',$value['grado_id']]);
			//$links[] =	array('title'=>'Actualizar','icon' => 'fa fa-pencil','url' => ['academico','grado','actualizar',$value['grado_id']]);
			
			$columns[] = array_merge($arr,array(array('data' =>$links, 'align' => 'center')));
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