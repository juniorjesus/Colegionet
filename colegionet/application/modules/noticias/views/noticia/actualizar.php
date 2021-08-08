<div class="container">
	<div class="myheader">
		<h1>Actualizar noticia</h1>
	</div>
	<div class="mybody relative">
		<?php echo anchor('noticias/noticia/leer','<span class="fa fa-arrow-left"></span> Volver',array('class'=>'btn btn-default btn-sm')) ?>
		<?php if ($noticia->publicado): ?>
			<?php echo anchor('noticias/noticia/inactivar/'.$noticia->noticia_id,'<span class="fa fa-pause"></span> Inactivar publicacion',
							array('class'=>'btn btn-default btn-sm','id' => 'boton-accion')) ?>
		<?php else: ?>
			<?php echo anchor('noticias/noticia/activar/'.$noticia->noticia_id,'<span class="fa fa-play"></span> Activar publicacion',
							array('class'=>'btn btn-default btn-sm','id' => 'boton-accion')) ?>
		<?php endif ?>
		<hr>
		<h3><b>Noticia:</b> <?= $noticia->noticia_id ?></h3>
		<?php $this->load->view('_form',array('mensaje' => $mensaje,'datos' => $noticia)) ?>
	</div>
</div>


<?php if ($this->session->flashdata('success')): ?>
	<script type="text/javascript">
		success = true;
		message = '<?= $this->session->flashdata('success') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>

<script type="text/javascript">
	$('#boton-accion').on('click',function(e){
		e.preventDefault();
		url = $(this).attr('href');
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			data: {accion:true},
			beforeSend: function(){
				showPleaseWait('Verificando','info');
			},
			complete: function(){
				hidePleaseWait();
			},
			success: function(data){ 
				if (data.success) {
					messageinfo(data.mensaje,data.success);
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
		});
	});
</script>
