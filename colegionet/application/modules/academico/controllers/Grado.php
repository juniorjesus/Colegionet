<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grado extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','borrar','ver'),
		'filter' => array('crear','leer','actualizar','borrar','ver'),
		'navs' => array('leer'=>'Periodo'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('leer','borrar'),
			'GET'	=> array('leer'),
			'POST'	=> array('crear','actualizar','borrar')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('grado_model','grado');
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
			$data = $this->input->get('grado');
			if ($data['grado_id'] != '') {
				$busqueda['like'][0]['grado_id'] = $data['grado_id'];
			}
			if ($data['grado'] != '') {
				$busqueda['like'][0]['grado'] = $data['grado'];
			}
			if ($data['numero'] != '') {
				$busqueda['like'][0]['numero'] = $data['numero'];
			}
			if ($data['seccion'] != '') {
				$busqueda['like'][0]['seccion'] = $data['seccion'];
			}
		}
		$totalGrados = $this->grado->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('academico/periodo/leer');
		$config['total_rows'] = $totalGrados;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$grados = $this->grado->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Periodos'));
		$this->load->view('grado/leer',array(
				'grados' => $grados,
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
			$grado = new $this->grado;
			$data = $this->input->post('grado');
			$this->form_validation->set_data($data);
			$grado->rules();
			
			$siGradoExiste = $this->grado->getData(array(
					'where' => array(array('numero' => $data['numero'], 'seccion' => $data['seccion']))
				))->row();

			if ($siGradoExiste == NULL) {
				if ($this->form_validation->run() == TRUE) {					
					$grado->grado = $data['grado'];
					$grado->numero = $data['numero'];
					$grado->seccion = $data['seccion'];
					
					if ($grado->saveValues()) {
						$this->session->set_flashdata('success','Grado creado satisfactoriamente');
						redirect('academico/grado/actualizar/'.$grado->grado_id,'refresh');
					} else {
						$mensaje[] = "Ha ocurrido un error al crear el grado";
					}
				}
			} else {
				$mensaje[] = "El numero y la seccion que intenta crear ya existe";
			}
		}
		$this->load->view('header',array('title'=>'Registrar grado'));
		$this->load->view('grado/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param $gradoID int, id del grado
	* actualiza los datos de un grado
	*/
	public function actualizar($gradoID){
		$mensaje = array();
		$grado = new $this->grado($gradoID);
		if ($grado->grado_id != NULL) {
			if ($this->input->post()) {
				
				$data = $this->input->post('grado');
				$this->form_validation->set_data($data);
				$grado->rules();
				
				$siGradoExiste = $this->grado->getData(array(
					'where' => array(array('numero' => $data['numero'], 'seccion' => $data['seccion']))
				))->row();
				
				if ($siGradoExiste!= NULL && $siGradoExiste->grado_id == $grado->grado_id) {
					if ($this->form_validation->run() == TRUE) {
						
						$grado->grado = $data['grado'];
						$grado->numero = $data['numero'];
						$grado->seccion = $data['seccion'];

						if ($grado->updateValues()) {
							$this->session->set_flashdata('success','Grado actualizado satisfactoriamente');
							redirect('academico/grado/actualizar/'.$grado->grado_id,'refresh');
						}else{
							$mensaje[] = "Ha ocurrido un error al actualizar el periodo";
						}
					}
				}else{
					$mensaje[] = "El numero y la seccion ya existe";
				}
			}
			$this->load->view('header',array('title'=>'Actualizar grado'));
			$this->load->view('grado/actualizar',array(
					'grado' => $grado,
					'mensaje' => $mensaje,
				)
			);
		}else {
			show_404();
		}
	}

	/**
	* @param $gradoID int, id del grado
	* muestra los datos de un grado
	*/
	public function ver($gradoID){
		$grado = new $this->grado($gradoID);
		if ($grado->grado_id != NULL) {
			$this->load->view('header',array('title'=>'Ver grado'));
			$this->load->view('grado/ver',array(
					'grado' => $grado,
				)
			);
		} else {
			show_404();
		}
	}
}