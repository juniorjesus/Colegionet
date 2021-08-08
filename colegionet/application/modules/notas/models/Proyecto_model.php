<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proyecto_model extends CI_Model {

	# defined field for this model
	private $table 	= "proyecto";
	public $proyecto_id;
	public $proyecto;
	public $grado_periodo_id;
	public $lapso_id;

	public function __construct($proyectoID = NULL){
		parent::__construct();
		if ($proyectoID != NULL) {
			$data = $this->getData(array('where' => array(array('proyecto_id' => $proyectoID))))->row();
			if ($data != null) {
				$this->proyecto_id 	= $data->proyecto_id;
				$this->proyecto 	= $data->proyecto;
				$this->grado_periodo_id	= $data->grado_periodo_id;
				$this->lapso_id		= $data->lapso_id;
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
			'proyecto_id' => 'Proyecto ID',
			'proyecto'	=> 'Proyecto',
			'grado_periodo_id' => 'Grado Periodo ID',
			'lapso_id' => 'Lapso ID'
		);
	}

	public function joins(){
		$this->db->join('grado_periodo','grado_periodo.grado_periodo_id = proyecto.grado_periodo_id');
		$this->db->join('grado','grado.grado_id = grado_periodo.grado_id');

		$this->db->join('lapso','lapso.lapso_id = proyecto.lapso_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'proyecto_id',
				'label'	=>	'Proyecto ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'proyecto',
				'label'	=>	'Proyecto',
				'rules'	=>	array('required','trim','max_length[100]','regex_match[/^[0-9a-zñA-Záéíóú]+[A-Z0-9a-zñáéíóú#-_\s]+[A-Z0-9a-zñáéíóú]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros'),
			),
			array(
				'field'	=>	'grado_periodo_id',
				'label'	=>	'Grado Periodo ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'lapso_id',
				'label'	=>	'Lapso ID',
				'rules'	=>	array('required','trim','numeric')
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
			$this->proyecto_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->evaluacion_id)) {
			$this->db->set($this);
			$this->db->where('proyecto_id',$this->proyecto_id);
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