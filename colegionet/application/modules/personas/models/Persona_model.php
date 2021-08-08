<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persona_model extends CI_Model {

	# defined field for this model
	private $table 	= "persona";
	public $persona_id;
	public $identificacion;
	public $nombres;
	public $apellidos;
	public $sexo;
	public $fecha_nac;
	public $telefono_hab;
	public $telefono_mov;
	public $rbac_user_id;
	
	public function __construct($personaID = NULL){
		parent::__construct();
		if ($personaID != null) {
			$data = $this->getData(array('where' => array(array('persona_id' => $personaID))))->row();
			if ($data != null) {
				$this->persona_id = $data->persona_id;
				$this->identificacion 	= $data->identificacion;
				$this->nombres 	= $data->nombres;
				$this->apellidos 	= $data->apellidos;
				$this->sexo 	= $data->sexo;
				$this->fecha_nac 	= $data->fecha_nac;
				$this->telefono_hab 	= $data->telefono_hab;
				$this->telefono_mov 	= $data->telefono_mov;
				$this->rbac_user_id 	= $data->rbac_user_id;
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
			'persona_id'	=> 'Persona ID',
			'identificacion'=> 'Cedula',
			'nombres'		=> 'Nombres',
			'apellidos'		=> 'Apellidos',
			'sexo'			=> 'Sexo',
			'fecha_nac'		=> 'Fecha Nac.',
			'telefono_hab'	=> 'Telefono Hab.',
			'telefono_mov'	=> 'Telefnon Mov.',
			'rbac_user_id'		=> 'Usuario ID'
		);
	}

	public function joins(){
		$this->db->join('rbac_user','persona.rbac_user_id = rbac_user.rbac_user_id','left');
	}


	public function rules($case){
		$routeClass = $this->router->class;
		$idRules = array('trim','numeric','is_unique['.$this->table.'.identificacion]','min_length[6]','max_length[9]');
		if (($routeClass == 'representante' || $routeClass == 'profesor' ) && $case == 'crear') {
			$idRules = array('required','trim','numeric','is_unique['.$this->table.'.identificacion]','min_length[6]','max_length[9]');
		}

		$configRules['crear'] = array(
			array(
				'field'	=>	'identificacion',
				'label'	=>	'Identificacion',
				'rules'	=>	$idRules,
				'errors'=>	array('is_unique'=>'El numero de cedula ya se encuentra registrado')
			),
			array(
				'field'	=>	'nombres',
				'label'	=>	'Nombres',
				'rules'	=>	array('required','trim','regex_match[/^[a-zñA-Záéíóú]+[A-Za-zñáéíóú\s]+[A-Za-zñáéíóú]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras')
			),
			array(
				'field'	=>	'apellidos',
				'label'	=>	'Apellidos',
				'rules'	=>	array('required','trim','regex_match[/^[a-zñA-Záéíóú]+[A-Za-zñáéíóú\s]+[A-Za-zñáéíóú]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras')
			),
			array(
				'field'	=>	'sexo',
				'label'	=>	'Sexo.',
				'rules'	=>	array('required','trim','in_list[F,M,f,m]'),
				'errors'=>	array('in_list' => 'Debe seleccionar entre los valores {param}')
			),
			array(
				'field'	=>	'fecha_nac',
				'label'	=>	'Fechan Nac.',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]'),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'telefono_hab',
				'label'	=>	'Telefono Hab.',
				'rules'	=>	array('required','trim','numeric','min_length[7]','max_length[11]')
			),
			array(
				'field'	=>	'telefono_mov',
				'label'	=>	'Telefono Mov.',
				'rules'	=>	array('trim','numeric','min_length[7]','max_length[11]')
			),
		);
		$configRules['actualizar'] = array(
			array(
				'field'	=>	'identificacion',
				'label'	=>	'Identificacion',
				'rules'	=>	$idRules,
				'errors'=>	array('is_unique'=>'La persona ya existe')
			),
			array(
				'field'	=>	'nombres',
				'label'	=>	'Nombres',
				'rules'	=>	array('required','trim','regex_match[/^[a-zñA-Z]+[A-Za-zñ\s]+[A-Za-zñ]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras')
			),
			array(
				'field'	=>	'apellidos',
				'label'	=>	'Apellidos',
				'rules'	=>	array('required','trim','regex_match[/^[a-zñA-Z]+[A-Za-zñ\s]+[A-Za-zñ]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras')
			),
			array(
				'field'	=>	'sexo',
				'label'	=>	'Sexo.',
				'rules'	=>	array('required','trim','in_list[F,M,f,m]'),
				'errors'=>	array('in_list' => 'Debe seleccionar entre los valores {param}')
			),
			array(
				'field'	=>	'fecha_nac',
				'label'	=>	'Fechan Nac.',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]'),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'telefono_hab',
				'label'	=>	'Telefono Hab.',
				'rules'	=>	array('required','trim','numeric','min_length[7]','max_length[11]')
			),
			array(
				'field'	=>	'telefono_mov',
				'label'	=>	'Telefono Mov.',
				'rules'	=>	array('trim','numeric','min_length[7]','max_length[11]')
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
		$this->form_validation->set_rules($configRules[$case]);
		$this->form_validation->set_message($messageRules);
	}

	public function saveValues(){
		$this->fecha_nac = date_format(date_create($this->fecha_nac),'Y-m-d');
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->persona_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		$this->fecha_nac = date_format(date_create($this->fecha_nac),'Y-m-d');
		if (isset($this->persona_id)) {
			$this->db->set($this);
			$this->db->where('persona_id',$this->persona_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, persona_id nulo");
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