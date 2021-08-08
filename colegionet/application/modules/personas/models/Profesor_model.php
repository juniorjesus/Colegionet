<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profesor_model extends CI_Model {

	# defined field for this model
	private $table 	= "profesor";
	public $profesor_id;
	public $persona_id;

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
			'profesor_id'	=> 'Cliente ID',
			'persona_id'	=> 'Persona ID',
		);
	}

	public function joins(){
		$this->db->join('persona','persona.persona_id = profesor.persona_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'profesor_id',
				'label'	=>	'Profesor ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'persona_id',
				'label'	=>	'Persona ID',
				'rules'	=>	array('required','trim','numeric','is_unique['.$this->table.'.persona_id]'),
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
			$this->profesor_id = $this->db->insert_id();
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