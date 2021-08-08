<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parentesco_model extends CI_Model {

	# defined field for this model
	private $table 	= "parentesco";
	public $parentesco_id;
	public $descripcion;

	public function __construct($parentescoID = NULL){
		parent::__construct();
		if ($parentescoID != NULL) {
			$data = $this->getData(array('where' => array(array('parentesco_id' => $parentescoID))))->row();
			if ($data != null) {
				$this->parentesco_id 	= $data->parentesco_id;
				$this->descripcion 		= $data->descripcion;
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
			'parentesco_id' => 'Parentesco ID',
			'descripcion' => 'Descripcion'
		);
	}

	public function joins(){
		return NULL;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'parentesco_id',
				'label'	=>	'Parentesco ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'descripcion',
				'label'	=>	'Descripcion',
				'rules'	=>	array('required','trim','max_length[100]','regex_match[/^[0-9a-zñA-Z]+[A-Z0-9a-zñ#-_\s]+[A-Z0-9a-zñ]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros'),
			),		
		);
		$messageRules = array(
			'required'				=>	'El campo {field} es obligatorio',
			'min_length'			=>	'El campo {field} debe contener minimo {param} caracteres',
			'max_length'			=>	'El campo {field} debe contener maximo {param} caracteres',
			'alpha_numeric'			=>	'El campo {field} solo puede contener caracteres alfa numericos sin espacios',
			'alpha_numeric_spaces'	=>	'El campo {field} solo puede contener caracteres alfa numericos',
			'numeric'				=>	'El campo {field} solo puede contener numeros',
			'integer'				=>	'El campo {field} solo puede contener numeros',
			'exact_length'			=>	'El campo {field} solo puede ser de {param} caracter',
		);
		$this->form_validation->set_rules($configRules);
		$this->form_validation->set_message($messageRules);
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->parentesco_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->parentesco_id)) {
			$this->db->set($this);
			$this->db->where('parentesco_id',$this->parentesco_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, grado_id nulo");
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