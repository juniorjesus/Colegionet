<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','ver','obtenerRolesNoAsignados'),
		'filter' => array('crear','leer','actualizar','ver','obtenerRolesNoAsignados'),
		'navs' => array('leer'=>'Usuarios'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('crear','leer','borrar','actualizar','obtenerRolesNoAsignados'),
			'GET'	=> array('leer','actualizar'),
			'POST'	=> array('crear','actualizar','borrar','obtenerRolesNoAsignados')
		),
		'unlogged' => array(
			'allow' => array('crear'),
			'ajax'	=> array('crear'),
			'GET'	=> array(),
			'POST'	=> array('crear'),
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('rbac_user_model','rbac_user');
		$this->load->model('rbac_rol_model','rbac_rol');
		$this->load->model('rbac_user_rol_model','rbac_user_rol');

		$this->load->model('personas/estudiante_model','estudiante');
		$this->load->model('personas/profesor_model','profesor');
		$this->load->model('personas/persona_model','persona');
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
			$data = $this->input->get('user');
			if ($data['rbac_user_id'] != '') {
				$busqueda['like'][0]['rbac_user_id'] = $data['rbac_user_id'];
			}
			if ($data['usuario'] != '') {
				$busqueda['like'][0]['usuario'] = $data['usuario'];
			}
			if ($data['email'] != '') {
				$busqueda['like'][0]['email'] = $data['email'];
			}
		}
		$totalUsuarios = $this->rbac_user->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('rbac/user/leer');
		$config['total_rows'] = $totalUsuarios;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$usuarios = $this->rbac_user->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Usuarios'));
		$this->load->view('user/leer',array(
				'usuarios' => $usuarios,
				'pagNums'	=> $pagNums,
			)
		);
	}

	/**
	* @param none
	* crea un usuario
	*/
	public function crear(){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$tipo = $this->input->post('tipo');
			switch ($tipo) {
				case 'estudiante':
					$this->createUserEstudiante($result);
					break;
				case 'profesor':
					$this->createUserProfesor($result);
					break;
				
				default:
					$result['mensajeError'] = 'Informacion imcompleta';
					break;
			}
		}
		echo json_encode($result);
	}

	private function createUserEstudiante(&$result){
		$datos = $this->input->post('user');
		$datosPersona = $this->input->post('persona');

		$datosEstudiante = $this->estudiante->getData(array(
				'or_where' => array(array('persona.identificacion' => $datosPersona['identificacion'],'estudiante.matricula' => $datosPersona['identificacion']))
			),TRUE)->row();
		if ($datosEstudiante != NULL) {
			if ($datosEstudiante->rbac_user_id == NULL) {
				$user = new $this->rbac_user;
				$this->form_validation->set_data($datos);
				$user->rules();
				if ($this->form_validation->run() == TRUE) {
					$this->db->trans_begin();

					$user->usuario = $datos['usuario'];
					$user->email = $datos['email'];
					$user->clave = md5($datos['clave']);
					if ($user->saveValues()) {
						$persona = new $this->persona($datosEstudiante->persona_id);
						$persona->rbac_user_id = $user->rbac_user_id;
						if ($persona->updateValues()) {
							
							$rolEstudiante = $this->rbac_rol->getData(array(
									'where' => array(array('tipo_id' => 1))//tipo 1 es estudiante
								))->row();

							if ($rolEstudiante != NULL) {
								$userRol = new $this->rbac_user_rol;
								$userRol->rbac_rol_id = $rolEstudiante->rbac_rol_id;
								$userRol->rbac_user_id = $user->rbac_user_id;
								if ($userRol->saveValues()) {
									$this->db->trans_commit();
									$result['success'] = TRUE;
									$result['user'] = $user;
									$result['mensaje'] = 'Usuario creado satisfactoriamente';
								} else {
									$this->db->trans_rollback();
									$result['mensajeError'] = 'Ha ocurrido un error al asignar rol al usuario, contacte al administrador';
								}
							} else {
								$this->db->trans_rollback();
								$result['mensajeError'] = 'Imposible crear usuario por que no existe rol estudiante para asignar';
							}

						} else {
							$this->db->trans_rollback();
							$result['mensajeError'] = 'Ha ocurrido un error asociando el usuario al estudiante';
						}
					} else {
						$this->db->trans_rollback();
						$result['mensajeError'] = 'Ha ocurrido un error al crear el usuario';
					}
					$this->db->trans_complete();
				}else{
					$result['mensajeError'] = validation_errors();
				}
			} else {
				$result['mensajeError'] = 'El estudiante ya posee un usuario';
			}
		} else {
			$result['mensajeError'] = 'La cedula de identidad o escolar no corresponda a ningun estudiante';
		}
	}

	private function createUserProfesor(&$result){
		$datos = $this->input->post('user');
		$profesorID = $this->input->post('profesor');

		$datosProfesor = $this->profesor->getData(array(
				'where' => array(array('profesor_id' => $profesorID))
			),TRUE)->row();
		if ($datosProfesor != NULL) {
			if ($datosProfesor->rbac_user_id == NULL) {
				$user = new $this->rbac_user;
				$this->form_validation->set_data($datos);
				$user->rules();
				if ($this->form_validation->run() == TRUE) {
					$this->db->trans_begin();

					$user->usuario = $datos['usuario'];
					$user->email = $datos['email'];
					$user->clave = md5($datos['clave']);
					if ($user->saveValues()) {
						$persona = new $this->persona($datosProfesor->persona_id);
						$persona->rbac_user_id = $user->rbac_user_id;
						if ($persona->updateValues()) {
							$this->db->trans_commit();
							$result['success'] = TRUE;
							$result['mensaje'] = 'Usuario creado satisfactoriamente';
						} else {
							$this->db->trans_rollback();
							$result['mensajeError'] = 'Ha ocurrido un error asociando el usuario al profesor';
						}
					}else{
						$this->db->trans_rollback();
						$result['mensajeError'] = 'Ha ocurrido un error al crear el usuario';
					}
					$this->db->trans_complete();
				}else{
					$result['mensajeError'] = validation_errors();
				}
			} else {
				$result['mensajeError'] = 'El profesor ya posee un usuario';
			}
		} else {
			$result['mensajeError'] = 'Datos incompletos';
		}
	}

	/**
	* @param $rbacRolID int, id del rol
	* actualiza los datos de un rol
	*/
	public function actualizar($rbacUserID){
		$mensaje = array();
		$user = new $this->rbac_user($rbacUserID);
		if ($user->rbac_user_id != NULL) {
			if ($this->input->post()) {
				
				$data = $this->input->post('user');
				$data['clave'] == '' ? $data['clave'] = $user->clave : NULL;
				$data['reclave'] == '' ? $data['reclave'] = $user->clave : NULL;

				$this->form_validation->set_data($data);
				$user->rules();
				
				if ($this->form_validation->run() == TRUE) {
					
					$user->usuario = $data['usuario'];
					$user->email = $data['email'];
					
					//data['clave'] could be the same so there is no need to update
					$data['clave'] != $user->clave ? $user->clave = md5($data['clave']) : NULL;

					if ($user->updateValues()) {
						$this->session->set_flashdata('success','Usuario actualizado satisfactoriamente');
						redirect('rbac/user/actualizar/'.$user->rbac_user_id,'refresh');
					}else{
						$mensaje[] = "Ha ocurrido un error al actualizar el periodo";
					}
				}
			}
			$roles = $this->rbac_user_rol->getData(array(
					'where' => array(array('rbac_user_rol.rbac_user_id' => $rbacUserID))
				),TRUE)->result_array();

			$this->load->view('header',array('title'=>'Actualizar rol'));
			$this->load->view('user/actualizar',array(
					'user' => $user,
					'mensaje' => $mensaje,
					'roles' => $roles,
					'userID' => $rbacUserID
				)
			);
		}else {
			show_404();
		}
	}

	public function obtenerRolesNoAsignados($rbacUserID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			
			$rolesAsignados = $this->rbac_user_rol->getData(array(
					'where' => array(array('rbac_user_id' => $rbacUserID))
				))->result_array();

			$arr = array(-1); //-1 para evitar error el query IN
			if (count($rolesAsignados) > 0) {
				$arr = array_column($rolesAsignados, 'rbac_rol_id');
			}
			$rolesNoAsignados = $this->rbac_rol->getdata(array(
					'where_not_in' => array('rbac_rol_id',$arr)
				))->result_array();

			$result['success'] = TRUE;
			$result['table'] = $this->load->view('user/_RolesNoAsignados',array(
					'rolesNoAsignados' => $rolesNoAsignados,
					'userID' => $rbacUserID
				),TRUE
			);
		}
		echo json_encode($result);	
	}

	/**
	* @param $gradoID int, id del grado
	* muestra los datos de un grado
	*/
	public function ver($rbacUserID){
		$user = new $this->rbac_user($rbacUserID);
		if ($user->rbac_user_id != NULL) {
			$this->load->view('header',array('title'=>'Ver usuario'));
			$this->load->view('user/ver',array(
					'user' => $user,
				)
			);
		} else {
			show_404();
		}
	}
}