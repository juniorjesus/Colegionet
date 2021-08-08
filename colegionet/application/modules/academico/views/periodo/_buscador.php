<?php 
$periodo = array(
	'name' => 'periodo[periodo_id]',
	'class' => 'form-control',
	'placeholder' => 'Periodo ID'
);
$descripcion = array(
	'name' => 'periodo[descripcion]',
	'class' => 'form-control',
	'placeholder' => 'Descripcion'
);
$estadoExtra = array(
	'class' => 'form-control',
	'placeholder' => 'Estados'
);
$estadosData = $this->estado_periodo->getData()->result_array();
$estados = array('' => 'Estado');
foreach ($estadosData as $key => $value) {
	$estados[$value['estado_periodo_id']] = $value['estado'];
}

$submit = array(
	'name'	=> 'submit',
	'id'	=> 'login',
	'value'	=> 'Buscar',
	'title' => 'Buscar',
	//'data-toggle' => 'tooltip',
	'class'	=> 'btn btn-primary btn-sm'
);
?>

<?php echo form_open('academico/periodo/leer',array('id' =>'form-buscador','method' => 'get')); ?>
	<div class="form-inline input-group-sm">
		<?php echo form_input($periodo) ?>
		<?php echo form_input($descripcion) ?>
		<?= form_dropdown('periodo[estado_periodo_id]',$estados,'',$estadoExtra) ?>
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