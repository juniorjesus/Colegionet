<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periodo_model extends CI_Model {

	# defined field for this model
	private $table 	= "periodo";
	public $periodo_id;
	public $descripcion;
	public $fecha_inicio;
	public $fecha_fin;
	public $fecha_inicio_inscripcion;
	public $fecha_fin_inscripcion;
	public $anio_inicio;
	public $anio_fin;
	public $estado_periodo_id;

	public function __construct($periodoID = NULL){
		parent::__construct();
		if ($periodoID != NULL) {
			$data = $this->getData(array('where' => array(array('periodo_id' => $periodoID))))->row();
			if ($data != null) {
				$this->periodo_id = $data->periodo_id;
				$this->descripcion 	= $data->descripcion;
				$this->fecha_inicio 	= $data->fecha_inicio;
				$this->fecha_fin 	= $data->fecha_fin;
				$this->fecha_inicio_inscripcion	= $data->fecha_inicio_inscripcion;
				$this->fecha_fin_inscripcion	= $data->fecha_fin_inscripcion;
				$this->anio_inicio	= $data->anio_inicio;
				$this->anio_fin	= $data->anio_fin;
				$this->estado_periodo_id	= $data->estado_periodo_id;
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
			'periodo_id'	=> 'Periodo ID',
			'descripcion'	=> 'Descripcion',
			'fecha_inicio'	=> 'Fecha inicio',
			'fecha_fin'		=> 'Fecha fin',
			'fecha_inicio_inscripcion'	=> 'Fecha inicio inscripcion',
			'fecha_fin_inscripcion'		=> 'Fecha fin inscripcion',
			'anio_inicio'	=> 'Año inicio',
			'anio_fin'		=> 'Año fin',
			'estado_periodo_id' => 'Estado periodo ID'
		);
	}

	public function joins(){
		$this->db->join('estado_periodo','estado_periodo.estado_periodo_id = periodo.estado_periodo_id');
	}

	public function rules($case){
		$configRules['crear'] = array(
			array(
				'field'	=>	'periodo_id',
				'label'	=>	'Periodo ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'descripcion',
				'label'	=>	'Descripcion',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Záéíóú]+[A-Z0-9a-zñáéíóú#-_\s]+[A-Z0-9a-zñáéíóú]$/]',
								'is_unique['.$this->table.'.descripcion]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros',
								'is_unique'=>'Ya existe un periodo con la misma descripcion')
			),
			array(
				'field'	=>	'fecha_inicio',
				'label'	=>	'Fecha inicio',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]','differs[fecha_fin]',
								array('validateFechaini',array($this,'validateFechaini'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'fecha_fin',
				'label'	=>	'Fecha fin',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]','differs[fecha_inicio]',
								array('validateFechafin',array($this,'validateFechafin'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'fecha_inicio_inscripcion',
				'label'	=>	'Fecha inicio inscripcion',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]',
								'differs[fecha_fin_inscripcion]',array('validateFechainiIns',array($this,'validateFechainiIns'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'fecha_fin_inscripcion',
				'label'	=>	'Fecha fin inscripcion',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]',
								'differs[fecha_inicio_inscripcion]',array('validateFechafinIns',array($this,'validateFechafinIns'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
		);
		$configRules['actualizar'] = array(
			array(
				'field'	=>	'periodo_id',
				'label'	=>	'Periodo ID',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'descripcion',
				'label'	=>	'Descripcion',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Z]+[A-Z0-9a-zñ#-_\s]+[A-Z0-9a-zñ]$/]',
								array('validateDescripcion',array($this,'validateDescripcion'))
							),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros')
			),
			array(
				'field'	=>	'fecha_inicio',
				'label'	=>	'Fecha inicio',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]','differs[fecha_fin]',
								array('validateFechaini',array($this,'validateFechaini'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'fecha_fin',
				'label'	=>	'Fecha fin',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]','differs[fecha_inicio]',
								array('validateFechafin',array($this,'validateFechafin'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'fecha_inicio_inscripcion',
				'label'	=>	'Fecha inicio inscripcion',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]',
								'differs[fecha_fin_inscripcion]',array('validateFechainiIns',array($this,'validateFechainiIns'))
							),
				'errors'=>	array('regex_match'=>'Debe ingresar una fecha en el formato dd-mm-yyyy')
			),
			array(
				'field'	=>	'fecha_fin_inscripcion',
				'label'	=>	'Fecha fin inscripcion',
				'rules'	=>	array('required','trim','regex_match[/^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$/]',
								'differs[fecha_inicio_inscripcion]',array('validateFechafinIns',array($this,'validateFechafinIns'))
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
		$this->form_validation->set_rules($configRules[$case]);
		$this->form_validation->set_message($messageRules);
	}

	public function validateDescripcion($value){
		$otroPeriodoDescripcion = $this->getData(array(
				'where' => array(array('descripcion' => $value))
			))->row();
		if ($otroPeriodoDescripcion != NULL) {
			if ($otroPeriodoDescripcion->periodo_id != $this->periodo_id) {
				$this->form_validation->set_message('validateDescripcion','Ya existe un periodo con la misma descripcion');
				return FALSE;
			}
		}
		return TRUE;
	}

	public function validateFechaini($value){
		$data = $this->input->post('periodo');
		$fechaFin = $data['fecha_fin'];
		$fechaFormateada = date_format(date_create($value),'Y-m-d');

		if ($this->fecha_inicio == NULL || ($this->fecha_inicio != NULL && $this->fecha_inicio != $fechaFormateada)) {
			if (strtotime($value) <= strtotime(date('d-m-Y'))) {
				$this->form_validation->set_message('validateFechaini','El campo {field} debe ser mayor a la fecha actual');
				return FALSE;
				
			} elseif (strtotime($value) >= strtotime($fechaFin)) {
				$this->form_validation->set_message('validateFechaini','El campo {field} debe ser menor al campo Fecha fin');
				return FALSE;
			}

			$otroPeriodoEntreFecha = $this->getData(array(
					'where' => array(array('fecha_inicio <= ' => $fechaFormateada,'fecha_fin >= ' => $fechaFormateada))
				))->row();
			if ($otroPeriodoEntreFecha != NULL) {
				$this->form_validation->set_message('validateFechaini','El campo {field} interfiere con las fechas de otro periodo');
				return FALSE;
			}
		}

		return TRUE;
	}

	public function validateFechafin($value){
		$data = $this->input->post('periodo');
		$fechaInicio = $data['fecha_inicio'];
		$fechaFormateada = date_format(date_create($value),'Y-m-d');

		if ($this->fecha_fin == NULL || ($this->fecha_fin != NULL && $this->fecha_fin != $fechaFormateada)) {
			if (strtotime($value) <= strtotime(date('d-m-Y'))) {
				$this->form_validation->set_message('validateFechafin','El campo {field} debe ser mayor a la fecha actual');
				return FALSE;
				
			} elseif (strtotime($value) <= strtotime($fechaInicio)) {
				$this->form_validation->set_message('validateFechafin','El campo {field} debe ser mayor al campo Fecha inicio');
				return FALSE;
			}

			$otroPeriodoEntreFecha = $this->getData(array(
					'where' => array(array('fecha_inicio <= ' => $fechaFormateada,'fecha_fin >= ' => $fechaFormateada))
				))->row();
			if ($otroPeriodoEntreFecha != NULL) {
				$this->form_validation->set_message('validateFechafin','El campo {field} interfiere con las fechas de otro periodo');
				return FALSE;
			}
		}

		return TRUE;
	}

	public function validateFechainiIns($value){
		$data = $this->input->post('periodo');
		$fechaFinIns = $data['fecha_fin_inscripcion'];
		$fechaInicio = $data['fecha_inicio'];
		$fechaFin = $data['fecha_fin'];
		$fechaFormateada = date_format(date_create($value),'Y-m-d');

		if ($this->fecha_inicio_inscripcion == NULL || ($this->fecha_inicio_inscripcion != NULL && $this->fecha_inicio_inscripcion != $fechaFormateada)) {
			if (strtotime($value) <= strtotime(date('d-m-Y'))) {
				$this->form_validation->set_message('validateFechainiIns','El campo {field} debe ser mayor a la fecha actual');
				return FALSE;
				
			} elseif (strtotime($value) >= strtotime($fechaFinIns)) {
				$this->form_validation->set_message('validateFechainiIns','El campo {field} debe ser menor al campo Fecha fin inscripcion');
				return FALSE;
			
			} elseif (strtotime($value) <= strtotime($fechaInicio)) {
				$this->form_validation->set_message('validateFechainiIns','El campo {field} debe ser mayor al campo Fecha inicio');
				return FALSE;
			
			} elseif (strtotime($value) >= strtotime($fechaFin)) {
				$this->form_validation->set_message('validateFechainiIns','El campo {field} debe ser menor al campo Fecha fin');
				return FALSE;
			}

		}

		return TRUE;
	}

	public function validateFechafinIns($value){
		$data = $this->input->post('periodo');
		$fechaInicioIns = $data['fecha_inicio_inscripcion'];
		$fechaInicio = $data['fecha_inicio'];
		$fechaFin = $data['fecha_fin'];
		$fechaFormateada = date_format(date_create($value),'Y-m-d');

		if ($this->fecha_fin_inscripcion == NULL || ($this->fecha_fin_inscripcion != NULL && $this->fecha_fin_inscripcion != $fechaFormateada)) {
			if (strtotime($value) <= strtotime(date('d-m-Y'))) {
				$this->form_validation->set_message('validateFechafinIns','El campo {field} debe ser mayor a la fecha actual');
				return FALSE;
				
			} elseif (strtotime($value) <= strtotime($fechaInicioIns)) {
				$this->form_validation->set_message('validateFechafinIns','El campo {field} debe ser mayor al campo Fecha inicio inscripcion');
				return FALSE;
			
			} elseif (strtotime($value) <= strtotime($fechaInicio)) {
				$this->form_validation->set_message('validateFechafinIns','El campo {field} debe ser mayor al campo Fecha inicio');
				return FALSE;
			
			} elseif (strtotime($value) >= strtotime($fechaFin)) {
				$this->form_validation->set_message('validateFechafinIns','El campo {field} debe ser menor al campo Fecha fin');
				return FALSE;
			}

		}

		return TRUE;
	}

	public function saveValues(){
		$this->fecha_inicio = date_format(date_create($this->fecha_inicio),'Y-m-d');
		$this->fecha_fin = date_format(date_create($this->fecha_fin),'Y-m-d');
		$this->fecha_inicio_inscripcion = date_format(date_create($this->fecha_inicio_inscripcion),'Y-m-d');
		$this->fecha_fin_inscripcion = date_format(date_create($this->fecha_fin_inscripcion),'Y-m-d');
		$this->anio_inicio = date_format(date_create($this->fecha_inicio),'Y');
		$this->anio_fin = date_format(date_create($this->fecha_fin),'Y');
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->periodo_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}
	
	public function updateValues(){
		$this->fecha_inicio = date_format(date_create($this->fecha_inicio),'Y-m-d');
		$this->fecha_fin = date_format(date_create($this->fecha_fin),'Y-m-d');
		$this->fecha_inicio_inscripcion = date_format(date_create($this->fecha_inicio_inscripcion),'Y-m-d');
		$this->fecha_fin_inscripcion = date_format(date_create($this->fecha_fin_inscripcion),'Y-m-d');
		$this->anio_inicio = date_format(date_create($this->fecha_inicio),'Y');
		$this->anio_fin = date_format(date_create($this->fecha_fin),'Y');
		if (isset($this->periodo_id)) {
			$this->db->set($this);
			$this->db->where('periodo_id',$this->periodo_id);
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