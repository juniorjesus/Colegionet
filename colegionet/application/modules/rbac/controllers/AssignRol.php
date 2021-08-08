<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssignRol extends MX_Controller {
	
	private $rules = array(
		'methods' => array('assignRolToUser','unassignRolToUser'),
		'filter' => array('assignRolToUser','unassignRolToUser'),
		'navs' => array(),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('assignRolToUser','unassignRolToUser'),
			'GET'	=> array(),
			'POST'	=> array('assignRolToUser','unassignRolToUser')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('rbac_user_model','rbac_user');
		$this->load->model('rbac_rol_model','rbac_rol');
		$this->load->model('rbac_user_rol_model','rbac_user_rol');
	}

	public function _remap($method,$params = array()){
		$method = $method == 'index' ? 'leer' : $method;
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this,$method), $params);
		}
		show_404();
	}


	public function assignRolToUser($rbacUserID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$rbacUser = new $this->rbac_user($rbacUserID);
			if ($rbacUser->rbac_user_id != NULL) {
				$datos = $this->input->post('roles');
				$this->db->trans_begin();
				$flag = TRUE;
				foreach ($datos as $key => $value) {
					$dataRbacUserRol = $this->rbac_user_rol->getData(array(
							'where' => array(array('rbac_user_id' => $rbacUserID,'rbac_rol_id' => $value['rbac_rol_id']))
						))->row();
					if ($dataRbacUserRol == NULL) {
						$rbacUserRol = new $this->rbac_user_rol;
						$rbacUserRol->rbac_user_id = $rbacUserID;
						$rbacUserRol->rbac_rol_id = $value['rbac_rol_id'];
						if (!$rbacUserRol->saveValues()) {
							$result['mensajeError'] = 'Ha ocurrido un error asignado el rol';
							$this->db->trans_rollback();
							$flag = FALSE;
							break;
						}
					}
				}
				if ($flag) {
					$this->db->trans_commit();
					$result['mensaje'] = 'Roles asignados satisfactoriamente';
					$result['success'] = TRUE;
				}

				$this->db->trans_complete();
			} else {
				$result['mensajeError'] = 'Usuarion inexistente';
			}
		}
		echo json_encode($result);
	}

	public function unassignRolToUser($rbacUserRolID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$rbacUserRol = new $this->rbac_user_rol($rbacUserRolID);
			if ($rbacUserRol->rbac_user_rol_id != NULL) {
				if ($rbacUserRol->deleteValues()) {
					$result['success'] = TRUE;
					$result['mensaje'] = 'Desasignado satisfactoriamente';
				} else {
					$result['mensajeError'] = 'Ha ocurrido un error al desasignar el rol';
				}
			} else {
				$result['mensajeError'] = 'La asociacion al rol no existe';
			}
		}
		echo json_encode($result);
	}
}