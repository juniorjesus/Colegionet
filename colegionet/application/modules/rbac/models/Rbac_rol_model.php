<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac_rol_model extends CI_Model {

	# defined field for this model
	private $table 	= "rbac_rol";
	public $rbac_rol_id;
	public $nombre;
	public $descripcion;
	public $tipo_id;

	public function __construct($rolID = NULL){
		parent::__construct();
		if ($rolID != NULL) {
			$data = $this->getData(array('where' => array(array('rbac_rol_id' => $rolID))))->row();
			if ($data != null) {
				$this->rbac_rol_id = $data->rbac_rol_id;
				$this->nombre 	= $data->nombre;
				$this->descripcion 	= $data->descripcion;
				$this->tipo_id 	= $data->tipo_id;
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
			'rbac_rol_id'	=> 'RBAC Rol ID',
			'nombre' => 'Nombre',
			'descripcion' => 'Descripcion',
			'tipo_id' => 'Tipo ID',
		);
	}

	public function joins(){
		return NULL;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'rbac_rol_id',
				'label'	=>	'Grado Id',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'nombre',
				'label'	=>	'Nombre',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Z]+[A-Z0-9a-zñ#-_\s]+[A-Z0-9a-zñ]$/]',
								array('isunique',array($this,'isunique'))),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros')
			),
			array(
				'field'	=>	'descripcion',
				'label'	=>	'Descripcion',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Z]+[A-Z0-9a-zñ#-_\s]+[A-Z0-9a-zñ]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros')
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

	public function isunique($nombre){
		$data = $this->getData(array('where' => array(array('nombre' => $nombre))))->row();
		if ($data != null && $this->rbac_rol_id != $data->rbac_rol_id) {
			$this->form_validation->set_message('isunique','Ya existe un rol con el mismo nombre');
			return FALSE;
		}
		return TRUE;
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->rbac_rol_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->rbac_rol_id)) {
			$this->db->set($this);
			$this->db->where('rbac_rol_id',$this->rbac_rol_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, rbac_rol_id nulo");
			return false;
		}
	}

	public function getData($params = array(),$useJoin = FALSE){
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