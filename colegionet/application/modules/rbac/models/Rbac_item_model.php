<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac_item_model extends CI_Model {

	# defined field for this model
	private $table 	= "rbac_item";
	public $rbac_item_id;
	public $module;
	public $class;
	public $method;
	public $navbar;
	public $navtext;

	public function __construct($rbacItemID = NULL){
		parent::__construct();
		if ($rbacItemID != NULL) {
			$data = $this->getData(array('where' => array(array('rbac_item_id' => $rbacItemID))))->row();
			if ($data != null) {
				$this->rbac_item_id = $data->rbac_item_id;
				$this->module 	= $data->module;
				$this->class 	= $data->class;
				$this->method 	= $data->method;
				$this->navbar	= $data->navbar;
				$this->navtext	= $data->navtext;
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
			'rbac_item_id' => 'RBAC item ID',
			'module'=> 'Modulo',
			'class'	=> 'Clase',
			'method'=> 'Metodo',
			'navbar'=> 'Navegador',
			'navtext'=> 'Texto',
		);
	}

	public function joins(){
		return null;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'rbac_item_id',
				'label'	=>	'RBAC item ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'module',
				'label'	=>	'Modulo',
				'rules'	=>	array('required','trim','alpha_dash')
			),
			array(
				'field'	=>	'class',
				'label'	=>	'Clase',
				'rules'	=>	array('required','trim','alpha_dash')
			),
			array(
				'field'	=>	'navbar',
				'label'	=>	'Navegador',
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