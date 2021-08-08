<div class="table-responsive" id='result'>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->noticia->fields()['noticia_id'], 'width' => '10%'),
		array('data' => $this->noticia->fields()['titulo'], 'width' => '40%'),
		array('data' => $this->noticia->fields()['fecha_creacion'], 'width' => '10%'),
		array('data' => 'Estado', 'width' => '10%'),
		array('data' => 'Creado por','width' => '10%'),
		array('data' => '','width' => '10%'),
	);
	if (count($noticias)>0){
		foreach ($noticias as $key => $value) {
			//$publicado = $value['publicado'];
			$value['publicado'] = $value['publicado'] ? 'Publicado' : 'inactivo';
			$arr = array_intersect_key($value, array_flip(array(
						'noticia_id','titulo','fecha_creacion','publicado','usuario'
					)
				)
			);
			$links = '';
			$links .= anchor('noticias/noticia/actualizar/'.$value['noticia_id'],'<span class="fa fa-pencil"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Actualizar')
			);

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