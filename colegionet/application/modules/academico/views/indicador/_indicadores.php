<div class="table-responsive" id='result'>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->indicador->fields()['indicador_id'], 'width' => '10%'),
		array('data' => $this->indicador->fields()['indicador'], 'width' => '60%'),
		array('data' => '','width' => '10%'),
	);
	if (count($indicadores)>0){
		foreach ($indicadores as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'indicador_id','indicador'
					)
				)
			);
			$links = array();
			$links[] =	array('title'=>'Ver','icon' => 'fa fa-eye','url' => ['academico','indicador','ver',$value['indicador_id']]);
			$links[] =	array('title'=>'Actualizar','icon' => 'fa fa-pencil','url' => ['academico','indicador','actualizar',$value['indicador_id']]);
			
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