<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscripcion extends MX_Controller {
	
	private $rules = array(
		'methods' => array('inscribir','obtenerDatosEstudiante','procesarInscripcion','leer','consultarInscripciones','comprobanteInscripcionPDF'),
		'filter' => array('inscribir','obtenerDatosEstudiante','procesarInscripcion','leer','consultarInscripciones','comprobanteInscripcionPDF'),
		'navs' => array('inscribir'=>'Inscribir','leer' => 'Inscripciones','consultarInscripciones' => 'Consultar'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('leer','obtenerDatosEstudiante','procesarInscripcion'),
			'GET'	=> array('leer'),
			'POST'	=> array('obtenerDatosEstudiante','procesarInscripcion')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('academico/periodo_model','periodo');
		$this->load->model('academico/grado_periodo_model','grado_periodo');
		$this->load->model('academico/grado_model','grado');
		$this->load->model('personas/estudiante_model','estudiante');
		$this->load->model('inscripcion_model','inscripcion');
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
	* @param $page int numero de offset para query de paginacion
	* funcion leer = index
	*/
	public function leer($page = '0'){
		$busqueda = array();
		if ($this->input->is_ajax_request() && $this->input->get()) {
			$data = $this->input->get('inscripcion');
			if ($data['periodo_id'] != '') {
				$busqueda['like'][0]['inscripcion.periodo_id'] = $data['periodo_id'];
			}
			if ($data['identificacion'] != '') {
				$busqueda['like'][0]['persona.identificacion'] = $data['identificacion'];
			}
			if ($data['matricula'] != '') {
				$busqueda['like'][0]['estudiante.matricula'] = $data['matricula'];
			}
			if ($data['grado_id'] != '') {
				$busqueda['like'][0]['grado.grado_id'] = $data['grado_id'];
			}
		}
		$totalInscripciones = $this->inscripcion->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('inscripciones/inscripcion/leer');
		$config['total_rows'] = $totalInscripciones;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$busqueda['select'] = array(array('inscripcion_id','descripcion','matricula','identificacion','nombres','apellidos','grado','fecha'));
		$inscripciones = $this->inscripcion->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();

		$this->load->view('header',array('title'=>'Inscripciones'));
		$this->load->view('inscripcion/leer',array(
				'inscripciones' => $inscripciones,
				'pagNums'	=> $pagNums,
			)
		);
	}

	public function inscribir($page = '0'){
		$this->load->view('header',array('title'=>'Inscripcion'));
		$this->load->view('inscripcion/inscribir');
	}

	/**
	* @param none
	* obtiene los datos del estudiante para la inscripcion
	*/
	public function obtenerDatosEstudiante(){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			
			$periodoActual = $this->periodo->getData(array(
					'where' => array(array('estado_periodo_id' => 1))//means activo
				))->row();
			
			$identificacion = $this->input->post('identificacion');
			$datosEstudiante = $this->estudiante->getData(array(
					'or_where' => array(array('estudiante.matricula' => $identificacion, 'persona.identificacion' => $identificacion))
				),TRUE)->row();
			
			if ($datosEstudiante != NULL) {
				if ($periodoActual != NULL) {
					$existeInscripcion = $this->inscripcion->getData(array(
							'where' => array(array('estudiante_id' => $datosEstudiante->estudiante_id,'periodo_id' => $periodoActual->periodo_id))
						))->row();
					
					if ($existeInscripcion == NULL) {
						$fechaActual = date('Y-m-d');

						if (strtotime($fechaActual) >= strtotime($periodoActual->fecha_inicio_inscripcion) &&
							strtotime($fechaActual) <= strtotime($periodoActual->fecha_fin_inscripcion)) {
							
							$result['datosEstudiante'] = $datosEstudiante;

							$this->prepararDatosInscripcion($datosEstudiante,$periodoActual,$result);

						} else {
							$result['mensajeError'] = 'No es posible procesar inscripcion fuera del rango de fechas';
						}
					} else {
						$result['mensajeError'] = 'El estudiante ya se encuentra inscrito';
					}
				} else {
					$result['mensajeError'] = 'No existe un periodo activo para procesar inscripcion';
				}
			} else {
				$result['mensajeError'] = 'No existe un estudiante con el dato suministrado';
			}

		}
		echo json_encode($result);
	}

	/**
	* @param $datosEstudiante datos del estudiante
	* @param $periodoActual datos del periodo
	* @param $result para mostrar los resultados obtenidos en la preparacion de los datos para la isncripcion
	*/
	private function prepararDatosInscripcion($datosEstudiante,$periodoActual,&$result){
		
		$inscripcionesAnteriores = $this->inscripcion->getData(array(
				'where' => array(array('inscripcion.estudiante_id' => $datosEstudiante->estudiante_id)),
				'order_by'	=> array('grado.numero,grado.seccion'),
			),TRUE)->result_array();

		if (count($inscripcionesAnteriores) > 0) {
			$dataGradosDisponibles = $this->grado_periodo->getData(array(
				'where' => array(array('grado.numero >' => max(array_column($inscripcionesAnteriores, 'numero')))),
				'order_by'	=> array('grado.numero,grado.seccion'),
				'limit' => array(1)
			),TRUE)->result();

		}else{
			//todos los grados y que se decida cual inscribir
			$dataGradosDisponibles = $this->grado_periodo->getData(array(
				'order_by'	=> array('grado.numero,grado.seccion'),
			),TRUE)->result();
		}

		if (count($dataGradosDisponibles) > 0) {
		
			$gradosDisponibles = array('' => 'Seleccione');
			foreach ($dataGradosDisponibles as $key => $value) {
				$gradosDisponibles[$value->grado_periodo_id] = $value->grado." [ ".$value->numero." ][ ".$value->seccion." ][ ".$value->turno."]";
			}
			$result['success'] = TRUE;
			$result['view'] = $this->load->view('inscripcion/_previaDatosInscripcion',
				array('datosEstudiante' => $datosEstudiante,'grados' => $gradosDisponibles,'periodo' => $periodoActual),
				TRUE
			);
			
		} else {
			$result['mensajeError'] = 'No es posible procesar inscripcion por que el estudiante no tiene grados disponibles para inscribir';
		}
		return;

	}

	/**
	* @param none
	* Procesa y guarda una inscripcion
	*/
	public function procesarInscripcion(){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$data = $this->input->post('inscripcion');
			if ($data['estudiante_id'] && $data['periodo_id'] && $data['grado_periodo_id']) {
				
				$existeInscripcion = $this->inscripcion->getData(array(
							'where' => array(array('estudiante_id' => $data['estudiante_id'],'periodo_id' => $data['periodo_id']))
						))->row();
				
				if ($existeInscripcion == NULL) {
					$verificarInscripcionGrado = $this->inscripcion->getdata(array(
							'where' => array(array('estudiante_id' => $data['estudiante_id'], 'grado_periodo_id' => $data['grado_periodo_id']))
						))->row();
					if ($verificarInscripcionGrado == NULL) {
						
						$inscripcion = new $this->inscripcion;
						$this->form_validation->set_data($data);
						$inscripcion->rules();
						if ($this->form_validation->run() == TRUE) {
							$inscripcion->periodo_id = $data['periodo_id'];
							$inscripcion->estudiante_id = $data['estudiante_id'];
							$inscripcion->grado_periodo_id = $data['grado_periodo_id'];
							$inscripcion->fecha = date('Y-m-d');

							if ($inscripcion->saveValues()) {
								$result['success'] = TRUE;
								$result['inscripcionID'] = $inscripcion->inscripcion_id;
								$result['mensaje'] = 'Inscripcion procesada existosamente';
							} else {
								$result['mensajeError'] = 'Ha ocurrido un error al procesar la inscripcion';
							}
						}else{
							$result['mensajeError'] = validation_errors();
						}
					}else{
						$result['mensajeError'] = 'El estudiante ya ha sido inscrito anteriormente en el grado seleccionado';
					}
				} else {
					$result['mensajeError'] = 'El estudiante ya se encuentra inscrito';
				}
			} else {
				$result['mensajeError'] = 'Datos imcompletos, verifique';
			}
		}
		echo json_encode($result);
	}

	/**
	* @param none
	* consulta inscripciones de un estudiante perfil para estudiante
	*/
	public function consultarInscripciones(){
		$estudiante = $this->session->estudiante;
		if ($estudiante == NULL) {
			show_error('Solo estudiantes',401,'Area restringida');
		}

		$inscripciones = $this->inscripcion->getdata(array(
				'where' => array(array('inscripcion.estudiante_id' => $estudiante->estudiante_id)),
				'select' => array(array('periodo.descripcion','grado','fecha','inscripcion_id'))
			),TRUE)->result_array();

		$this->load->view('header',array('title'=>'Inscripciones'));
		$this->load->view('inscripcion/consultarInscripciones',array(
				'inscripciones' => $inscripciones
			)
		);
	}

	/**
	* @param $inscripcionID, id de la inscripcion;
	* consulta inscripciones de un estudiante perfil para estudiante
	*/
	public function comprobanteInscripcionPDF($inscripcionID){
		$inscripcion = new $this->inscripcion($inscripcionID);

		if ($inscripcion->inscripcion_id != NULL) {
			$infoInscripcion = $this->inscripcion->getData(array(
					'where' => array(array('inscripcion_id' => $inscripcionID))
				),TRUE)->row();
			$this->load->library('html2pdf');
			
			$pdf = new $this->html2pdf;
			$pdf->paper('A4','portrait');
			$pdf->folder(base_url());

			$pdf->html(utf8_decode($this->load->view('inscripcion/comprobanteInscripcionPDF',array(
							'inscripcion' => $infoInscripcion
						),TRUE
					)
				)
			);
			$pdf->create();

		}else{
			show_404();
		}
	}
}