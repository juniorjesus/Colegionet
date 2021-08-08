<div class="table-responsive">
<?php 
	$template = array(
		'table_open' => '<table class="table table-striped table-condensed table-hover">',
		'thead_open' => '<thead class="thead-header">'
	);
	$this->table->set_template($template);
	$this->table->set_empty('<i>Sin información</i>');

	$columns[0] = array(
		array('data' => 'Roles no asignados al usuario','colspan' => 2),
	);

	if (count($rolesNoAsignados)>0){
		foreach ($rolesNoAsignados as $key => $value) {
			$arr = array_intersect_key($value, array_flip(array(
						'nombre'
					)
				)
			);

			$checkBox = form_checkbox(
				array(
					'name' => 'roles[][rbac_rol_id]',
					'value' => $value['rbac_rol_id']
				)
			);

			$columns[] = array_merge(
							array(array('data' => $checkBox,'align' => 'center','width'=>'10%')),
							$arr
						);
		}
	}else{
		//cuando no hay resultados
		$columns[] = array(array('colspan' => 2,'data' => ''));
	}
	echo form_open('rbac/assignRol/assignRolToUser/'.$userID,array('id' => 'rolesNoAsignado-form'));
	?>
	<p class="alert alert-danger" id='modal-roles-summary' style="display: none"></p>
	<?php
	echo $this->table->generate($columns);
	?>
	<div class="form-group">
		<div>
			<?= form_submit(
					array(
						'name'	=> 'submit',
						'value'	=> 'Asignar',
						'class'	=> 'btn btn-primary btn-sm'
					)
				) ?>
		</div>
	</div>
	<?php
	echo form_close();
	?>
</div>
<script type="text/javascript">
	$('#rolesNoAsignado-form').unbind().on('submit',function(e){
		e.preventDefault();
		url = $(this).attr('action');
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			data: $(this).serialize(),
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
					$('#modal-roles').modal('hide');
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
	});
</script>