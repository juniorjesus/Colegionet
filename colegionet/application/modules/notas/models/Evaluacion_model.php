<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluacion_model extends CI_Model {

	# defined field for this model
	private $table 	= "evaluacion";
	public $evaluacion_id;
	public $inscripcion_id;
	public $proyecto_id;
	public $observaciones;
	public $inasistencias;
	public $representante_id;

	public function __construct($evaluacionID = NULL){
		parent::__construct();
		if ($evaluacionID != NULL) {
			$data = $this->getData(array('where' => array(array('evaluacion_id' => $evaluacionID))))->row();
			if ($data != null) {
				$this->evaluacion_id 	= $data->evaluacion_id;
				$this->inscripcion_id 	= $data->inscripcion_id;
				$this->proyecto_id		= $data->proyecto_id;
				$this->observaciones 	= $data->observaciones;
				$this->inasistencias 	= $data->inasistencias;
				$this->representante_id 	= $data->representante_id;
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
			'evaluacion_id'	=> 'Evaluacion ID',
			'inscripcion_id' => 'Inscripcion ID',
			'proyecto_id'	=> 'Proyecto ID',
			'observaciones' => 'Observaciones',
			'inasistencias' => 'Inasistencias',
			'representante_id' => 'Representante ID'
		);
	}

	public function joins(){
		$this->db->join('proyecto','proyecto.proyecto_id = evaluacion.proyecto_id');
		$this->db->join('inscripcion','inscripcion.inscripcion_id = evaluacion.inscripcion_id');

		$this->db->join('representante','representante.representante_id = evaluacion.representante_id','left');
		$this->db->join('persona','persona.persona_id = representante.persona_id','left');

	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'evaluacion_id',
				'label'	=>	'Evaluacion ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'inscripcion_id',
				'label'	=>	'Inscripcion ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'proyecto_id',
				'label'	=>	'Proyecto ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'observaciones',
				'label'	=>	'Observaciones',
				'rules'	=>	array('trim','max_length[500]','regex_match[/^[0-9a-zñA-Záéíóú]+[A-Z0-9a-zñáéíóú#.,\s]+[A-Z0-9a-zñáéíóú.]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros'),
			),
			array(
				'field'	=>	'inasistencias',
				'label'	=>	'Inasistencias',
				'rules'	=>	array('trim','integer')
			),
			array(
				'field'	=>	'representante_id',
				'label'	=>	'Representante ID',
				'rules'	=>	array('trim','integer')
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
			$this->evaluacion_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->evaluacion_id)) {
			$this->db->set($this);
			$this->db->where('evaluacion_id',$this->evaluacion_id);
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