<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rbac_user_model extends CI_Model {

	# defined field for this model
	private $table 	= "rbac_user";
	public $rbac_user_id;
	public $usuario;
	public $clave;
	public $email;
	public $activo = 1;
	public $admin = 0;

	public function __construct($rbacUserID = NULL){
		parent::__construct();
		if ($rbacUserID != NULL) {
			$data = $this->getData(array('where' => array(array('rbac_user_id' => $rbacUserID))))->row();
			if ($data != null) {
				$this->rbac_user_id = $data->rbac_user_id;
				$this->usuario 	= $data->usuario;
				$this->clave 	= $data->clave;
				$this->email 	= $data->email;
				$this->activo 	= $data->activo;
				$this->admin 	= $data->admin;
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
			'rbac_user_id' => 'RBAC User ID',
			'usuario'	=> 'Usuario',
			'clave'		=> 'Clave',
			'email'		=> 'Correo',
			'activo'	=> 'Activo',
			'admin'		=> 'Admin'
		);
	}

	public function joins(){
		return null;
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'rbac_user_id',
				'label'	=>	'RBAC usuario ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'usuario',
				'label'	=>	'Usuario',
				'rules'	=>	array('required','trim','alpha_dash',array('isuniqueUser',array($this,'isuniqueUser')))
			),
			array(
				'field'	=>	'email',
				'label'	=>	'Correo',
				'rules'	=>	array('required','trim','valid_email',array('isuniqueEmail',array($this,'isuniqueEmail')))
			),
			array(
				'field'	=>	'clave',
				'label'	=>	'Contraseña',
				'rules'	=>	array('required','min_length[5]','matches[reclave]'),
				'errors'=>	array('matches'=>'Las contraseñas no coinciden'),
			),
			array(
				'field'	=>	'reclave',
				'label'	=>	'Confirmar contraseña',
				'rules'	=>	array('required','min_length[5]','matches[clave]'),
				'errors'=>	array('matches'=>'Las contraseñas no coinciden'),
			),
			array(
				'field'	=>	'activo',
				'label'	=>	'Activo',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'admin',
				'label'	=>	'Admin',
				'rules'	=>	array('trim','numeric')
			),
		);
		$messageRules = array(
			'required'				=>	'El campo {field} es obligatorio',
			'min_length'			=>	'El campo {field} debe contener minimo {param} caracteres',
			'max_length'			=>	'El campo {field} debe contener maximo {param} caracteres',
			'alpha_numeric'			=>	'El campo {field} solo puede contener caracteres alfa numericos sin espacios',
			'alpha_numeric_spaces'	=>	'El campo {field} solo puede contener caracteres alfa numericos',
			'numeric'				=>	'El campo {field} solo puede contener numeros',
			'alpha_dash'			=>	'El campo {field} solo puede contener caracteres alfa numericos, guiones y guiones bajos',
			'valid_email'			=>	'El campo {field} debe contener un correo valido',
		);
		$this->form_validation->set_rules($configRules);
		$this->form_validation->set_message($messageRules);
	}
	
	public function isuniqueUser($usuario){
		$data = $this->getData(array('where' => array(array('usuario' => $usuario))))->row();
		if ($data != null && $this->rbac_user_id != $data->rbac_user_id) {
			$this->form_validation->set_message('isuniqueUser','Ya existe un rol con el mismo nombre');
			return FALSE;
		}
		return TRUE;
	}

	public function isuniqueEmail($email){
		$data = $this->getData(array('where' => array(array('email' => $email))))->row();
		if ($data != null && $this->rbac_user_id != $data->rbac_user_id) {
			$this->form_validation->set_message('isuniqueEmail','El correo ya se encuentra en uso');
			return FALSE;
		}
		return TRUE;
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->rbac_user_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->rbac_user_id)) {
			$this->db->set($this);
			$this->db->where('rbac_user_id',$this->rbac_user_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, rbac_user_id nulo");
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