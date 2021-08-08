<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turno_model extends CI_Model {

	# defined field for this model
	private $table 	= "turno";
	public $turno_id;
	public $turno;

	public function __construct($turnoID = NULL){
		parent::__construct();
		if ($turnoID != NULL) {
			$data = $this->getData(array('where' => array(array('turno_id' => $turnoID))))->row();
			if ($data != null) {
				$this->turno_id = $data->turno_id;
				$this->turno = $data->turno;
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
			'turno_id'	=> 'Turno ID',
			'turno'	=> 'Turno'
		);
	}

	public function joins(){
		return NULL;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'turno_id',
				'label'	=>	'Turno Id',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'turno',
				'label'	=>	'Turno',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Z]+[A-Z0-9a-zñ#-_\s]+[A-Z0-9a-zñ]$/]',
								array('isunique',array($this,'isunique'))),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros',
								'is_unique'=>'Ya existe un turno con la misma descripcion'),
			),
		);
		$messageRules = array(
			'required'				=>	'El campo {field} es obligatorio',
			'min_length'			=>	'El campo {field} debe contener minimo {param} caracteres',
			'max_length'			=>	'El campo {field} debe contener maximo {param} caracteres',
			'alpha_numeric'			=>	'El campo {field} solo puede contener caracteres alfa numericos sin espacios',
			'alpha_numeric_spaces'	=>	'El campo {field} solo puede contener caracteres alfa numericos',
			'numeric'				=>	'El campo {field} solo puede contener numeros',
			'exact_length'			=>	'El campo {field} solo puede ser de {param} caracter',
		);
		$this->form_validation->set_rules($configRules);
		$this->form_validation->set_message($messageRules);
	}

	public function isunique($turno){
		$data = $this->getData(array('where' => array(array('turno' => $turno))))->row();
		if ($data != null && $this->turno_id != $data->turno_id) {
			$this->form_validation->set_message('isunique','Ya existe un turno con la misma descripcion');
			return FALSE;
		}
		return TRUE;
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->turno_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->turno_id)) {
			$this->db->set($this);
			$this->db->where('turno_id',$this->turno_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, turno_id nulo");
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