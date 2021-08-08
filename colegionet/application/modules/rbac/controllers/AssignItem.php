<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssignItem extends MX_Controller {
	
	private $rules = array(
		'methods' => array('managementItemOfRol','assignItemToRol','unassignItemToRol'),
		'filter' => array('managementItemOfRol','assignItemToRol','unassignItemToRol'),
		'navs' => array(),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('managementItemOfRol','assignItemToRol','unassignItemToRol'),
			'GET'	=> array('managementItemOfRol'),
			'POST'	=> array('assignItemToRol','unassignItemToRol')
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
		$this->load->model('rbac_item_model','rbac_item');
		$this->load->model('rbac_rol_item_model','rbac_rol_item');
	}

	public function _remap($method,$params = array()){
		$method = $method == 'index' ? 'leer' : $method;
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this,$method), $params);
		}
		show_404();
	}

	/**
	* @param $rbacRolID int numero de offset para query de paginacion
	* funcion leer = index
	*/
	public function managementItemOfRol($rbacRolID){
		$rol = new $this->rbac_rol($rbacRolID);
		if ($rol->rbac_rol_id != NULL) {
			
			//to get all items and those that this rol already has
			$items = $this->rbac_item->getData(array(
					'join' => array('rbac_rol_item','rbac_rol_item.rbac_item_id = rbac_item.rbac_item_id AND rbac_rol_id = '.$rbacRolID,'left'),
					'distinct' => array(),
					'select' => array(array('rbac_item.*','concat(rbac_item.module,"_",rbac_item.class,"_",rbac_item.method) as item','rbac_rol_item.rbac_rol_item_id')),
					'order_by' => array('rbac_item.rbac_item_id ASC')
				))->result();

			$groupedItems = $this->groupItems($items);

			$this->load->view('header',array('title'=>'Roles'));
			$this->load->view('assignItem/managementItemOfRol',array(
					'items' => $groupedItems,
					'rolID' => $rbacRolID,
					'rol' => $rol,
				));
		} else {
			show_404();
		}
	}

	/**
	* @param $items, all items
	* group this by module then class then method
	*/
	private function groupItems($items){
		$arr = array();
		if (count($items) > 0) {
			foreach ($items as $key => $value) {
				$arr[$value->module][$value->class][$value->method]['item'] = $value->item;
				$arr[$value->module][$value->class][$value->method]['assigned'] = $value->rbac_rol_item_id ? TRUE : FALSE;
				$arr[$value->module][$value->class][$value->method]['rbacRolItemID'] = $value->rbac_rol_item_id;
				$arr[$value->module][$value->class][$value->method]['rbacItemID'] = $value->rbac_item_id;
			}
		}
		return $arr;
	}

	public function assignItemToRol($rbacRolID,$rbacItemID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$rbacRol = new $this->rbac_rol($rbacRolID);
			if ($rbacRol->rbac_rol_id != NULL) {
				$rbacItem = new $this->rbac_item($rbacItemID);
				if ($rbacItem->rbac_item_id != NULL) {
					$dataRbacRolItem = $this->rbac_rol_item->getData(array(
							'where' => array(array('rbac_rol_id' => $rbacRolID,'rbac_item_id' => $rbacItemID))
						))->row();
					if ($dataRbacRolItem == NULL) {
						$rbacRolItem = new $this->rbac_rol_item;
						$rbacRolItem->rbac_rol_id = $rbacRolID;
						$rbacRolItem->rbac_item_id = $rbacItemID;
						if ($rbacRolItem->saveValues()) {
							$result['mensaje'] = 'Asignado satisfactoriamente';
							$result['success'] = TRUE;
						} else {
							$result['mensajeError'] = 'Ha ocurrido un error al asignar el permiso';
						}
					} else {
						$result['mensajeError'] = 'El permiso ya se encuentra asociado al rol';
					}

				} else {
					$result['mensajeError'] = 'El permiso que intenta agregar no existe';
				}
			} else {
				$result['mensajeError'] = 'El rol al que intenta agregar permisos no existe';
			}
		}
		echo json_encode($result);
	}

	public function unassignItemToRol($rbacRolItemID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$rbacRolItem = new $this->rbac_rol_item($rbacRolItemID);
			if ($rbacRolItem->rbac_rol_item_id != NULL) {
				if ($rbacRolItem->deleteValues()) {
					$result['success'] = TRUE;
					$result['mensaje'] = 'Desasignado satisfactoriamente';
				} else {
					$result['mensajeError'] = 'Ha ocurrido un error al desasignar el permiso';
				}
			} else {
				$result['mensajeError'] = 'La asociacion al rol no existe';
			}
		}
		echo json_encode($result);
	}
}