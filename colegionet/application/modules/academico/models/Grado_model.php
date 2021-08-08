<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grado_model extends CI_Model {

	# defined field for this model
	private $table 	= "grado";
	public $grado_id;
	public $grado;
	public $numero;
	public $seccion;

	public function __construct($gradoID = NULL){
		parent::__construct();
		if ($gradoID != NULL) {
			$data = $this->getData(array('where' => array(array('grado_id' => $gradoID))))->row();
			if ($data != null) {
				$this->grado_id = $data->grado_id;
				$this->grado 	= $data->grado;
				$this->numero 	= $data->numero;
				$this->seccion 	= $data->seccion;
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
			'grado_id'	=> 'Grado ID',
			'grado'	=> 'Grado',
			'numero'	=> 'Numero',
			'seccion'	=> 'seccion',
		);
	}

	public function joins(){
		return NULL;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'grado_id',
				'label'	=>	'Grado Id',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'grado',
				'label'	=>	'Grado',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Záéíóú]+[A-Z0-9a-zñáéíóú#-_\s]+[A-Z0-9a-zñáéíóú]$/]',
								array('isunique',array($this,'isunique'))),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros',
								'is_unique'=>'Ya existe un grado con la misma descripcion'),
			),
			array(
				'field'	=>	'numero',
				'label'	=>	'Numero',
				'rules'	=>	array('required','trim','integer','exact_length[1]')
			),
			array(
				'field'	=>	'seccion',
				'label'	=>	'Seccion',
				'rules'	=>	array('required','trim','alpha','exact_length[1]')
			)
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

	public function isunique($grado){
		$data = $this->getData(array('where' => array(array('grado' => $grado))))->row();
		if ($data != null && $this->grado_id != $data->grado_id) {
			$this->form_validation->set_message('isunique','Ya existe un grado con la misma descripcion');
			return FALSE;
		}
		return TRUE;
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->grado_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->grado_id)) {
			$this->db->set($this);
			$this->db->where('grado_id',$this->grado_id);
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