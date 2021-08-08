<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Indicador extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','ver'),
		'filter' => array('crear','leer','actualizar','ver'),
		'navs' => array('leer'=>'Indicadores Eval.'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('leer'),
			'GET'	=> array('leer'),
			'POST'	=> array('crear','actualizar')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('indicador_model','indicador');
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
			$data = $this->input->get('indicador');
			if ($data['indicador_id'] != '') {
				$busqueda['like'][0]['indicador_id'] = $data['indicador_id'];
			}
			if ($data['indicador'] != '') {
				$busqueda['like'][0]['indicador'] = $data['indicador'];
			}
		}
		$totalIndicadores = $this->indicador->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('academico/indicador/leer');
		$config['total_rows'] = $totalIndicadores;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$indicadores = $this->indicador->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Periodos'));
		$this->load->view('indicador/leer',array(
				'indicadores' => $indicadores,
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
			$indicador = new $this->indicador;
			$data = $this->input->post('indicador');
			$this->form_validation->set_data($data);
			
			$indicador->rules();
		
			if ($this->form_validation->run() == TRUE) {					
				$indicador->indicador = $data['indicador'];
				
				if ($indicador->saveValues()) {
					$this->session->set_flashdata('success','Indicador creado satisfactoriamente');
					redirect('academico/indicador/actualizar/'.$indicador->indicador_id,'refresh');
				} else {
					$mensaje[] = "Ha ocurrido un error al crear el grado";
				}
			}
		}
		$this->load->view('header',array('title'=>'Registrar indicador'));
		$this->load->view('indicador/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param $indicadorID int, id del indicador
	* actualiza los datos de un indicador
	*/
	public function actualizar($indicadorID){
		$mensaje = array();
		$indicador = new $this->indicador($indicadorID);
		if ($indicador->indicador_id != NULL) {
			if ($this->input->post()) {
				
				$data = $this->input->post('indicador');
				$this->form_validation->set_data($data);
				$indicador->rules();
				
				if ($this->form_validation->run() == TRUE) {
					
					$indicador->indicador = $data['indicador'];

					if ($indicador->updateValues()) {
						$this->session->set_flashdata('success','Indicador actualizado satisfactoriamente');
						redirect('academico/indicador/actualizar/'.$indicador->indicador_id,'refresh');
					}else{
						$mensaje[] = "Ha ocurrido un error al actualizar el indicador";
					}
				}
			}
			$this->load->view('header',array('title'=>'Actualizar grado'));
			$this->load->view('indicador/actualizar',array(
					'indicador' => $indicador,
					'mensaje' => $mensaje,
				)
			);
		}else {
			show_404();
		}
	}

	/**
	* @param $indicadorID int, id del indicador
	* muestra los datos de un indicador
	*/
	public function ver($indicadorID){
		$indicador = new $this->indicador($indicadorID);
		if ($indicador->indicador_id != NULL) {
			$this->load->view('header',array('title'=>'Ver indicador'));
			$this->load->view('indicador/ver',array(
					'indicador' => $indicador,
				)
			);
		} else {
			show_404();
		}
	}
}