<?php 
$periodos = array('' => 'Periodo');
$dataPeriodo = $this->periodo->getData()->result();
foreach ($dataPeriodo as $key => $value) {
	$periodos[$value->periodo_id] = $value->descripcion;
}
$periodoExtra = array(
	'class' => 'form-control input-sm',
	'id'	=> 'periodo',
);

$matricula = array(
	'name' => 'inscripcion[matricula]',
	'class' => 'form-control',
	'placeholder' => 'Matricula'
);
$identificacion = array(
	'name' => 'inscripcion[identificacion]',
	'class' => 'form-control',
	'placeholder' => 'identificacion',
);
$grados = array('' => 'Grado');
$dataGrado = $this->grado->getData()->result();
foreach ($dataGrado as $key => $value) {
	$grados[$value->grado_id] = $value->grado;
}
$gradoExtra = array(
	'class' => 'form-control input-sm',
	'id'	=> 'grado',
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

<?php echo form_open('inscripciones/inscripcion/leer',array('id' =>'form-buscador','method' => 'get')); ?>
	<div class="form-inline input-group-sm">
		<?= form_dropdown('inscripcion[periodo_id]',$periodos,'',$periodoExtra); ?>
		<?php echo form_input($matricula) ?>
		<?php echo form_input($identificacion) ?>
		<?= form_dropdown('inscripcion[grado_id]',$grados,'',$gradoExtra); ?>
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