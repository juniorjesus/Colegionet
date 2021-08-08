<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Representante extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','borrar','ver'),
		'filter' => array('crear','leer','actualizar','borrar','ver'),
		'navs' => array('leer'=>'Estudiantes'),
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
		$this->load->model('representante_model','representante');
		$this->load->model('persona_model','persona');
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
			$data = $this->input->get('representante');
			if ($data['identificacion'] != '') {
				$busqueda['like'][0]['identificacion'] = $data['identificacion'];
			}
			if ($data['nombres'] != '') {
				$busqueda['like'][0]['nombres'] = $data['nombres'];
			}
			if ($data['apellidos'] != '') {
				$busqueda['like'][0]['apellidos'] = $data['apellidos'];
			}
		}
		$totalRepresentantes = $this->representante->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('personas/representante/leer');
		$config['total_rows'] = $totalRepresentantes;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$representantes = $this->representante->getData($busqueda,true)->result_array();
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Representantes'));
		$this->load->view('representante/leer',array(
				'representantes' => $representantes,
				'pagNums'	=> $pagNums,
			)
		);
	}

	/**
	* @param none
	* crea un estudiante
	*/
	public function crear(){
		$mensaje = array();
		if ($this->input->post()) {
			$persona = new $this->persona;
			$data = $this->input->post('persona');
			$this->form_validation->set_data($data);
			$persona->rules('crear');
			if ($this->form_validation->run() == TRUE) {
				$this->db->trans_begin();
				$persona->identificacion = $data['identificacion'] != '' ? $data['identificacion'] : null;
				$persona->nombres = $data['nombres'];
				$persona->apellidos = $data['apellidos'];
				$persona->sexo = $data['sexo'];
				$persona->fecha_nac = $data['fecha_nac'];
				$persona->telefono_hab = $data['telefono_hab'];
				$persona->telefono_mov = $data['telefono_mov'];
				if ($persona->saveValues()) {
					$representante = new $this->representante;
					$representante->persona_id = $persona->persona_id;
					if ($representante->saveValues()) {
						$this->db->trans_commit();
						$this->session->set_flashdata('success','Representante creado satisfactoriamente');
						redirect('personas/representante/actualizar/'.$representante->representante_id,'refresh');
					} else {
						$this->db->trans_rollback();
						$mensaje[] = "Ha ocurrido un error al crear al representante";
					}
				} else {
					$this->db->trans_rollback();
					$mensaje[] = "Ha ocurrido un error al crear la persona";
				}
				$this->db->trans_complete();		
			}
		}
		$this->load->view('header',array('title'=>'Registrar representante'));
		$this->load->view('representante/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param $representanteID int, id del reoresentante
	* actualiza los datos personales de un representante
	*/
	public function actualizar($representanteID){
		$mensaje = array();
		$representante = $this->representante->getData(array(
				'where' => array(array('representante_id' => $representanteID))
			),true)->row();
		if ($representante != NULL) {
			if ($this->input->post()) {
				$persona = new $this->persona();
				$data = $this->input->post('persona');
				$this->form_validation->set_data($data);
				$persona->rules('actualizar');
				if ($this->form_validation->run() == TRUE) {
					$persona->persona_id = $representante->persona_id;
					$persona->identificacion = isset($data['identificacion']) && $data['identificacion'] != '' 
													? $data['identificacion'] : $representante->identificacion;
					$persona->nombres = $data['nombres'];
					$persona->apellidos = $data['apellidos'];
					$persona->sexo = $data['sexo'];
					$persona->fecha_nac = $data['fecha_nac'];
					$persona->telefono_hab = $data['telefono_hab'];
					$persona->telefono_mov = $data['telefono_mov'];
					if ($persona->updateValues()) {
						$this->session->set_flashdata('success','Estudiante actualizado satisfactoriamente');
						redirect('personas/representante/actualizar/'.$representante->representante_id,'refresh');
					}else{
						$mensaje[] = "Ha ocurrido un error al actualizar al representante";
					}
				}
			}
			$this->load->view('header',array('title'=>'Actualizar representante'));
			$this->load->view('representante/actualizar',array(
					'representante' => $representante,
					'mensaje' => $mensaje,
				)
			);
		}else {
			show_404();
		}
	}

	/**
	* @param $estudianteID int, id del representante
	* muestra los datos de un representante
	*/
	public function ver($representanteID){
		$representante = $this->representante->getData(array(
				'where' => array(array('representante_id' => $representanteID))
			),true)->row();
		if ($representante != NULL) {
			$this->load->view('header',array('title'=>'Ver representante'));
			$this->load->view('representante/ver',array(
					'representante' => $representante,
				)
			);
		} else {
			show_404();
		}
	}
}