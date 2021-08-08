<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lapso extends MX_Controller {
	
	private $rules = array(
		'methods' => array('borrar','actualizar'),
		'filter' => array('borrar','actualizar'),
		'navs' => array(),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('borrar','actualizar'),
			'GET'	=> array(),
			'POST'	=> array('borrar','actualizar')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('lapso_model','lapso');
		$this->load->model('notas/proyecto_model','proyecto');
		$this->load->model('periodo_model','periodo');
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
	* @param $gradoPeriodoID int, id del grado periodo
	* borra el grado asociado al periodo
	*/
	public function borrar($lapsoID){
		$result['success'] = FALSE;
		$lapso = new $this->lapso($lapsoID);
		if ($lapso->lapso_id != NULL) {
			if ($this->input->is_ajax_request() && $this->input->post()) {
				$proyecto = $this->proyecto->getData(array(
						'where' => array(array('lapso_id' => $lapso->lapso_id))
					))->row();
				if ($proyecto == NULL) {
					if ($lapso->deleteValues()) {
						$result['success'] = TRUE;
					}else{
						$result['mensajeError'] = 'Ha ocurrido un error al borrar el grado del periodo';
					}
				} else {
					$result['mensajeError'] = 'Existen datos asociados al lapso. Imposible eliminar';
				}
			}
		}
		echo json_encode($result);
	}

	public function actualizar($lapsoID){
		$result['success'] = FALSE;
		$lapso = new $this->lapso($lapsoID);
		if ($lapso->lapso_id != NULL) {
			if ($this->input->is_ajax_request() && $this->input->post()) {
				$datos = $this->input->post('lapso');
				$datos['numero'] = $lapso->numero;
				$this->form_validation->set_data($datos);
				$lapso->rules();
				if ($this->form_validation->run() == TRUE) {
					$lapso->lapso_fecha_inicio = $datos['lapso_fecha_inicio'];
					$lapso->lapso_fecha_fin = $datos['lapso_fecha_fin'];
					if ($lapso->updateValues()) {
						$result['success'] = TRUE;
					}else{
						$result['mensajeError'] = 'Ha ocurrido un error al borrar el grado del periodo';
					}
				}else{
					$result['mensajeError'] = validation_errors();
				}
			}
		}
		echo json_encode($result);
	}

}