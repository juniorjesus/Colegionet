<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class estudiante_representante_model extends CI_Model {

	# defined field for this model
	private $table 	= "estudiante_representante";
	public $estudiante_representante_id;
	public $estudiante_id;
	public $representante_id;
	public $parentesco_id;

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
			'estudiante_representante_id' => 'Estudiante Representante ID',
			'estudiante_id'	=> 'Estudiante ID',
			'representante_id'	=> 'Representante ID',
			'parentesco_id'	=> 'Parentesco ID',
		);
	}

	public function joins(){
		$this->db->join('estudiante','estudiante.estudiante_id = estudiante_representante.estudiante_id');
		$this->db->join('persona as p1','p1.persona_id = estudiante.persona_id');

		$this->db->join('representante','representante.representante_id = estudiante_representante.representante_id');
		$this->db->join('persona as p2','p2.persona_id = representante.persona_id');

		$this->db->join('parentesco','parentesco.parentesco_id = estudiante_representante.parentesco_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'estudiante_representante_id',
				'label'	=>	'Estudiante Representante ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'estudiante_id',
				'label'	=>	'Estudiante ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'representante_id',
				'label'	=>	'Representante ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'parentesco_id',
				'label'	=>	'Parentesco ID',
				'rules'	=>	array('required','trim','numeric')
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
			$this->estudiante_representante_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function deleteValues(){
		if (isset($this->estudiante_representante_id)) {
			$this->db->set($this);
			$this->db->where('estudiante_representante_id',$this->estudiante_representante_id);
			if ($this->db->delete($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos, estudiante_representante_id nulo");
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