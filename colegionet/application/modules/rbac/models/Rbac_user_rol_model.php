<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac_user_rol_model extends CI_Model {

	# defined field for this model
	private $table 	= "rbac_user_rol";
	public $rbac_user_rol_id;
	public $rbac_user_id;
	public $rbac_rol_id;

	public function __construct($rbacUserRolID = NULL){
		parent::__construct();
		if ($rbacUserRolID != NULL) {
			$data = $this->getData(array('where' => array(array('rbac_user_rol_id' => $rbacUserRolID))))->row();
			if ($data != null) {
				$this->rbac_user_rol_id = $data->rbac_user_rol_id;
				$this->rbac_user_id 	= $data->rbac_user_id;
				$this->rbac_rol_id 	= $data->rbac_rol_id;
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
			'rbac_user_rol_id' => 'RBAC user rol ID',
			'rbac_user_id' => 'RBAC user ID',
			'rbac_rol_id' => 'RBAC rol ID',
		);
	}

	public function joins(){
		$this->db->join('rbac_user','rbac_user.rbac_user_id = rbac_user_rol.rbac_user_id');
		$this->db->join('rbac_rol','rbac_rol.rbac_rol_id = rbac_user_rol.rbac_rol_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'rbac_user_rol_id',
				'label'	=>	'RBAC user rol ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'rbac_user_id',
				'label'	=>	'RBAC user ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'rbac_rol_id',
				'label'	=>	'RBAC rol ID',
				'rules'	=>	array('trim','numeric')
			),
		);
		$messageRules = array(
			'required'				=>	'El campo {field} es obligatorio',
			'min_length'			=>	'El campo {field} debe contener minimo {param} caracteres',
			'max_length'			=>	'El campo {field} debe contener maximo {param} caracteres',
			'alpha_numeric'			=>	'El campo {field} solo puede contener caracteres alfa numericos sin espacios',
			'alpha_numeric_spaces'	=>	'El campo {field} solo puede contener caracteres alfa numericos',
			'numeric'			=>	'El campo {field} solo puede contener numeros'
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

	public function deleteValues(){
		if (isset($this->rbac_user_rol_id)) {
			$this->db->set($this);
			$this->db->where('rbac_user_rol_id',$this->rbac_user_rol_id);
			if ($this->db->delete($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos, rbac_user_rol_id nulo");
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