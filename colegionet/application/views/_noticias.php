<div id='noticias-result' style="padding: 20px 0;">
	
	<?php foreach ($noticias as $key => $value): ?>
		<div class="relative noticias" style="">
			<div class="col-sm-3 inline" style="vertical-align: middle">
				<img src="<?= base_url('noticias/'.($value->imagen != NULL && $value->imagen != '' ? $value->imagen : 'default.png')) ?>">
			</div>
			<div class="col-sm-9 inline text-justify" style="vertical-align: middle">
				<h3><?= $value->titulo ?></h3>
				<p><?= nl2br($value->contenido) ?></p>
			</div>
			<p style="position: absolute;bottom: -11px;right: 11px;"><?= date_format(date_create($value->fecha_creacion),'d-m-Y') ?></p>
		</div>
	<?php endforeach ?>
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
		//showPleaseWait('Buscando','info');
		$('#contenido-noticias').load(href+' #noticias-result',
			function(response,status,xhr){
				if (xhr.status == 0) {
					messageinfo('Imposible conectar con el servidor',false);
				}
				//hidePleaseWait();
			}
		)
	})
</script>
