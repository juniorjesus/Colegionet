<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GradoPeriodo extends MX_Controller {
	
	private $rules = array(
		'methods' => array('actualizar','borrar'),
		'filter' => array('actualizar','borrar'),
		'navs' => array(),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('actualizar','borrar'),
			'GET'	=> array(),
			'POST'	=> array('actualizar','borrar')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('grado_model','grado');
		$this->load->model('grado_periodo_model','grado_periodo');
		//$this->load->model('estado_periodo_model','estado_periodo');
	}

	public function _remap($method,$params = array()){
		$method = $method == 'index' ? 'leer' : $method;
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this,$method), $params);
		}
		show_404();
	}

	/**
	* @param $gradoID int, id del grado
	* actualiza los datos de un grado
	*/
	public function actualizar($gradoPeriodoID){
		$result['success'] = FALSE;
		$gradoPeriodo = new $this->grado_periodo($gradoPeriodoID);
		if ($gradoPeriodo->grado_periodo_id != NULL) {
			if ($this->input->post()) {
				
				$data = $this->input->post('gradoPeriodo');
				$data['grado_id'] = $gradoPeriodo->grado_id;
				$data['periodo_id'] = $gradoPeriodo->periodo_id;
				$this->form_validation->set_data($data);
				$gradoPeriodo->rules();
				
				$siProfesorAgregado = $this->grado_periodo->getData(array(
					'where' => array(array('profesor_id' => $data['profesor_id']))
				))->row();

				if ($this->form_validation->run() == TRUE) {
					
					$gradoPeriodo->turno_id = $data['turno_id'];
					$gradoPeriodo->profesor_id = $data['profesor_id'];

					if ($gradoPeriodo->updateValues()) {
						$result['success'] = true;
					}else{
						$result['mensajeError'] = "Ha ocurrido un error al actualizar el periodo";
					}
				}else{
					$result['mensajeError'] = validation_errors();
				}
			}
			echo json_encode($result);
		}else {
			show_404();
		}
	}

	/**
	* @param $gradoPeriodoID int, id del grado periodo
	* borra el grado asociado al periodo
	*/
	public function borrar($gradoPeriodoID){
		$result['success'] = FALSE;
		$gradoPeriodo = new $this->grado_periodo($gradoPeriodoID);
		if ($gradoPeriodo->grado_periodo_id != NULL) {
			if ($this->input->is_ajax_request() && $this->input->post()) {
				//verificar por inscritos
				if ($gradoPeriodo->deleteValues()) {
					$result['success'] = TRUE;
				}else{
					$result['mensajeError'] = 'Ha ocurrido un error al borrar el grado del periodo';
				}
			}
		}
		echo json_encode($result);
	}

}