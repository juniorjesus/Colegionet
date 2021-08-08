<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_evaluacion_model extends CI_Model {

	# defined field for this model
	private $table 	= "detalle_evaluacion";
	public $detalle_evaluacion_id;
	public $evaluacion_id;
	public $indicador_id;
	public $calificacion;

	public function __construct($detalleEvaluacionID = NULL){
		parent::__construct();
		if ($detalleEvaluacionID != NULL) {
			$data = $this->getData(array('where' => array(array('detalle_evaluacion_id' => $detalleEvaluacionID))))->row();
			if ($data != null) {
				$this->detalle_evaluacion_id 	= $data->detalle_evaluacion_id;
				$this->evaluacion_id 	= $data->evaluacion_id;
				$this->indicador_id		= $data->indicador_id;
				$this->calificacion		= $data->calificacion;
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
			'detalle_evaluacion_id' => 'Detalle Evaluacion ID',
			'evaluacion_id'	=> 'Evaluacion ID',
			'indicador_id' => 'Indicador ID',
			'calificacion' => 'Calificacion'
		);
	}

	public function joins(){
		$this->db->join('evaluacion','evaluacion.evaluacion_id = detalle_evaluacion.evaluacion_id');
		$this->db->join('indicador','indicador.indicador_id = detalle_evaluacion.indicador_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'detalle_evaluacion_id',
				'label'	=>	'Detalle Evaluacion ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'evaluacion_id',
				'label'	=>	'Evaluacion Id',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'indicador_id',
				'label'	=>	'Indicador ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'calificacion',
				'label'	=>	'Calificacion',
				'rules'	=>	array('required','trim','in_list[E,B,R,M]'),
				'errors'=>	array('in_list' => 'Debe seleccionar entre los valores {param}')
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
			$this->detalle_evaluacion_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->detalle_evaluacion_id)) {
			$this->db->set($this);
			$this->db->where('detalle_evaluacion_id',$this->detalle_evaluacion_id);
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
		if (isset($this->detalle_evaluacion_id)) {
			$this->db->set($this);
			$this->db->where('detalle_evaluacion_id',$this->detalle_evaluacion_id);
			if ($this->db->delete($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos, detalle_evaluacion_id nulo");
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