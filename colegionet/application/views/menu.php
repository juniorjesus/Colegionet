<div class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type='button' class="navbar-toggle collapsed" data-toggle='collapse' data-target='#my-menu'>
				<span class="fa fa-home"></span>
			</button>
		</div>

		<div class="collapse navbar-collapse" id='my-menu'>
			<ul class="nav navbar-nav">
				<li><a href="<?= base_url() ?>"><span class="fa fa-home"></span> Inicio</a></li>
				<?php if ($this->session->userdata('login')): ?>
					<?php 
						$arrMenu = $this->session->userdata('menu');
						$html = '';
						foreach ($arrMenu as $menu => $links) {
							$class1 = $menu == $this->uri->segment(1) ? 'active' : NULL;
							$html .= '<li class="dropdown '.$class1.'">';
							$html .= '<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">';
							$html .= ucwords($menu) . ' <span class="caret"></span></a>';
							$html .= '<ul Class="dropdown-menu">';
							foreach ($links as $key => $data) {
								$uri = array_values(array_intersect_key($this->uri->segment_array(),array_flip(array(1,2))));
								//$dataMain = array_values($data['dataMain']);
								$class2 = NULL;//$dataMain == $uri ? 'active' : null;
								$html .= '<li class="'.$class2.'"><a href="'.$data['url'].'">'.$data['text'].'</a></li>';
							}
							$html .= "</ul></li>";
						}
						echo $html;
					?>
				<?php endif ?>
			</ul>
			<!--user nav-->
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<?php if ($this->session->userdata('login')): ?>
						<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">
							<?= $this->session->userdata('username') ?> <span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li>
								<a href="<?= site_url('rbac/auth/logout') ?>">Salir</a>
							</li>
						</ul>
					<?php else: ?>
						<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Ingresar <span class="caret"></span></a>
						<ul class="dropdown-menu" style="min-width: 250px">
							<li>
								<?php $this->load->view('rbac/auth/_formLogin') ?>
							</li>
						</ul>

					<?php endif ?>
				</li>
			</ul>
			<!--end-->
		</div>
	</div>
</div>
<?php if (!$this->session->userdata('login')): ?>
	<?php $this->load->view('rbac/user/_formCreateUser',array('tipo' => 'estudiante')) ?>
<?php endif ?>