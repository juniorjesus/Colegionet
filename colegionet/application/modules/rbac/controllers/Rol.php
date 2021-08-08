<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rol extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','ver'),
		'filter' => array('crear','leer','actualizar','ver'),
		'navs' => array('leer'=>'Roles'),
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
		$this->load->model('rbac_rol_model','rbac_rol');
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
			$data = $this->input->get('rol');
			if ($data['rbac_rol_id'] != '') {
				$busqueda['like'][0]['rbac_rol_id'] = $data['rbac_rol_id'];
			}
			if ($data['nombre'] != '') {
				$busqueda['like'][0]['nombre'] = $data['nombre'];
			}
			if ($data['descripcion'] != '') {
				$busqueda['like'][0]['descripcion'] = $data['descripcion'];
			}
		}
		$totalRoles = $this->rbac_rol->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('rbac/rol/leer');
		$config['total_rows'] = $totalRoles;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$roles = $this->rbac_rol->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Roles'));
		$this->load->view('rol/leer',array(
				'roles' => $roles,
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
			$rol = new $this->rbac_rol;
			$data = $this->input->post('rol');
			$this->form_validation->set_data($data);
			$rol->rules();
			

			if ($this->form_validation->run() == TRUE) {
				$rol->nombre = $data['nombre'];
				$rol->descripcion = $data['descripcion'];
				
				if ($rol->saveValues()) {
					$this->session->set_flashdata('success','Rol creado satisfactoriamente');
					redirect('rbac/rol/actualizar/'.$rol->rbac_rol_id,'refresh');
				} else {
					$mensaje[] = "Ha ocurrido un error al crear el grado";
				}
			}
		}
		$this->load->view('header',array('title'=>'Registrar rol'));
		$this->load->view('rol/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param $rbacRolID int, id del rol
	* actualiza los datos de un rol
	*/
	public function actualizar($rbacRolID){
		$mensaje = array();
		$rol = new $this->rbac_rol($rbacRolID);
		if ($rol->rbac_rol_id != NULL) {
			if ($this->input->post()) {
				
				$data = $this->input->post('rol');
				$this->form_validation->set_data($data);
				$rol->rules();
				
				
				if ($this->form_validation->run() == TRUE) {
					
					$rol->nombre = $data['nombre'];
					$rol->descripcion = $data['descripcion'];

					if ($rol->updateValues()) {
						$this->session->set_flashdata('success','Grado actualizado satisfactoriamente');
						redirect('rbac/rol/actualizar/'.$rol->rbac_rol_id,'refresh');
					}else{
						$mensaje[] = "Ha ocurrido un error al actualizar el periodo";
					}
				}
			}
			$this->load->view('header',array('title'=>'Actualizar rol'));
			$this->load->view('rol/actualizar',array(
					'rol' => $rol,
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
	public function ver($rbacRolID){
		$rol = new $this->rbac_rol($rbacRolID);
		if ($rol->rbac_rol_id != NULL) {
			$this->load->view('header',array('title'=>'Ver rol'));
			$this->load->view('rol/ver',array(
					'rol' => $rol,
				)
			);
		} else {
			show_404();
		}
	}
}