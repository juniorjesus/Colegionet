<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MX_Controller {
	
	private $rules = array(
		'methods' => array('authentication','logout','crear'),
		'filter' => array(),
		'navs' => array(),
		'login' => array(
			'allow' => array('logout'),
			'ajax'	=> array(),
			'GET'	=> array(),
			'POST'	=> array()
		),
		'unlogged' => array(
			'allow' => array('authentication','crear'),
			'ajax'	=> array('authentication'),
			'GET'	=> array(),
			'POST'	=> array('authentication','crear'),
			'redirect' => ''
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('rbac_user_model','rbac_user');
		$this->load->model('rbac_user_rol_model','rbac_user_rol');
		$this->load->model('Rbac_rol_hierarchy_model','rbac_rol_hierarchy');
		$this->load->model('Rbac_rol_item_model','rbac_rol_item');
		$this->load->model('Rbac_rol_task_model','rbac_rol_task');
	}

	/**
	* mapeo. ver _remap en la guia de codegniter
	*/
	public function _remap($method,$params = array()){
		$method = $method == 'index' ? 'leer' : $method;
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this,$method), $params);
		}
		show_404();
	}

	/**
	* para iniciar sesion via ajax
	*/
	public function authentication(){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$datos = $this->input->post('login');
			if ($datos['usuario'] != '' || $datos['usuario'] != null) {
				$usuario = $datos['usuario'];
				$clave	= $datos['clave'];
				$usuario = $this->rbac_user->getData(array('or_where' => array(array('usuario' => $usuario,'email' => $usuario))));
				if (count($usuario->result()) > 0) {
					$dataUsuario = $usuario->row();
					$claveMD5 = md5($clave);
					if ($claveMD5 === $dataUsuario->clave) {
						if ($dataUsuario->activo) {
							$result['success'] = TRUE;
							$this->openSession($dataUsuario);
						} else {
							$result['mensajeError'] = 'Usuario inactivo';
						}
					} else {
						$result['mensajeError'] = 'Usuario o clave incorrecta';
					}
				} else {
					$result['mensajeError'] = 'Usuario o clave incorrecta';
				}
			} else {
				$result['mensajeError'] = 'Debe ingresar datos';
			}
		}
		echo json_encode($result);
	}

	/**
	* para cerrar la sesion
	*/
	public function logout(){
		$this->session->sess_destroy();
		redirect();
	}

	/**
	* @param $dataUsuario object datos del usuario
	* funcion usada para abrir la sesion y asignar los datos necesarios
	*/
	private function openSession($dataUsuario)
	{
		$accion = $dataUsuario->admin ? 'getFullItems' : 'getItems';
		$items = $this->$accion($dataUsuario->rbac_user_id);
		$itemsMenu = array_intersect_key($items, array_flip(array_keys(array_column($items, 'navbar'),1)));
		$data = array(
			'login'	=> true,
			'rbacUserID' => $dataUsuario->rbac_user_id,
			'username' => $dataUsuario->usuario,
			'activo' => $dataUsuario->activo,
			'admin' => $dataUsuario->admin,
			'estudiante' 	=> $this->getEstudiante($dataUsuario->rbac_user_id),
			'profesor' 		=> $this->getProfesor($dataUsuario->rbac_user_id),
			'representante' => $this->getRepresentante($dataUsuario->rbac_user_id),
			'persona' => $this->getPersona($dataUsuario->rbac_user_id),
			'items'	=> $items,
			'menu'	=> $this->getMenu($itemsMenu)
		);
		$this->session->set_userdata($data);
	}

	/**
	* @param $itemMenu item que son accesibles por menu (navbar = 1)
	* @return $menu array organizado
	*/
	private function getMenu($itemMenu)
	{
		$menu = array();
		foreach ($itemMenu as $key => $value) {
			$dataMain = explode('_', $value['item']);
			$menu[$dataMain[0]][] = array(
				'text'	=> $value['navtext'],
				'url'	=> site_url($dataMain),
				'dataMain' => array($dataMain[0],$dataMain[1])//modulo y controllador
			);
		}
		return $menu;
	}

	/**
	* @param $rbacUserID id del usuario
	* @return datos de estudiante
	*/
	private function getEstudiante($rbacUserID)
	{
		$this->load->model('personas/estudiante_model','estudiante');
		$estudiante = $this->estudiante->getData(array(
			'where' => array(array('persona.rbac_user_id' => $rbacUserID)),
			'select' => array('estudiante.*')
			),true)->row();
		return $estudiante;
	}

	/**
	* @param $rbacUserID id del usuario
	* @return datos de profesor
	*/
	private function getProfesor($rbacUserID)
	{
		$this->load->model('personas/profesor_model','profesor');
		$profesor = $this->profesor->getData(array(
			'where' => array(array('persona.rbac_user_id' => $rbacUserID)),
			'select' => array('profesor.*')
			),true)->row();
		return $profesor;
	}

	/**
	* @param $rbacUserID id del usuario
	* @return datos de representante
	*/
	private function getRepresentante($rbacUserID)
	{
		$this->load->model('personas/representante_model','representante');
		$representante = $this->representante->getData(array(
			'where' => array(array('persona.rbac_user_id' => $rbacUserID)),
			'select' => array('representante.*')
			),true)->row();
		return $representante;
	}

	/**
	* @param $rbacUserID id del usuario
	* @return datos de completos de la persona
	*/
	private function getPersona($rbacUserID)
	{
		$this->load->model('personas/persona_model','persona');
		$persona = $this->persona->getData(array(
			'where' => array(array('persona.rbac_user_id' => $rbacUserID)),
			'select' => array('persona.*')
			),true)->row();
		return $persona;	
	}

	/**
	* @param rbacUserID id del usuario
	* @return array con los item
	* funcion usada para obtener todos los permisos por ser administrador
	*/
	private function getFullItems($rbacUserID)
	{

		$items = $this->rbac_item->getData(array(
				'select' => array(
					array('concat(rbac_item.module,"_",rbac_item.class,"_",rbac_item.method) as item','navbar','navtext')
				)
			));

		return $items->result_array();
	}

	/**
	* @param rbacUserID id del usuario
	* @return array con los item
	* funcion usada para obtener los permisos asociados al usuario
	*/
	private function getItems($rbacUserID)
	{
		$roles = $this->rbac_user_rol->getData(array(
			'where'=>array(array('rbac_user_rol.rbac_user_id' => $rbacUserID)),
			'select' => array('rbac_rol.rbac_rol_id')
			),true)->result();

		//Lista de roles heredados, -1 para evitar error al ejecutar query en caso que no existe ningun rol
		$rolesHirearchy = array(-1);

		$rolesIni = array_column($roles, 'rbac_rol_id');
		foreach ($roles as $key => $value) {
			$rolesHirearchy[] = $value->rbac_rol_id;
			$this->getRolHierarchy($value->rbac_rol_id,$rolesIni,$rolesHirearchy);
		}

		$items = $this->rbac_rol_item->getData(array(
				'where_in'=>array(
					'rbac_rol_item.rbac_rol_id',$rolesHirearchy
				),
				'distinct'=>array(),
				'select' => array(
					array('concat(rbac_item.module,"_",rbac_item.class,"_",rbac_item.method) as item','navbar','navtext')
				)
			),true)->result_array();
		
		$itemsTask = $this->rbac_rol_task->getData(array(
				'where_in'=>array(
					'rbac_rol_task.rbac_rol_id',$rolesHirearchy
				),
				'distinct'=>array(),
				'select' => array(
					array('concat(rbac_item.module,"_",rbac_item.class,"_",rbac_item.method) as item','navbar','navtext')
				)
			),true)->result_array();

		return array_unique(array_merge($items,$itemsTask),SORT_REGULAR);
	}

	/**
	* @param rolID rol id a buscar
	* @param dataInicial array con los roles iniciales
	* @param dataFinal array al que se le iran agregando los roles encontrados
	* @return none
	* funcion usada para obtener los roles por herencia de los principales asignados directamente al usuario
	* funcion recursiva
	*/
	private function getRolHierarchy($rolID,$dataInicial,&$dataFinal)
	{
		$data = $this->rbac_rol_hierarchy->getData(array(
			'where'=>array(array('rbac_rol_hierarchy.rbac_rol_id' => $rolID)),
			'select'=>array('rh.rbac_rol_id'))
			,true)->result();
		foreach ($data as $key => $value) {
			if ($value->rbac_rol_id != $rolID && !in_array($value->rbac_rol_id,$dataInicial) && !in_array($value->rbac_rol_id, $dataFinal)) {
				$dataFinal[] = $value->rbac_rol_id;
				$this->getRolHierarchy($value->rbac_rol_id,$dataInicial,$dataFinal);
			}
		}
		return;
	}

}