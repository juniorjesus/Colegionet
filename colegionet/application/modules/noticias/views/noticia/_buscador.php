<?php 
$noticiaID = array(
	'name' => 'noticia[noticia_id]',
	'class' => 'form-control',
	'placeholder' => 'Noticia ID'
);
$titulo = array(
	'name' => 'noticia[titulo]',
	'class' => 'form-control',
	'placeholder' => 'Titulo'
);

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'login',
	'value'	=> 'Buscar',
	'title' => 'Buscar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);
?>

<?php echo form_open('noticias/noticia/leer',array('id' =>'form-buscador','method' => 'get')); ?>
	<div class="form-inline input-group-sm">
		<?php echo form_input($noticiaID) ?>
		<?php echo form_input($titulo) ?>
		<?php echo form_submit($submit) ?>
	</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	$('#form-buscador').on('submit',function(e){
		e.preventDefault();
		url = $(this).attr('action');
		showPleaseWait('Cargando...','info');
		$('#contenido').load(url+' #result',$(this).serialize(),
			function(response,status,xhr){
				if(xhr.status==0){
                    messageinfo('Imposible conectar con el servidor',false);
                }
				$('[data-toggle="tooltip"]').tooltip();
				hidePleaseWait();
			});
	});
</script>