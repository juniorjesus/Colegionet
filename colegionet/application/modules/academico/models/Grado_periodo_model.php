<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grado_periodo_model extends CI_Model {

	# defined field for this model
	private $table 	= "grado_periodo";
	public $grado_periodo_id;
	public $grado_id;
	public $periodo_id;
	public $turno_id;
	public $profesor_id;

	public function __construct($gradoPeriodoID = NULL){
		parent::__construct();
		if ($gradoPeriodoID != NULL) {
			$data = $this->getData(array('where' => array(array('grado_periodo_id' => $gradoPeriodoID))))->row();
			if ($data != null) {
				$this->grado_periodo_id = $data->grado_periodo_id;
				$this->grado_id 	= $data->grado_id;
				$this->periodo_id 	= $data->periodo_id;
				$this->turno_id 	= $data->turno_id;
				$this->profesor_id	= $data->profesor_id;
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
			'grado_periodo_id'	=> 'Grado Periodo ID',
			'grado_id'	=> 'Grado ID',
			'periodo_id'	=> 'Periodo ID',
			'turno_id'	=> 'Turno ID',
			'profesor_id'	=> 'Profesor ID'
		);
	}

	public function joins(){
		$this->db->join('grado','grado.grado_id = grado_periodo.grado_id');
		$this->db->join('periodo','periodo.periodo_id = grado_periodo.periodo_id');
		$this->db->join('turno','turno.turno_id = grado_periodo.turno_id','left');
		
		$this->db->join('profesor','profesor.profesor_id = grado_periodo.profesor_id','left');
		$this->db->join('persona','persona.persona_id = profesor.persona_id','left');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'grado_periodo_id',
				'label'	=>	'Grado Periodo ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'grado_id',
				'label'	=>	'Grado ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'periodo_id',
				'label'	=>	'Periodo ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'turno_id',
				'label'	=>	'Turno ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'profesor_id',
				'label'	=>	'Profesor ID',
				'rules'	=>	array('trim','numeric',array('profesorTurno',array($this,'profesorTurno')))
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

	public function profesorTurno($profesorID){
		$profesor = $this->grado_periodo->getData(array(
			'where' => array(array('profesor_id' => $profesorID,'periodo_id' => $this->periodo_id))
		))->result();

		$data = $this->input->post('gradoPeriodo');
		$turno = $data['turno_id'] != $this->turno_id ? $data['turno_id'] : $this->turno_id;

		log_message('error',$this->db->last_query());
		log_message('error',json_encode($profesor));
		log_message('error',$this->turno_id." ".$this->grado_periodo_id);

		foreach ($profesor as $key => $value) {
			if ($value->grado_periodo_id != $this->grado_periodo_id && $value->turno_id == $turno) {
				$this->form_validation->set_message('profesorTurno','El profesor ya se encuentra asignado para el mismo turno en otro grado');
				return FALSE;
			}
		}
		return TRUE;
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->grado_periodo_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->grado_periodo_id)) {
			$this->db->set($this);
			$this->db->where('grado_periodo_id',$this->grado_periodo_id);
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

	public function deleteValues(){
		if (isset($this->grado_periodo_id)) {
			$this->db->set($this);
			$this->db->where('grado_periodo_id',$this->grado_periodo_id);
			if ($this->db->delete($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos, grado_periodo_id nulo");
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