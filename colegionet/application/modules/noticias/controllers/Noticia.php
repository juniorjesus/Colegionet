<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Noticia extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','borrar','activar','inactivar'),
		'filter' => array('crear','leer','actualizar','borrar','activar','inactivar'),
		'navs' => array('leer'=>'Noticias'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('leer','borrar','activar','inactivar'),
			'GET'	=> array('leer'),
			'POST'	=> array('crear','actualizar','borrar','activar','inactivar')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('noticia_model','noticia');
		//$this->load->model('estado_periodo_model','estado_periodo');
	}

	public function _remap($method,$params = array()){
		$method = $method == 'index' ? 'leer' : $method;
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this,$method), $params);
		}
		show_404();
	}

	/**
	* @param $page int numero de offset para query de paginacion
	* funcion leer = index
	*/
	public function leer($page = '0'){
		$busqueda = array();
		if ($this->input->is_ajax_request() && $this->input->get()) {
			$data = $this->input->get('noticia');
			if ($data['noticia_id'] != '') {
				$busqueda['like'][0]['noticia_id'] = $data['noticia_id'];
			}
			if ($data['titulo'] != '') {
				$busqueda['like'][0]['titulo'] = $data['titulo'];
			}
		}
		$totalNoticias = $this->noticia->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('noticias/noticia/leer');
		$config['total_rows'] = $totalNoticias;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$noticias = $this->noticia->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Periodos'));
		$this->load->view('noticia/leer',array(
				'noticias' => $noticias,
				'pagNums'	=> $pagNums,
			)
		);
	}

	/**
	* @param none
	* crea un grado
	*/
	public function crear(){
		$mensaje = array();
		if ($this->input->post()) {
			$noticia = new $this->noticia;
			$data = $this->input->post('noticia');

			$noticia->titulo = $data['titulo'];
			$noticia->contenido = $data['contenido'];
			$noticia->fecha_creacion = date('Y-m-d');
			$noticia->rbac_user_id = $this->session->rbacUserID;
			$this->form_validation->set_data((array)$noticia);
			$noticia->rules();

			if ($this->form_validation->run() == TRUE) {
				$this->db->trans_begin();
				if ($noticia->saveValues()) {
					$flag = TRUE;
					if ($_FILES['imagen']['name'] != '' && $_FILES['imagen']['name'] != NULL ) {

						$config['upload_path']		= './noticias/';
						$config['allowed_types']	= 'jpg|png';
						$config['overwrite']		= TRUE;
						$config['file_name']		= $noticia->noticia_id;
						$config['max_size']			= 200;
						$config['max_width']		= 1024;
						$config['max_height']		= 768;	

						$this->load->library('upload',$config);
						if ($this->upload->do_upload('imagen')) {
							$noticia->imagen = $this->upload->data('file_name');
							if ($noticia->updateValues()) {
								$flag = TRUE;
								$this->db->trans_commit();
								$this->session->set_flashdata('success','Noticia creada satisfactoriamente');
								redirect('noticias/noticia/actualizar/'.$noticia->noticia_id,'refresh');
							} else {
								$flag = FALSE;
								//$this->db->trans_rollback();
								$mensaje[] = "Ha ocurrido un error cargando la imagen";	
							}
						} else {
							$flag = FALSE;
							//$this->db->trans_rollback();
							$mensaje[] = "Ha ocurrido un error cargando la imagen".$this->upload->display_errors();
						}
					}

					if ($flag) {
						$this->db->trans_commit();
						$this->session->set_flashdata('success','Noticia creada satisfactoriamente');
						redirect('noticias/noticia/actualizar/'.$noticia->noticia_id,'refresh');
					} else {
						$this->db->trans_rollback();
					}
				}else{
					$this->db->trans_rollback();
					$mensaje[] = "Ha ocurrido un al crear la noticia";
				}
				$this->db->trans_complete();
			}
		}
		$this->load->view('header',array('title'=>'Registrar noticia'));
		$this->load->view('noticia/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param $gradoID int, id del grado
	* actualiza los datos de un grado
	*/
	public function actualizar($noticiaID){
		$mensaje = array();
		$noticia = new $this->noticia($noticiaID);
		if ($noticia->noticia_id != NULL) {
			if ($this->input->post()) {
				
				$data = $this->input->post('noticia');

				$noticia->titulo = $data['titulo'];
				$noticia->contenido = $data['contenido'];
				
				$this->form_validation->set_data((array)$noticia);
				$noticia->rules();
						
				if ($this->form_validation->run() == TRUE) {
					$this->db->trans_begin();
					$flag = TRUE;
					if ($_FILES['imagen']['name'] != '' && $_FILES['imagen']['name'] != NULL ) {
						
						$config['upload_path']		= './noticias/';
						$config['allowed_types']	= 'jpg|png';
						$config['overwrite']		= TRUE;
						$config['file_name']		= $noticia->noticia_id;
						$config['max_size']			= 200;
						$config['max_width']		= 1024;
						$config['max_height']		= 768;	

						$this->load->library('upload',$config);
						if ($this->upload->do_upload('imagen')) {
							$noticia->imagen = $this->upload->data('file_name');
							if ($noticia->updateValues()) {
								$flag = TRUE;
								$this->db->trans_commit();
								$this->session->set_flashdata('success','Noticia actualizada satisfactoriamente');
								redirect('noticias/noticia/actualizar/'.$noticia->noticia_id,'refresh');
							} else {
								$flag = FALSE;
								//$this->db->trans_rollback();
								$mensaje[] = "Ha ocurrido un error cargando la imagen";	
							}
						} else {
							$flag = FALSE;
							//$this->db->trans_rollback();
							$mensaje[] = "Ha ocurrido un error cargando la imagen".$this->upload->display_errors();
						}
					}
					if ($flag) {
						if ($noticia->updateValues()) {
							$this->db->trans_commit();
							$this->session->set_flashdata('success','Noticia actualizada satisfactoriamente');
							redirect('noticias/noticia/actualizar/'.$noticia->noticia_id,'refresh');
						}else{
							$this->db->trans_rollback();
							$mensaje[] = "Ha ocurrido un error al actualizar la noticia";
						}
					}
					$this->db->trans_complete();
				}
			}
			$this->load->view('header',array('title'=>'Actualizar noticia'));
			$this->load->view('noticia/actualizar',array(
					'noticia' => $noticia,
					'mensaje' => $mensaje,
				)
			);
		}else {
			show_404();
		}
	}

	public function activar($noticiaID){
		$result['success'] = TRUE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$noticia = new $this->noticia($noticiaID);
			if ($noticia->noticia_id != NULL) {
				$noticia->publicado = 1;
				if ($noticia->updateValues()) {
					$result['success'] = TRUE;
					$result['mensaje'] = 'Activado satisfactoriamente';
				} else {
					$result['mensajeError'] = 'Ha ocurrido un error al activar la noticia';
				}
			} else {
				$result['mensajeError'] = 'Noticia inexistente';
			}
		}
		echo json_encode($result);
	}

	public function inactivar($noticiaID){
		$result['success'] = TRUE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$noticia = new $this->noticia($noticiaID);
			if ($noticia->noticia_id != NULL) {
				$noticia->publicado = 0;
				if ($noticia->updateValues()) {
					$result['success'] = TRUE;
					$result['mensaje'] = 'Activado satisfactoriamente';
				} else {
					$result['mensajeError'] = 'Ha ocurrido un error al activar la noticia';
				}
			} else {
				$result['mensajeError'] = 'Noticia inexistente';
			}
		}
		echo json_encode($result);
	}
}