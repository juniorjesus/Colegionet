<div class="table-responsive" id='result'>
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => $this->rbac_user->fields()['rbac_user_id'], 'width' => '10%'),
		array('data' => $this->rbac_user->fields()['usuario'], 'width' => '20%'),
		array('data' => $this->rbac_user->fields()['email'], 'width' => '40%'),
		array('data' => $this->rbac_user->fields()['activo'], 'width' => '10%'),
		array('data' => $this->rbac_user->fields()['admin'], 'width' => '10%'),
		array('data' => '','width' => '10%'),
	);
	if (count($usuarios)>0){
		foreach ($usuarios as $key => $value) {
			$value['activo'] = $value['activo'] ? 'activo' : 'inactivo';
			$value['admin'] = $value['admin'] ? 'Si' : '';
			$arr = array_intersect_key($value, array_flip(array(
						'rbac_user_id','usuario','email','activo','admin'
					)
				)
			);
			$links = '';
			$links .= anchor('rbac/user/ver/'.$value['rbac_user_id'],'<span class="fa fa-eye"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Ver rol')
			);
			$links .= anchor('rbac/user/actualizar/'.$value['rbac_user_id'],'<span class="fa fa-pencil"></span>',
				array('style' => 'color: inherit;padding: 2px;','title' => 'Actualizar')
			);
		//	$links .= anchor('rbac/assignItem/managementItemOfRol/'.$value['rbac_rol_id'],'<span class="fa fa-plus"></span>',
		//		array('style' => 'color: inherit;padding: 2px;','title' => 'Agregar Permisos')
		//	);

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