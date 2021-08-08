<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscripcion_model extends CI_Model {

	# defined field for this model
	private $table 	= "inscripcion";
	public $inscripcion_id;
	public $estudiante_id;
	public $grado_periodo_id;
	public $periodo_id;
	public $fecha;

	public function __construct($inscripcionID = NULL){
		parent::__construct();
		if ($inscripcionID != NULL) {
			$data = $this->getData(array('where' => array(array('inscripcion_id' => $inscripcionID))))->row();
			if ($data != null) {
				$this->inscripcion_id = $data->inscripcion_id;
				$this->estudiante_id = $data->estudiante_id;
				$this->grado_periodo_id	= $data->grado_periodo_id;
				$this->periodo_id = $data->periodo_id;
				$this->fecha = $data->fecha;
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
			'inscripcion_id' => 'Inscripcion ID',
			'estudiante_id'	=> 'Estudiante ID',
			'grado_periodo_id'	=> 'Grado Periodo ID',
			'periodo_id'	=> 'Periodo ID',
			'fecha'	=> 'Fecha'
		);
	}

	public function joins(){
		$this->db->join('estudiante','estudiante.estudiante_id = inscripcion.estudiante_id');
		$this->db->join('persona','persona.persona_id = estudiante.persona_id');

		$this->db->join('grado_periodo','grado_periodo.grado_periodo_id = inscripcion.grado_periodo_id');
		$this->db->join('turno','turno.turno_id = grado_periodo.turno_id');
		$this->db->join('grado','grado.grado_id = grado_periodo.grado_id');

		$this->db->join('periodo','periodo.periodo_id = inscripcion.periodo_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'inscripcion_id',
				'label'	=>	'Inscripcion ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'estudiante_id',
				'label'	=>	'Estudiante',
				'rules'	=>	array('required','trim','numeric')	
			),
			array(
				'field'	=>	'grado_periodo_id',
				'label'	=>	'Numero',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'periodo_id',
				'label'	=>	'Seccion',
				'rules'	=>	array('required','trim','numeric')
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

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->inscripcion_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->inscripcion_id)) {
			$this->db->set($this);
			$this->db->where('inscripcion_id',$this->inscripcion_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, inscripcion_id nulo");
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