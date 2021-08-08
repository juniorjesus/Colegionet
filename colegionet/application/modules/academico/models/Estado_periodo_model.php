<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estado_periodo_model extends CI_Model {

	# defined field for this model
	private $table 	= "estado_periodo";
	public $estado_periodo_id;
	public $estado;

	public function __construct(){
		parent::__construct();
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
			'estado_periodo_id'	=> 'Estado Periodo ID',
			'estado'	=> 'Estado',
		);
	}

	public function joins(){
		return NULL;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'estado_periodo_id',
				'label'	=>	'Estado Periodo',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'estado',
				'label'	=>	'Estado',
				'rules'	=>	array('required','trim','alpha','is_unique['.$this->table.'.estado]'),
				'errors'=>	array('is_unique'=>'El cliente ya existe')
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
			$this->estado_periodo_id = $this->db->insert_id();
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