<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lapso_model extends CI_Model {

	# defined field for this model
	private $table 	= "lapso";
	public $lapso_id;
	public $numero;
	public $lapso_fecha_inicio;
	public $lapso_fecha_fin;
	public $periodo_id;

	public function __construct($lapsoID = NULL){
		parent::__construct();
		if ($lapsoID != NULL) {
			$data = $this->getData(array('where' => array(array('lapso_id' => $lapsoID))))->row();
			if ($data != null) {
				$this->lapso_id = $data->lapso_id;
				$this->numero 	= $data->numero;
				$this->lapso_fecha_inicio = $data->lapso_fecha_inicio;
				$this->lapso_fecha_fin = $data->lapso_fecha_fin;
				$this->periodo_id = $data->periodo_id;
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
			'lapso_id'	=> 'Lapso ID',
			'numero'	=> 'Numero',
			'lapso_fecha_inicio'	=> 'Fecha inicio',
			'lapso_fecha_fin'	=> 'Fecha fin',
			'periodo_id'	=> 'Periodo ID'
		);
	}

	public function joins(){
		$this->db->join('periodo','periodo.periodo_id = lapso.periodo_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'lapso_id',
				'label'	=>	'Lapso ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'numero',
				'label'	=>	'Numero',
				'rules'	=>	array('required','trim','integer','exact_length[1]')
			),
			array(
				'field'	=>	'lapso_fecha_inicio',
				'label'	=>	'Fecha inicio',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]','differs[lapso_fecha_fin]',
								array('validateFechaini',array($this,'validateFechaini'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'lapso_fecha_fin',
				'label'	=>	'Fecha fin',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]','differs[lapso_fecha_inicio]',
								array('validateFechafin',array($this,'validateFechafin'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
		);
		$messageRules = array(
			'required'				=>	'El campo {field} es obligatorio',
			'min_length'			=>	'El campo {field} debe contener minimo {param} caracteres',
			'max_length'			=>	'El campo {field} debe contener maximo {param} caracteres',
			'alpha_numeric'			=>	'El campo {field} solo puede contener caracteres alfa numericos sin espacios',
			'alpha_numeric_spaces'	=>	'El campo {field} solo puede contener caracteres alfa numericos',
			'numeric'				=>	'El campo {field} solo puede contener numeros',
			'differs'				=>	'El campo {field} debe ser diferente al campo {param}'
		);
		$this->form_validation->set_rules($configRules);
		$this->form_validation->set_message($messageRules);
	}

	public function validateFechaini($value){
		$data = $this->input->post('lapso');
		$fechaFin = $data['lapso_fecha_fin'];

		$fechaFormateada = date_format(date_create($value),'Y-m-d');

		if ($this->lapso_fecha_inicio == NULL || ($this->lapso_fecha_inicio != NULL && $this->lapso_fecha_inicio != $fechaFormateada)) {
			if (strtotime($value) <= strtotime(date('d-m-Y'))) {
				$this->form_validation->set_message('validateFechaini','El campo {field} debe ser mayor a la fecha actual');
				return FALSE;
				
			} elseif (strtotime($value) >= strtotime($fechaFin)) {
				$this->form_validation->set_message('validateFechaini','El campo {field} debe ser menor al campo Fecha fin');
				return FALSE;
			}

			$fechasPeriodo = $this->periodo->getData(array(
					'where' => array(array(
							'periodo_id' => $this->periodo_id,
							'fecha_inicio < ' => $fechaFormateada,
							'fecha_fin > ' => $fechaFormateada
					))
				))->row();
			if ($fechasPeriodo == NULL) {
				$this->form_validation->set_message('validateFechaini','El campo {field} debe estar entre el rango de fechas del periodo');
				return FALSE;
			}else{
				$otroLapsoPeriodo = $this->getData(array(
						'where' => array(array(
								'periodo_id' => $this->periodo_id,
								'lapso_fecha_inicio < ' => $fechaFormateada,
								'lapso_fecha_fin > ' => $fechaFormateada
						))
					))->row();

				if ($otroLapsoPeriodo != NULL && $otroLapsoPeriodo->lapso_id != $this->lapso_id) {
					$this->form_validation->set_message('validateFechaini','El campo {field} interfiere con las fechas de otro lapso');
					return FALSE;
				}

			}
		}

		return TRUE;
	}

	public function validateFechafin($value){
		$data = $this->input->post('lapso');
		$fechaInicio = $data['lapso_fecha_inicio'];
		$fechaFormateada = date_format(date_create($value),'Y-m-d');

		if ($this->lapso_fecha_fin == NULL || ($this->lapso_fecha_fin != NULL && $this->lapso_fecha_fin != $fechaFormateada)) {
			if (strtotime($value) <= strtotime(date('d-m-Y'))) {
				$this->form_validation->set_message('validateFechafin','El campo {field} debe ser mayor a la fecha actual');
				return FALSE;
				
			} elseif (strtotime($value) <= strtotime($fechaInicio)) {
				$this->form_validation->set_message('validateFechafin','El campo {field} debe ser mayor al campo Fecha inicio');
				return FALSE;
			}

			$fechasPeriodo = $this->periodo->getData(array(
					'where' => array(array(
							'periodo_id' => $this->periodo_id,
							'fecha_inicio < ' => $fechaFormateada,
							'fecha_fin > ' => $fechaFormateada
					))
				))->row();
			if ($fechasPeriodo == NULL) {
				$this->form_validation->set_message('validateFechafin','El campo {field} debe estar entre el rango de fechas del periodo');
				return FALSE;
			}else{
				$otroLapsoPeriodo = $this->getData(array(
						'where' => array(array(
								'periodo_id' => $this->periodo_id,
								'lapso_fecha_inicio < ' => $fechaFormateada,
								'lapso_fecha_fin > ' => $fechaFormateada
						))
					))->row();

				if ($otroLapsoPeriodo != NULL && $otroLapsoPeriodo->lapso_id != $this->lapso_id) {
					$this->form_validation->set_message('validateFechafin','El campo {field} interfiere con las fechas de otro lapso');
					return FALSE;
				}

			}
		}

		return TRUE;
	}

	public function saveValues(){
		$this->lapso_fecha_inicio = date_format(date_create($this->lapso_fecha_inicio),'Y-m-d');
		$this->lapso_fecha_fin = date_format(date_create($this->lapso_fecha_fin),'Y-m-d');
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->lapso_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}
	
	public function updateValues(){
		$this->lapso_fecha_inicio = date_format(date_create($this->lapso_fecha_inicio),'Y-m-d');
		$this->lapso_fecha_fin = date_format(date_create($this->lapso_fecha_fin),'Y-m-d');
		if (isset($this->lapso_id)) {
			$this->db->set($this);
			$this->db->where('lapso_id',$this->lapso_id);
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

	public function deleteValues(){
		if (isset($this->lapso_id)) {
			$this->db->set($this);
			$this->db->where('lapso_id',$this->lapso_id);
			if ($this->db->delete($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al borrar los datos, lapso_id nulo");
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