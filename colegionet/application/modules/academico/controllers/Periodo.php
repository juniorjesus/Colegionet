<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periodo extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','borrar','ver','activar'),
		'filter' => array('crear','leer','actualizar','borrar','ver','activar'),
		'navs' => array('leer'=>'Periodo'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('leer','borrar','activar'),
			'GET'	=> array('leer'),
			'POST'	=> array('crear','actualizar','borrar','activar')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('periodo_model','periodo');
		$this->load->model('lapso_model','lapso');
		$this->load->model('grado_model','grado');
		$this->load->model('grado_periodo_model','grado_periodo');
		$this->load->model('estado_periodo_model','estado_periodo');
		$this->load->model('turno_model','turno');
		$this->load->model('personas/profesor_model','profesor');
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
			$data = $this->input->get('periodo');
			if ($data['periodo_id'] != '') {
				$busqueda['like'][0]['periodo_id'] = $data['periodo_id'];
			}
			if ($data['descripcion'] != '') {
				$busqueda['like'][0]['descripcion'] = $data['descripcion'];
			}
			if ($data['estado_periodo_id'] != '') {
				$busqueda['like'][0]['periodo.estado_periodo_id'] = $data['estado_periodo_id'];
			}
		}
		$totalPeriodos = $this->periodo->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('academico/periodo/leer');
		$config['total_rows'] = $totalPeriodos;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$periodos = $this->periodo->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Periodos'));
		$this->load->view('periodo/leer',array(
				'periodos' => $periodos,
				'pagNums'	=> $pagNums,
			)
		);
	}

	/**
	* @param none
	* crea un periodo
	*/
	public function crear(){
		$mensaje = array();
		if ($this->input->post()) {
			$periodo = new $this->periodo;
			$data = $this->input->post('periodo');
			$this->form_validation->set_data($data);
			$periodo->rules('crear');

			$periodoPorActivar = $this->periodo->getData(array('where' => array(array('estado_periodo_id' => 3))))->row();
			
			if ($periodoPorActivar == NULL) {
				if ($this->form_validation->run() == TRUE) {					
					$periodo->descripcion = $data['descripcion'];
					$periodo->fecha_inicio = $data['fecha_inicio'];
					$periodo->fecha_fin = $data['fecha_fin'];
					$periodo->fecha_inicio_inscripcion = $data['fecha_inicio_inscripcion'];
					$periodo->fecha_fin_inscripcion = $data['fecha_fin_inscripcion'];
					$periodo->estado_periodo_id = 3; //periodo por activar
					
					if ($periodo->saveValues()) {
						$this->session->set_flashdata('success','Periodo creado satisfactoriamente');
						redirect('academico/periodo/actualizar/'.$periodo->periodo_id,'refresh');
					} else {
						$mensaje[] = "Ha ocurrido un error al crear el periodo";
					}
				} 
			} else {
				$mensaje[] = "No es posible crear el periodo por que ya existe un periodo por activar";
			}
		}
		$this->load->view('header',array('title'=>'Registrar periodo'));
		$this->load->view('periodo/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param $periodoID int, id del periodo
	* actualiza los datos de un periodo
	*/
	public function actualizar($periodoID){
		$mensaje['periodo'] = array();
		$mensaje['gradoPeriodo'] = array();
		$mensaje['lapso'] = array();
		$dataPeriodo = $this->periodo->getData(array(
				'where' => array(array('periodo_id' => $periodoID))
			),true)->row();
		if ($dataPeriodo != NULL) {
			if ($dataPeriodo->estado_periodo_id == 2) {
				show_error('Imposible acceder, no se permite la actualizacion de datos de un periodo finalizado',403,'Periodo Finalizado');
			}

			if ($this->input->post()) {
				if ($this->input->post('periodo')) {
					$this->actualizarPeriodo($dataPeriodo,$mensaje);
				}

				if ($this->input->post('gradoPeriodo')) {
					$this->agregarGradoPeriodo($periodoID,$mensaje);
				}

				if ($this->input->post('lapso')) {
					$this->agregarLapso($periodoID,$mensaje);
				}
			}

			$dataGradoPeriodo = $this->grado_periodo->getData(array(
					'where' => array(array('grado_periodo.periodo_id' => $periodoID)),
					'select' => array(array('*','concat(persona.apellidos," ",persona.nombres) as profesor'))
				),TRUE)->result_array();

			$dataLapsos = $this->lapso->getData(array(
					'where' => array(array('periodo_id' => $periodoID)),
					'order_by' => array('numero')
				))->result_array();

			$this->load->view('header',array('title'=>'Actualizar periodo'));
			$this->load->view('periodo/actualizar',array(
					'periodo' => $dataPeriodo,
					'gradosPeriodo' => $dataGradoPeriodo,
					'lapsos' => $dataLapsos,
					'mensaje' => $mensaje,
				)
			);
		}else {
			show_404();
		}
	}

	private function actualizarPeriodo($dataPeriodo,&$mensaje){
		$periodo = new $this->periodo;	
		//para validar
		$periodo->periodo_id = $dataPeriodo->periodo_id;
		$periodo->estado_periodo_id = $dataPeriodo->estado_periodo_id;
		$periodo->fecha_inicio = $dataPeriodo->fecha_inicio;
		$periodo->fecha_fin = $dataPeriodo->fecha_fin;
		$periodo->fecha_inicio_inscripcion = $dataPeriodo->fecha_inicio_inscripcion;
		$periodo->fecha_fin_inscripcion = $dataPeriodo->fecha_fin_inscripcion;
		//
		$data = $this->input->post('periodo');
		$this->form_validation->set_data($data);
		$periodo->rules('actualizar');
		if ($this->form_validation->run() == TRUE) {
			
			$periodo->descripcion = $data['descripcion'];
			$periodo->fecha_inicio = $data['fecha_inicio'];
			$periodo->fecha_fin = $data['fecha_fin'];
			$periodo->fecha_inicio_inscripcion = $data['fecha_inicio_inscripcion'];
			$periodo->fecha_fin_inscripcion = $data['fecha_fin_inscripcion'];

			if ($periodo->updateValues()) {
				$this->session->set_flashdata('success','Periodo actualizado satisfactoriamente');
				redirect('academico/periodo/actualizar/'.$periodo->periodo_id,'refresh');
			}else{
				$mensaje['periodo'][] = "Ha ocurrido un error al actualizar el periodo";
			}
		}else{
			$mensaje['periodo'][] = validation_errors();
		}
	}

	private function agregarLapso($periodoID,&$mensaje){
		$data = $this->input->post('lapso');
		$data['periodo_id'] = $periodoID;
		
		$lapso = $this->lapso;
		$lapso->periodo_id = $periodoID;

		$siLapsoAgregado = $this->lapso->getdata(array(
			'where' => array(array('numero' => $data['numero'],'periodo_id' => $periodoID))
		))->row();

		if ($siLapsoAgregado == NULL) {
			
			$this->form_validation->set_data($data);
			$lapso->rules();
			if ($this->form_validation->run() == TRUE) {
				
				$lapso->lapso_fecha_inicio = $data['lapso_fecha_inicio'];
				$lapso->lapso_fecha_fin = $data['lapso_fecha_fin'];
				$lapso->numero = $data['numero'];
				if ($lapso->saveValues()) {
					$this->session->set_flashdata('success','Lapso agregado satisfactoriamente');
					redirect('academico/periodo/actualizar/'.$periodoID,'refresh');	
				} else {
					$mensaje['lapso'][] = 'Ha ocurrido un error al crear el lapso';
				}
			}else{
				$mensaje['lapso'][]	= validation_errors();
			}

		} else {
			$mensaje['lapso'][] = 'El lapso que intenta agregar ya se encuentra asociado al periodo';
		}
	}

	private function agregarGradoPeriodo($periodoID,&$mensaje){
		$data = $this->input->post('gradoPeriodo');
		$gradoPeriodo = new $this->grado_periodo;

		$siGradoAgregado = $this->grado_periodo->getdata(array(
			'where' => array(array('grado_id'=>$data['grado_id'],'periodo_id' => $periodoID))
		))->row();

		if ($siGradoAgregado == NULL) {
			$data['periodo_id'] = $periodoID;
			$this->form_validation->set_data($data);
			$gradoPeriodo->rules();
			if ($this->form_validation->run() == TRUE) {
				
				$gradoPeriodo->grado_id = $data['grado_id'];
				$gradoPeriodo->periodo_id = $data['periodo_id'];
				$gradoPeriodo->turno_id = $data['turno_id'];
				if ($gradoPeriodo->saveValues()) {
					$this->session->set_flashdata('success','Grado agregado satisfactoriamente');
					redirect('academico/periodo/actualizar/'.$periodoID,'refresh');	
				}else{
					$mensaje['gradoPeriodo'][] = 'Ha ocurrido un error al agregar un grado al periodo';
				}
			} else {
				$mensaje['gradoPeriodo'][] = validation_errors();
			}

		} else {
			$mensaje['gradoPeriodo'][] = 'El grado que intenta agregar ya se encuentra asociado al periodo';
		}
	}

	/**
	* @param $periodoID int, id del periodo
	* muestra los datos de un periodo
	*/
	public function ver($periodoID){
		$periodo = $this->periodo->getData(array(
				'where' => array(array('periodo_id' => $periodoID))
			),true)->row();
		if ($periodo != NULL) {
			
			$dataGradoPeriodo = $this->grado_periodo->getData(array(
					'where' => array(array('grado_periodo.periodo_id' => $periodoID)),
					'select' => array(array('*','concat(persona.apellidos," ",persona.nombres) as profesor'))
				),TRUE)->result_array();

			$dataLapsos = $this->lapso->getData(array(
					'where' => array(array('periodo_id' => $periodoID)),
					'order_by' => array('numero')
				))->result_array();

			$this->load->view('header',array('title'=>'Ver periodo'));
			$this->load->view('periodo/ver',array(
					'periodo' => $periodo,
					'lapsos' => $dataLapsos,
					'gradosPeriodo' => $dataGradoPeriodo,
				)
			);
		} else {
			show_404();
		}
	}

	/**
	* @param $periodoID int id del periodo
	* activa un periodo y finalizado el periodo activo
	*/
	public function activar($periodoID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			if ($periodoID && $this->input->post('activar')) {
				$dataPeriodo = $this->periodo->getData(array(
						'where' => array(array('periodo_id' => $periodoID))
					))->row();
				$result['data'] = $dataPeriodo;
				if ($dataPeriodo != NULL) {
					if ($dataPeriodo->estado_periodo_id != '1') {
						if ($dataPeriodo->estado_periodo_id != '2') {
							if ($dataPeriodo->estado_periodo_id == '3') {
								
								$periodosActivos = $this->periodo->getData(array(
										'where' => array(array('estado_periodo_id' => '1'))
									))->result_array();
								$flag = true;
								$this->db->trans_begin();
								if (count($periodosActivos) > 0) {
									$this->db->set('estado_periodo_id',2);
									$this->db->where_in('periodo_id',array_column($periodosActivos, 'periodo_id'));
									if (!$this->db->update('periodo')) {
										$result['mensajeError'] = 'Ha ocurrido un error finalizando los periodos';
										$flag = false;
										$this->db->trans_rollback();
									}
								}
								if ($flag) {
									$periodo = new $this->periodo;
									$periodo->periodo_id = $dataPeriodo->periodo_id;
									$periodo->descripcion = $dataPeriodo->descripcion;
									$periodo->fecha_inicio = $dataPeriodo->fecha_inicio;
									$periodo->fecha_fin = $dataPeriodo->fecha_fin;
									$periodo->estado_periodo_id = 1; //activando
									if ($periodo->updateValues()) {
										$this->db->trans_commit();
										$result['mensaje'] = 'Periodo activado satisfactoriamente';
										$result['success'] = TRUE;
									} else {
										$this->db->trans_rollback();
										$result['mensajeError'] = 'Ha ocurrido un error al activar el periodo';
									}
								}
								$this->db->trans_complete();
							}else{
								$result['mensajeError'] = 'El periodo debe estar por activar';	
							}
						} else {
							$result['mensajeError'] = 'El periodo se encuentra finalizado';
						}
					} else {
						$result['mensajeError'] = 'El periodo ya se encuentra activo';
					}
				} else {
					$result['mensajeError'] = 'Periodo no encontrado';
				}
			} else {
				$result['mensajeError'] = 'Datos incompletos';
			}
		}
		echo json_encode($result);
	}
}