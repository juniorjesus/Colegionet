<div>
	<h3>Datos del estudiante</h3>
	<div class="col-md-4 col-sm-6 col-xs-12 inline">
		<div class="panel panel-primary ">
			<div class="panel-heading">
				<h4><span class="fa fa-address-card"></span><b> Matricula: </b></h4>
				
			</div>
			<div class="panel-body no-padding text-right" style="padding: 0 10px;">
				<h4><b><?= $datosEstudiante->matricula ?></b></h4>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12  inline">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4><span class="fa fa-address-card"></span><b> Identificacion: </b></h4>
			</div>
			<div class="panel-body no-padding text-right" style="padding: 0 10px;">
				<h4><b><?= $datosEstudiante->identificacion != NULL ? $datosEstudiante->identificacion : '<i>Sin informacion</i>' ?></b></h4>
			</div>
		</div>
	</div>
	<br>
	<div class="col-md-4 col-sm-6 col-xs-12 inline">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4><span class="fa fa-address-card"></span><b> Apellidos: </b></h4>
			</div>
			<div class="panel-body no-padding text-right" style="padding: 0 10px;">
				<h4><b><?= $datosEstudiante->apellidos ?></b></h4>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12 inline">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4><span class="fa fa-address-card"></span><b> Nombres: </b></h4>
			</div>
			<div class="panel-body no-padding text-right" style="padding: 0 10px;">
				<h4><b><?= $datosEstudiante->nombres ?></b></h4>
			</div>
		</div>
	</div>
	<hr>
	<p class="alert alert-info">
		Se muestra solo los grados asociados al periodo exceptuando aquellos a los que el alumno ya ha sido inscrito en periodos academicos anteriores
	</p>
		<?php $this->load->view('_formInscripcion',array('datosEstudiante' => $datosEstudiante, 'grados' => $grados,'periodo' => $periodo)) ?>
	
</div>