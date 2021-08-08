<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac_rol_task_model extends CI_Model {

	# defined field for this model
	private $table 	= "rbac_rol_task";
	public $rbac_rol_task_id;
	public $rbac_rol_id;
	public $rbac_task_id;

	public function __construct($rbacRolTaskID = NULL){
		parent::__construct();
		if ($rbacRolTaskID != NULL) {
			$data = $this->getData(array('where' => array(array('rbac_rol_task_id' => $rbacRolTaskID))))->row();
			if ($data != null) {
				$this->rbac_rol_task_id = $data->rbac_rol_task_id;
				$this->rbac_rol_id 	= $data->rbac_rol_id;
				$this->rbac_task_id 	= $data->rbac_task_id;
			}
		}
	}

	public function __set($name,$value){
		if (property_exists($this, $name) && $name!='table') {
			$this->$name = $value;
		}
	}

	public function getTable(){
		return $this->table;
	}
	
	public function fields(){
		return array(
			'rbac_rol_task_id' => 'RBAC rol task ID',
			'rbac_rol_id' => 'RBAC rol ID',
			'rbac_task_id' => 'RBAC task ID',
		);
	}

	public function joins(){
		$this->db->join('rbac_rol','rbac_rol.rbac_rol_id = rbac_rol_task.rbac_rol_id');
		$this->db->join('rbac_task','rbac_task.rbac_task_id = rbac_rol_task.rbac_task_id');

		$this->db->join('rbac_task_item','rbac_task_item.rbac_task_id = rbac_task.rbac_task_id');
		$this->db->join('rbac_item','rbac_item.rbac_item_id = rbac_task_item.rbac_item_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'rbac_rol_task_id',
				'label'	=>	'RBAC rol task ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'rbac_rol_id',
				'label'	=>	'RBAC rol ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'rbac_task_id',
				'label'	=>	'RBAC task ID',
				'rules'	=>	array('trim','numeric')
			),
		);
		$messageRules = array(
			'required'				=>	'El campo {field} es obligatorio',
			'min_length'			=>	'El campo {field} debe contener minimo {param} caracteres',
			'max_length'			=>	'El campo {field} debe contener maximo {param} caracteres',
			'alpha_numeric'			=>	'El campo {field} solo puede contener caracteres alfa numericos sin espacios',
			'alpha_numeric_spaces'	=>	'El campo {field} solo puede contener caracteres alfa numericos',
			'numeric'				=>	'El campo {field} solo puede contener numeros'
		);
		$this->form_validation->set_rules($configRules);
		$this->form_validation->set_message($messageRules);
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->estudiante_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function getData($params = array(),$useJoin = false){
		if ($useJoin) {
			$this->joins();
		}
		foreach ($params as $method => $value) {
			call_user_func_array(array($this->db,$method), $value);
		}
		$this->db->from($this->table);
		return $this->db->get();
	}

}