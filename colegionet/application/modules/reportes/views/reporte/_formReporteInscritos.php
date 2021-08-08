<?php 
$dataPeriodos = $this->periodo->getData()->result();
$periodos = array('' => 'Seleccione');
foreach ($dataPeriodos as $key => $value) {
	$periodos[$value->periodo_id] = $value->descripcion. " [ ".$value->anio_inicio." - ".$value->anio_fin." ]";
}
$periodosExtra = array(
	'class' => 'form-control input-sm',
	'id'	=> 'periodo',
	'required' => 'required'
);
?>
<div class="col-sm-6 inline">
	<?= form_open('reportes/reporte/reporteInscritosPDF',array('class' => 'form-horizontal')) ?>
	<div class="form-group">
		<?= form_label('Periodos','',array('class' => 'control-label col-sm-4')) ?>
		<div class="col-sm-6">
			<?= form_dropdown('periodo[periodo_id]',$periodos,'',$periodosExtra) ?>
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