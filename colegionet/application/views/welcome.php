<div class="fullscreen boxes" style="padding-top: 50px;">
	<div class="container" style="display: flex; height: 100%;">
		<div style="margin: auto; color: black; text-shadow: white;" class="col-xs-12 text-center">
			<h1>Centro Educativo Comunitario Fe y alegria</h1>
			<img style="width: 250px;" src="<?= base_url('img/logo2.png') ?>">
		</div>
	</div>
</div>
<div class="fullscreen boxes" style="/*background-image: url('<?= base_url('img/back-1.jpg') ?>'); box-shadow: 0px -10px 50px;*/">
	<div class="container" style="display: flex; height: 100%;">
		<div style="margin: auto" class="col-xs-12">
			<h2>Informacion reciente</h2>
			<div style="margin: auto;" id='contenido-noticias'>
			<?php 
				$this->load->view('_noticias',array('noticias' => $noticias,'pagNums' => $pagNums));
			?>
			</div>
		</div>
	</div>
</div>

<?php if ($this->session->flashdata('error')): ?>
	<script type="text/javascript">
		success = false;
		message = '<?= $this->session->flashdata('error') ?>';
		messageinfo(message,success);
	</script>
<?php endif ?>
