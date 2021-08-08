<div class="col-md-6 inline" id='contenido' style="vertical-align: top">
	<div id='result'>
		<?php 
		$template = array(
			'table_open' => '<table class="table table-striped table-condensed table-hover">',
			'thead_open' => '<thead class="thead-header">'
		);
		$this->table->set_template($template);
		$this->table->set_empty('<i>Sin información</i>');

		$linkADD = '';
		$linkADD .= anchor('rbac/user/obtenerRolesNoAsignados/'.$userID,'<span class="fa fa-plus"></span> Agregar',
			array('style' => 'color: inherit;padding: 2px;','title' => 'Agregar','onclick' => 'agregarRoles(this)')
		);
		$columns[0] = array(
			array('data' => 'Roles asignado', 'width' => '80%'),
			array('data' => $linkADD)
		);
		if (count($roles) > 0) {
			foreach ($roles as $key => $value) {
				$links = '';
				$links .= anchor('rbac/assignRol/unassignRolToUser/'.$value['rbac_user_rol_id'],'<span class="fa fa-close"></span>',
					array('style' => 'color: inherit;padding: 2px;','title' => 'Eliminar','onclick' => 'eliminarRol(this)')
				);
				$columns[] =  array(
					array('data' => $value['nombre'],'width' => '80%'),
					array('data' => $links,'class' => 'text-center')
				);
			}
		} else {
			$columns[] = array(array('data' => '', 'colspan' =>2));
		}

		echo $this->table->generate($columns);

		?>
	</div>
</div>
<?php $this->load->view('user/_modalRolesPorAsignar') ?>
<script type="text/javascript">
	function agregarRoles(link) {
		event.preventDefault();
		url = $(link).attr('href');
		$.ajax({
			type: 'post',
			url: url,
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
					$('#modal-roles-body').html(data.table);
					$('#modal-roles').on('show.bs.modal', function () {
						$('.modal-content').css('max-height',$( window ).height()*0.9);
						$('.modal-content').css('overflow-y','auto');
					});
					$('#modal-roles').modal();
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

	function eliminarRol(link){
		event.preventDefault();
		url = $(link).attr('href');
		bootbox.confirm('¿Seguro que desea eliminar este rol?, esta accion no se puede deshacer',
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
								myurl = '<?= site_url('rbac/user/actualizar/'.$userID) ?>';
								$('#contenido').load(myurl+' #result',
									function(response,status,xhr){
										if(xhr.status==0){
											messageinfo('Imposible conectar con el servidor',false);
										}
									}
								);
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
</script>