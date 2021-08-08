<title>Lista <?= $grado->grado_periodo_id." ".date('d-m-Y h:i:s') ?></title>
<style type="text/css"><?= file_get_contents('css/bootstrap/css/bootstrap.min.css') ?></style>
<style type="text/css"><?= file_get_contents('css/style.css') ?></style>
<style type="text/css">
	h1,h2,h3,h4,h5,p{
		color: black!important;
	}
	table{
		font-size: 10pt;
		margin-bottom: 0;
	}
	#header{
		position:fixed; 
		right:0; 
		left:0; 
		top:-100px;
		text-align: center;
	}
	#footer{
		position: fixed;
		bottom: -30px;
	}
	@page {
		border-bottom: 10px solid black;
		margin-top: 100px;
		margin-bottom: 100px;
	}
	p{
		margin: 0;
	}
</style>
<?php 
	$this->load->view('headerPDF')
?>
<div>
	<h3 class="text-center">Lista estudiantes</h3>
	<p><b>Periodo: </b><?= $periodo->descripcion ?></p>
	<p><b>Grado: </b><?= $grado->grado ?></p>
	<p><b>Numero: </b><?= $grado->numero ?></p>
	<p><b>Seccion: </b><?= $grado->seccion ?></p>
</div>
<hr>
<div>
	<?php 
		$template = array(
			'table_open' => '<table class="table table-striped table-condensed table-hover">',
			'thead_open' => '<thead class="thead-header">'
		);
		$this->table->set_template($template);
		$this->table->set_empty('<i>Sin información</i>');

		$columns[0] = array(
			array('data' => 'Nº','width' => '5%','class' => 'text-center'),
			array('data' => 'Apellidos','width' => '40%'),
			array('data' => 'Nombres','width' => '40%')
		);
		$num = 1;
		if (count($inscripciones) > 0){
			foreach ($inscripciones as $key => $value) {
				$arr = array_intersect_key($value, array_flip(array(
							'apellidos','nombres'
						)
					)
				);
				$columns[] = array_merge(array(array('data' =>$num++, 'align' => 'center')),$arr);
			}
		} else {

		}
		
		echo $this->table->generate($columns);
	?>
</div>