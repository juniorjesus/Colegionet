<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profesor extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','borrar','ver'),
		'filter' => array('crear','leer','actualizar','borrar','ver'),
		'navs' => array('leer'=>'Profesores'),
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
		$this->load->model('profesor_model','profesor');
		$this->load->model('persona_model','persona');
		$this->load->model('rbac/rbac_user_model','rbac_user');
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
			$data = $this->input->get('profesor');
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
		$totalProfesores = $this->profesor->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('personas/profesor/leer');
		$config['total_rows'] = $totalProfesores;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$profesores = $this->profesor->getData($busqueda,true)->result_array();
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Profesores'));
		$this->load->view('profesor/leer',array(
				'profesores' => $profesores,
				'pagNums'	=> $pagNums,
			)
		);
	}

	/**
	* @param none
	* crea un profesor
	*/
	public function crear(){
		$mensaje = array();
		if ($this->input->post()) {
			$persona = new $this->persona;
			$data = $this->input->post('persona');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules('identificacion','identificacion','required');
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
					$profesor = new $this->profesor;
					$profesor->persona_id = $persona->persona_id;
					if ($profesor->saveValues()) {
						$this->db->trans_commit();
						$this->session->set_flashdata('success','Profesor creado satisfactoriamente');
						redirect('personas/profesor/actualizar/'.$profesor->profesor_id,'refresh');
					} else {
						$this->db->trans_rollback();
						$mensaje[] = "Ha ocurrido un error al crear al profesor";
					}
				} else {
					$this->db->trans_rollback();
					$mensaje[] = "Ha ocurrido un error al crear la persona";
				}
				$this->db->trans_complete();		
			}
		}
		$this->load->view('header',array('title'=>'Registrar profesor'));
		$this->load->view('profesor/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param $profesorID int, id del profesor
	* actualiza los datos personales de un profesor
	*/
	public function actualizar($profesorID){
		$mensaje = array();
		$profesor = $this->profesor->getData(array(
				'where' => array(array('profesor_id' => $profesorID))
			),true)->row();
		if ($profesor != NULL) {
			if ($this->input->post()) {
				$persona = new $this->persona();
				$data = $this->input->post('persona');
				$this->form_validation->set_data($data);
				$persona->rules('actualizar');
				if ($this->form_validation->run() == TRUE) {
					$persona->persona_id = $profesor->persona_id;
					$persona->identificacion = isset($data['identificacion']) && $data['identificacion'] != '' 
													? $data['identificacion'] : $profesor->identificacion;
					$persona->nombres = $data['nombres'];
					$persona->apellidos = $data['apellidos'];
					$persona->sexo = $data['sexo'];
					$persona->fecha_nac = $data['fecha_nac'];
					$persona->telefono_hab = $data['telefono_hab'];
					$persona->telefono_mov = $data['telefono_mov'];
					if ($persona->updateValues()) {
						$this->session->set_flashdata('success','Estudiante actualizado satisfactoriamente');
						redirect('personas/profesor/actualizar/'.$profesor->profesor_id,'refresh');
					}else{
						$mensaje[] = "Ha ocurrido un error al actualizar al representante";
					}
				}
			}
			$this->load->view('header',array('title'=>'Actualizar representante'));
			$this->load->view('profesor/actualizar',array(
					'profesor' => $profesor,
					'mensaje' => $mensaje,
				)
			);
		}else {
			show_404();
		}
	}

	/**
	* @param $profesorID int, id del profesor
	* muestra los datos de un profesor
	*/
	public function ver($profesorID){
		$profesor = $this->profesor->getData(array(
				'where' => array(array('profesor_id' => $profesorID))
			),true)->row();
		if ($profesor != NULL) {
			$dataUser = NULL;
			if ($profesor->rbac_user_id != NULL) {
				$dataUser = new $this->rbac_user($profesor->rbac_user_id);
			}

			$this->load->view('header',array('title'=>'Ver profesor'));
			$this->load->view('profesor/ver',array(
					'profesor' => $profesor,
					'user' => $dataUser
				)
			);
		} else {
			show_404();
		}
	}
}