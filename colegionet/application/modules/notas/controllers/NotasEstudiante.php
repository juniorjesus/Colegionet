<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotasEstudiante extends MX_Controller {
	
	private $rules = array(
		'methods' => array('lapsos'),
		'filter' => array('lapsos'),
		'navs' => array(),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array(),
			'GET'	=> array(),
			'POST'	=> array()
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('inscripciones/inscripcion_model','inscripcion');
		$this->load->model('evaluacion_model','evaluacion');
		$this->load->model('detalle_evaluacion_model','detalle_evaluacion');
		//$this->load->model('estado_periodo_model','estado_periodo');
	}

	public function _remap($method,$params = array()){
		$method = $method == 'index' ? 'leer' : $method;
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this,$method), $params);
		}
		show_404();
	}

	public function lapsos($inscripcionID){
		$estudiante = $this->session->estudiante;
		if ($estudiante == NULL) {
			show_error('Solo estudiantes',401,'Area restringida');
		}

		$inscripcion = new $this->inscripcion($inscripcionID);
		if ($inscripcion->inscripcion_id != NULL) {
			$datosLapsos = $this->inscripcion->getdata(array(
				'where' => array(array('inscripcion_id' => $inscripcionID,'inscripcion.estudiante_id' => $estudiante->estudiante_id)),
				'join' => array('lapso','lapso.periodo_id = periodo.periodo_id'),
				'select' => array('lapso.*')
			),TRUE)->result();

			$datos = array();
			if (count($datosLapsos) > 0) {
				foreach ($datosLapsos as $key => $value) {
					$datosEvaluacion = $this->evaluacion->getData(array(
							'where' => array(array('evaluacion.inscripcion_id' => $inscripcionID,'proyecto.lapso_id' => $value->lapso_id))
						),TRUE)->row();

					$datosDetalleEvaluacion = array();
					if ($datosEvaluacion != NULL) {
						$datosDetalleEvaluacion = $this->detalle_evaluacion->getdata(array(
								'where' => array(array('detalle_evaluacion.evaluacion_id' => $datosEvaluacion->evaluacion_id)),
								'select' => array(array('indicador','calificacion'))
							),TRUE)->result_array();
					}
					$datos[] = array(
						'lapso' => $value,
						'evaluacion' => $datosEvaluacion,
						'detalleEvaluacion' => $datosDetalleEvaluacion
					);
				}	
			}
			$this->load->view('header',array('title' => 'Lapsos'));
			$this->load->view('notasestudiante/lapsos',array(
					'datos'=>$datos
				)
			);
		} else {
			show_404();
		}
	}

}