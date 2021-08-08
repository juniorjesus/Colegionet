<div class="table-responsive">
	<div style="display: table-cell;">
		<ul class="nav nav-pills nav-stacked">
		<?php $class = 'active' ?>
		<?php foreach ($items as $key => $value): ?>
			<li class="<?= $class ?>"><a style="border-radius: 4px 0 0 4px" href="#<?= $key ?>" data-toggle="tab"><?= ucwords($key) ?></a></li>
			<?php $class = '' ?>
		<?php endforeach ?>
		</ul>
	</div>

	<div class="tab-content" style="display: table-cell; width: 100%;background-color: #337ab7; border-radius: 0 10px 10px 0">
	<?php $class = 'active in' ?>
	<?php foreach ($items as $key => $value): ?>
		<div id="<?= $key ?>" class="tab-pane fade <?= $class ?>" style="padding: 15px">
			<?php foreach ($value as $key2 => $value2): ?>
				<div id="<?= $key.$key2.'-content' ?>">
					<div id="<?= $key.$key2.'-result' ?>">
						<h4><?= ucwords($key2) ?></h4>
						<ul class="list-group">
							<?php foreach ($value2 as $key3 => $value3): ?>
								<?php $url = site_url('rbac/assignItem/'.($value3['assigned'] ? 'unassignItemToRol/'.$value3['rbacRolItemID'] : 'assignItemToRol/'.$rolID.'/'.$value3['rbacItemID'] )) ?>
								<li class="list-group-item <?= $value3['assigned'] ? 'list-group-item-success' : ''  ?>">
									<a href="<?= $url ?>" data-main="<?= $key.$key2 ?>" title="<?= $value3['assigned'] ? 'Desasignar' : 'Asignar' ?>" onclick="managementItems(this)">
										<?= $value3['item'] ?>
										<span class="fa fa-<?= $value3['assigned'] ? 'check' : 'plus'  ?>"></span>
									</a>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endforeach ?>
		</div>
		<?php $class = '' ?>
	<?php endforeach ?>
	</div>
</div>
<script type="text/javascript">
	function managementItems(link){
		event.preventDefault();
		dataMain = $(link).data('main');
		url = $(link).attr('href');
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			data: {doSomething:true},
			beforeSend: function(){
				showPleaseWait('Verificando','info');
			},
			complete: function(){
				hidePleaseWait();
			},
			success: function(data){
				if (data.success) {
					messageinfo(data.mensaje,data.success);
					thisUrl = '<?= site_url(uri_string()) ?>';
					$('#'+dataMain+'-content').load(thisUrl+' #'+dataMain+'-result',
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
</script>