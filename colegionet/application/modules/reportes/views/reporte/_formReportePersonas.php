<?php 
$personas = array(
	'' => 'Seleccione',
	'1' => 'Estudiantes',
	'2'	=> 'Profesores',
	'3' => 'Representantes'
);
$personasExtra = array(
	'class' => 'form-control input-sm',
	'id'	=> 'persona',
	'required' => 'required'
);
?>
<div class="col-sm-6 inline">
	<?= form_open('reportes/reporte/reportePersonasPDF',array('class' => 'form-horizontal')) ?>
	<div class="form-group">
		<?= form_label('Tipo','',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-sm-6">
			<?= form_dropdown('persona[tipo]',$personas,'',$personasExtra) ?>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-6 col-sm-offset-4">
			<?php 
			 echo anchor('','Obtener',
					array('class' => 'btn btn-primary btn-sm','id' => 'obtener-button'));
			?>
		</div>
	</div>
	<?= form_close() ?>
</div>
<script type="text/javascript">
	$('#obtener-button').on('click',function(e){
		e.preventDefault();
		periodoID = $('#periodo').val();
		if (periodoID) {
			window.open('<?= site_url('reportes/reporte/reporteInscritosPDF/') ?>'+periodoID);
		}
	})
</script>