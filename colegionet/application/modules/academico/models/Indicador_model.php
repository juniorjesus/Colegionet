<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Indicador_model extends CI_Model {

	# defined field for this model
	private $table 	= "indicador";
	public $indicador_id;
	public $indicador;

	public function __construct($indicadorID = NULL){
		parent::__construct();
		if ($indicadorID != NULL) {
			$data = $this->getData(array('where' => array(array('indicador_id' => $indicadorID))))->row();
			if ($data != null) {
				$this->indicador_id = $data->indicador_id;
				$this->indicador 	= $data->indicador;
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
			'indicador_id' => 'Indicador ID',
			'indicador'	=> 'Indicador'
		);
	}

	public function joins(){
		return NULL;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'indicador_id',
				'label'	=>	'Indicador ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'indicador',
				'label'	=>	'Inidicador',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Záéíóú]+[A-Z0-9a-zñáéíóú#-_\s]+[A-Z0-9a-zñáéíóú]$/]',
								array('isunique',array($this,'isunique'))),
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
			'exact_length'			=>	'El campo {field} solo puede ser de {param} caracter',
		);
		$this->form_validation->set_rules($configRules);
		$this->form_validation->set_message($messageRules);
	}

	public function isunique($indicador){
		$data = $this->getData(array('where' => array(array('indicador' => $indicador))))->row();
		if ($data != null && $this->indicador_id != $data->indicador_id) {
			$this->form_validation->set_message('isunique','Ya existe un indicador con la misma descripcion');
			return FALSE;
		}
		return TRUE;
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->indicador_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->indicador_id)) {
			$this->db->set($this);
			$this->db->where('indicador_id',$this->indicador_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, indicador_id nulo");
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