<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotasProfesor extends MX_Controller {
	
	private $rules = array(
		'methods' => array(
			'leer','lapsos','crearProyecto','crearEvaluacion','cargarEvaluacion','editarDetalleEvaluacion',
			'obtenerIndicadoresNoUsados','actualizarEvaluacion','agregarDetallesEvaluacion','borrarDetalleEvaluacion',
			'obtenerBoletinPDF'
		),
		'filter' => array(
			'leer','lapsos','crearProyecto','crearEvaluacion','cargarEvaluacion','editarDetalleEvaluacion',
			'obtenerIndicadoresNoUsados','actualizarEvaluacion','agregarDetallesEvaluacion','borrarDetalleEvaluacion',
			'obtenerBoletinPDF'
		),
		'navs' => array('leer' => 'Grados asig.'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('lapsos','crearProyecto','cargarEvaluacion','editarDetalleEvaluacion','obtenerIndicadoresNoUsados',
						'agregarDetallesEvaluacion','actualizarEvaluacion','borrarDetalleEvaluacion'),
			'GET'	=> array('lapsos','cargarEvaluacion'),
			'POST'	=> array('crearProyecto','editarDetalleEvaluacion','obtenerIndicadoresNoUsados','agregarDetallesEvaluacion',
						'actualizarEvaluacion','borrarDetalleEvaluacion'),
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
		$this->load->model('academico/indicador_model','indicador');
		$this->load->model('academico/lapso_model','lapso');
		
		$this->load->model('proyecto_model','proyecto');
		$this->load->model('evaluacion_model','evaluacion');
		$this->load->model('detalle_evaluacion_model','detalle_evaluacion');
		
		$this->load->model('inscripciones/inscripcion_model','inscripcion');

		$this->load->model('personas/estudiante_representante_model','estudiante_representante');
		$this->load->model('personas/estudiante_model','estudiante');
		
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
	* @param none
	* funcion leer = index
	*/
	public function leer(){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$asignacionProfesor = $this->grado_periodo->getData(array(
				'where' => array(array('grado_periodo.profesor_id' => $profesor->profesor_id,'grado_periodo.periodo_id' => $periodoActual->periodo_id)),
				'join'	=> array('inscripcion','inscripcion.grado_periodo_id = grado_periodo.grado_periodo_id','left'),
				'select' => array(array('grado_periodo.grado_periodo_id','descripcion','grado','turno','count(inscripcion_id) as inscritos')),
				'group_by' => array('grado.grado_id')
			),TRUE)->result_array();

		$this->load->view('header',array('title'=>'Grados asignados'));
		$this->load->view('notasprofesor/leer',array(
				'asignacionProfesor' => $asignacionProfesor,
			)
		);
	}

	public function lapsos($gradoPeriodoID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}		
		
		$datosGrado = $this->grado_periodo->getData(array(
				'where' => array(array('grado_periodo_id' => $gradoPeriodoID))
			),TRUE)->row();

		$datosLapsoGrado = $this->grado_periodo->getData(array(
				'where' => array(array('profesor_id' => $profesor->profesor_id, 'grado_periodo_id' => $gradoPeriodoID)),
				'join'	=> array('lapso','lapso.periodo_id = grado_periodo.periodo_id'),
				'select' => array('*,CASE WHEN lapso.lapso_fecha_inicio <= "'.date('Y-m-d').'" AND lapso.lapso_fecha_fin >= "'.date('Y-m-d').'" THEN 1 ELSE NULL END AS activo,
					CASE WHEN lapso.lapso_fecha_fin <= "'.date('Y-m-d').'" THEN 1 ELSE NULL END AS ver',FALSE),
				'order_by' => array('lapso.numero'),
			))->result();
		
		$datos = array();
		if (count($datosLapsoGrado) > 0) {
			foreach ($datosLapsoGrado as $key => $value) {
				$datosProyecto = $this->proyecto->getData(array(
						'where' => array(array('lapso_id' => $value->lapso_id, 'grado_periodo_id' => $gradoPeriodoID))
					))->row();

				$queryInscritos = "SELECT i.inscripcion_id,evaluacion_id,matricula,identificacion,apellidos,nombres
							FROM inscripcion i
								JOIN estudiante est ON est.estudiante_id = i.estudiante_id
								JOIN persona per ON per.persona_id = est.persona_id
								LEFT JOIN proyecto p ON p.lapso_id = ? and p.grado_periodo_id = ?
								LEFT JOIN evaluacion e ON e.inscripcion_id = i.inscripcion_id AND e.proyecto_id = p.proyecto_id
						WHERE i.grado_periodo_id = ?";
				
				$estudianteEvaluaciones = $this->db->query($queryInscritos,array($value->lapso_id,$gradoPeriodoID,$gradoPeriodoID))->result_array();

				$datos[] = array(
					'gradoPeriodoID' => $gradoPeriodoID,
					'nombre' => 'Lapso '.$value->numero,
					'lapso' => $value,
					'activo' => $value->activo,
					'lapsoID' => $value->lapso_id,
					'estudiantes' => $estudianteEvaluaciones,
					'proyecto'	=> $datosProyecto
				);
			}
		}
		$this->load->view('header',array('title'=>'Lapsos'));
		$this->load->view('notasprofesor/lapsos',array(
				'datos' => $datos,
				'grado'	=> $datosGrado
			)
		);
	}

	public function crearProyecto($gradoPeriodoID,$lapsoID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$gradoDelProfesor = $this->grado_periodo->getdata(array(
					'where' => array(array('grado_periodo_id' => $gradoPeriodoID, 'profesor_id' => $profesor->profesor_id))
				))->row();
			if ($gradoDelProfesor != NULL) {
				$proyectoExiste = $this->proyecto->getdata(array(
						'where' => array(array('grado_periodo_id' => $gradoPeriodoID, 'lapso_id' => $lapsoID))
					))->row();
				if ($proyectoExiste == NULL) {
					$proyecto = new $this->proyecto;
					$data = $this->input->post('proyecto');
					$data['lapso_id'] = $lapsoID;
					$data['grado_periodo_id'] = $gradoPeriodoID;
					$this->form_validation->set_data($data);
					$proyecto->rules();
					if ($this->form_validation->run() == TRUE) {
						$proyecto->proyecto = $data['proyecto'];
						$proyecto->lapso_id = $lapsoID;
						$proyecto->grado_periodo_id = $gradoPeriodoID;
						if ($proyecto->saveValues()) {
							$result['success'] = TRUE;
							$result['mensaje'] = 'Proyecto creado satisfactoriamente';
						} else {
							$result['mensajeError'] = 'Ha ocurrido un error al crear el proyecto';
						}
					} else {
						$result['mensajeError'] = validation_errors();
					}
				} else {
					$result['mensajeError'] = 'El lapso ya tiene un proyecto creado';
				}
			} else {
				$result['mensajeError'] = 'Usted no es el profesor asignado al grado';
			}
		}
		echo json_encode($result);
	}

	public function crearEvaluacion($inscripcionID,$proyectoID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$evaluacionExiste = $this->evaluacion->getdata(array(
				'where' => array(array('inscripcion_id' => $inscripcionID,'proyecto_id' => $proyectoID))
			))->row();
		$proyectoAsosiado = $this->proyecto->getdata(array(
				'where' => array(array('proyecto_id' => $proyectoID))
			),TRUE)->row();

		if ($evaluacionExiste == NULL) {
			
			if ($proyectoAsosiado != NULL && $proyectoAsosiado->profesor_id != $profesor->profesor_id) {
				show_error('Usted no es el profesor asignado',401,'Area restringida');
				exit;
			}

			$evaluacion = new $this->evaluacion;
			$evaluacion->inscripcion_id = $inscripcionID;
			$evaluacion->proyecto_id = $proyectoID;
			if ($evaluacion->saveValues()) {
				$this->session->set_flashdata('success','Evaluacion creada satisfactoriamente');
				redirect('notas/notasprofesor/cargarEvaluacion/'.$evaluacion->evaluacion_id);
			} else {
				if ($proyectoAsosiado != NULL) {
					$this->session->set_flashdata('danger','Ha ocurrido un error creando la evaluacion');
					redirect('notas/notasprofesor/lapsos/'.$proyectoAsosiado->grado_periodo_id);
				}
			}
		} else {
			redirect('notas/notasprofesor/cargarEvaluacion/'.$evaluacionExiste->evaluacion_id);
		}
	}

	public function actualizarEvaluacion($evaluacionID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			
			$evaluacion = new $this->evaluacion($evaluacionID);
			if ($evaluacion->evaluacion_id != NULL) {
				$query = "SELECT *
							FROM evaluacion e
								JOIN inscripcion i ON i.inscripcion_id = e.inscripcion_id
								JOIN grado_periodo gp ON gp.grado_periodo_id = i.grado_periodo_id
						WHERE e.evaluacion_id =  ? AND gp.profesor_id = ?";
				$esDelProfesor = $this->db->query($query,array($evaluacionID,$profesor->profesor_id))->row();

				if ($esDelProfesor != NULL) {
					$data = $this->input->post('evaluacion');
					$evaluacion->representante_id = isset($data['representante_id']) ? 
						($data['representante_id'] != '' ? $data['representante_id'] : NULL ) : $evaluacion->representante_id;
					$evaluacion->observaciones = isset($data['observaciones']) ? $data['observaciones'] : $evaluacion->observaciones;
					$evaluacion->inasistencias = isset($data['inasistencias']) ? $data['inasistencias'] : $evaluacion->inasistencias;

					$this->form_validation->set_data((array)$evaluacion);
					$evaluacion->rules();
					if ($this->form_validation->run() == TRUE) {
						if ($evaluacion->updateValues()) {
							$result['success'] = TRUE;
							$result['mensaje'] = 'Actualizado satisfactoriamente';
						}else{
							$result['mensajeError'] = 'Ha ocurrido un error al actualizar la informacion';
						}
					} else {
						$result['mensajeError'] = validation_errors();
					}
				} else {
					$result['mensajeError'] = 'Usted no es el profesor asignado al grado';
				}
				
			}

		}
		echo json_encode($result);
	}

	public function cargarEvaluacion($evaluacionID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$query = "SELECT est.estudiante_id,l.numero as lapso,p.proyecto,l.lapso_fecha_inicio,l.lapso_fecha_fin,
						per.nombres AS est_nombres,per.apellidos AS est_apellidos,est.matricula,per.identificacion AS est_identificacion,
						per2.nombres AS re_nombres,per2.apellidos AS re_apellidos,per2.identificacion AS re_identificacion,
						per3.nombres AS pro_nombres,per3.apellidos AS pro_apellidos,g.numero,g.seccion,anio_inicio,anio_fin,
						e.*,gp.grado_periodo_id
					FROM evaluacion e
						JOIN inscripcion i ON i.inscripcion_id = e.inscripcion_id
						JOIN periodo peri ON peri.periodo_id = i.periodo_id
						JOIN estudiante est ON est.estudiante_id = i.estudiante_id
						JOIN persona per ON per.persona_id = est.persona_id
						LEFT JOIN representante r ON r.representante_id = e.representante_id
						LEFT JOIN persona per2 ON per2.persona_id = r.persona_id
						JOIN proyecto p ON p.proyecto_id = e.proyecto_id
						JOIN lapso l on l.lapso_id = p.lapso_id
						JOIN grado_periodo gp ON gp.grado_periodo_id = p.grado_periodo_id
						JOIN profesor pro ON pro.profesor_id = gp.profesor_id
						JOIN persona per3 ON per3.persona_id = pro.profesor_id
						JOIN grado g ON g.grado_id = gp.grado_id
					WHERE evaluacion_id = ? and gp.profesor_id = ?";

		$todaInfo = $this->db->query($query,array($evaluacionID,$profesor->profesor_id))->row();
		
		if ($todaInfo != NULL) {
			$habilitado = FALSE;
			if (strtotime(date('Y-m-d')) > strtotime($todaInfo->lapso_fecha_inicio) &&
				strtotime(date('Y-m-d')) < strtotime($todaInfo->lapso_fecha_fin)) {
				$habilitado = TRUE;
			}

			$detallesEvaluacion = $this->detalle_evaluacion->getData(array(
					'where' => array(array('detalle_evaluacion.evaluacion_id' => $evaluacionID))
				),TRUE)->result_array();

			$this->load->view('header',array('title'=>'Carga Nota'));
			$this->load->view('notasprofesor/cargarEvaluacion',array(
					'info' => $todaInfo,
					'evaluacionID' => $evaluacionID,
					'detallesEvaluacion' => $detallesEvaluacion,
					'habilitado' => $habilitado
				)
			);

		} else {
			show_error('Usted no es el profesor asignado',401,'Area restringida');
		}
	}

	public function editarDetalleEvaluacion($detalleEvaluacionID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$result['success'] = FALSE;
		
		$detalleEvaluacion = new $this->detalle_evaluacion($detalleEvaluacionID);

		if ($this->input->is_ajax_request() && $this->input->post()) {
			if ($detalleEvaluacion->detalle_evaluacion_id != NULL) {
				$query = "SELECT *
							FROM detalle_evaluacion de
								JOIN evaluacion e ON e.evaluacion_id = de.evaluacion_id
								JOIN inscripcion i ON i.inscripcion_id = e.inscripcion_id
								JOIN grado_periodo gp ON gp.grado_periodo_id = i.grado_periodo_id
							WHERE detalle_evaluacion_id =  ? AND gp.profesor_id = ?";
				$esDelProfesor = $this->db->query($query,array($detalleEvaluacionID,$profesor->profesor_id))->row();

				if ($esDelProfesor != NULL) {
					$datos = $this->input->post('indicador');
					$datos['indicador_id'] = $detalleEvaluacion->indicador_id;
					$datos['evaluacion_id'] = $detalleEvaluacion->evaluacion_id;
					$this->form_validation->set_data($datos);
					$detalleEvaluacion->rules();
					if ($this->form_validation->run() == TRUE) {
						$detalleEvaluacion->calificacion = $datos['calificacion'];
						if ($detalleEvaluacion->updateValues()) {
							$result['success'] = TRUE;
							$result['mensaje'] = 'Actualizado satisfactoriamente';
						} else {
							$result['mensajeError'] = 'Ha ocurrido un error al actualizar el la calificacion';
						}
					}else{
						$result['mensajeError'] = validation_errors();
					}

				} else {
					$result['mensajeError'] = 'Usted no es el profesor asignado al grado';
				}

			} else {
				$result['mensajeError'] = 'El detalle de evaluacion no existe';
			}
			
		}

		echo json_encode($result);
	}

	public function obtenerIndicadoresNoUsados($evaluacionID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			
			$detallesEvaluacion = $this->detalle_evaluacion->getData(array(
					'where' => array(array('detalle_evaluacion.evaluacion_id' => $evaluacionID))
				),TRUE)->result_array();

			$arr = array(-1); //-1 para evitar error el query IN
			if (count($detallesEvaluacion) > 0) {
				$arr = array_column($detallesEvaluacion, 'indicador_id');
			}
			$indicadoresNoUsados = $this->indicador->getdata(array(
					'where_not_in' => array('indicador_id',$arr)
				))->result_array();

			$result['success'] = TRUE;
			$result['table'] = $this->load->view('notasprofesor/_indicadoresNoUsados',array(
					'indicadores' => $indicadoresNoUsados,
					'evaluacionID' => $evaluacionID
				),TRUE
			);
		}
		echo json_encode($result);
	}

	public function agregarDetallesEvaluacion($evaluacionID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$query = "SELECT *
						FROM evaluacion e
							JOIN inscripcion i ON i.inscripcion_id = e.inscripcion_id
							JOIN grado_periodo gp ON gp.grado_periodo_id = i.grado_periodo_id
					WHERE e.evaluacion_id =  ? AND gp.profesor_id = ?";
			$esDelProfesor = $this->db->query($query,array($evaluacionID,$profesor->profesor_id))->row();

			if ($esDelProfesor != NULL) {
				$datos = $this->input->post('detalleEvaluacion');
				$this->db->trans_begin();
				$flag = TRUE;
				foreach ($datos as $indicadorID => $value) { //key as indicadorID
					$detalleEvaluacionCheck = $this->detalle_evaluacion->getData(array(
							'where' => array(array('evaluacion_id' => $evaluacionID,'indicador_id' => $indicadorID))
						))->row();
					if (isset($value['indicador_id'])) {
						if ($detalleEvaluacionCheck == NULL) {
							$detalleEvaluacion = new $this->detalle_evaluacion;
							$detalleEvaluacion->evaluacion_id = $evaluacionID;
							$detalleEvaluacion->indicador_id = $indicadorID;
							$detalleEvaluacion->calificacion = $value['calificacion'];

							$this->form_validation->set_data((array)$detalleEvaluacion);
							$detalleEvaluacion->rules();
							if ($this->form_validation->run() == TRUE) {
								if ($detalleEvaluacion->saveValues()) {
									$flag = TRUE;
								} else {
									$flag = FALSE;
									$result['mensajeError'] = 'Ha ocurrido un error cargando los indicadores';
									$this->db->trans_rollback();
									break;
								}
							}else{
								$flag = FALSE;
								$result['mensajeError'] = validation_errors();
								$this->db->trans_rollback();
								break;
							}
						}
					}
				}
				if ($flag) {
					$result['success'] = TRUE;
					$result['mensaje'] = 'Indicadores cargados satisfactoriamente';
					$this->db->trans_commit();
				}
				$this->db->trans_complete();
			} else {
				$result['mensajeError'] = 'Usted no es el profesor asignado al grado';
			}
		} else {
			$result['mensajeError'] ='No se han suministrador los datos correctamente';
		}
		

		echo json_encode($result);
	}

	public function borrarDetalleEvaluacion($detalleEvaluacionID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$result['success'] = FALSE;
		
		$detalleEvaluacion = new $this->detalle_evaluacion($detalleEvaluacionID);
		if ($this->input->is_ajax_request() && $this->input->post()) {
			if ($detalleEvaluacion->detalle_evaluacion_id != NULL) {
				$query = "SELECT *
							FROM detalle_evaluacion de
								JOIN evaluacion e ON e.evaluacion_id = de.evaluacion_id
								JOIN inscripcion i ON i.inscripcion_id = e.inscripcion_id
								JOIN grado_periodo gp ON gp.grado_periodo_id = i.grado_periodo_id
							WHERE detalle_evaluacion_id =  ? AND gp.profesor_id = ?";
				$esDelProfesor = $this->db->query($query,array($detalleEvaluacionID,$profesor->profesor_id))->row();

				if ($esDelProfesor != NULL) {
					if ($detalleEvaluacion->deleteValues()) {
						$result['success'] = TRUE;
						$result['mensaje'] = 'Eliminado satisfactoriamente';
					} else {
						$result['mensajeError'] = 'Ha ocurrido un error al eliminar el detalle de evaluacion';
					}
				} else {
					$result['mensajeError'] = 'Usted no es el profesor asignado al grado';
				}
			} else {
				$result['mensajeError'] = 'El detalle de evaluacion no existe';
			}
		}
		echo json_encode($result);
	}

	public function obtenerBoletinPDF($evaluacionID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$this->load->library('html2pdf');

		$pdf = new $this->html2pdf;
		$pdf->paper('A4','portrait');
		$pdf->folder(base_url());

		

		$datosEvaluacion = $this->evaluacion->getData(array(
				'where' => array(array('evaluacion.evaluacion_id' => $evaluacionID))
			),TRUE)->row();
		
		if ($datosEvaluacion != NULL) {
			
			$query = "SELECT *
					FROM evaluacion e
						JOIN inscripcion i ON i.inscripcion_id = e.inscripcion_id
						JOIN grado_periodo gp ON gp.grado_periodo_id = i.grado_periodo_id
				WHERE e.evaluacion_id =  ? AND gp.profesor_id = ?";
			$esDelProfesor = $this->db->query($query,array($evaluacionID,$profesor->profesor_id))->row();

			if ($esDelProfesor != NULL) {
				$detallesEvaluacion = $this->detalle_evaluacion->getdata(array(
						'where' => array(array('detalle_evaluacion.evaluacion_id' => $evaluacionID)),
						'select' => array(array('indicador','calificacion'))
					),TRUE)->result_array();

				$datosGrado = $this->grado_periodo->getData(array(
						'where' => array(array('grado_periodo_id' => $datosEvaluacion->grado_periodo_id))
					),TRUE)->row();

				$datosLapso = $this->lapso->getData(array(
						'where' => array(array('lapso_id' => $datosEvaluacion->lapso_id))
					))->row();

				$datosPeriodo = $this->periodo->getData(array(
						'where' => array(array('periodo_id' => $datosEvaluacion->periodo_id))
					))->row();

				$datosEstudiante = $this->estudiante->getData(array(
						'where' => array(array('estudiante_id' => $datosEvaluacion->estudiante_id))
					),TRUE)->row();

				$pdf->html(utf8_decode($this->load->view('notasprofesor/obtenerBoletinPDF',array(
									'evaluacion' => $datosEvaluacion,
									'estudiante' => $datosEstudiante,
									'grado' => $datosGrado,
									'lapso' => $datosLapso,
									'periodo' => $datosPeriodo,
									'detallesEvaluacion' => $detallesEvaluacion
								),true
							)
						)
					);
				$pdf->create();

			} else {
				show_error('Usted no es el profesor asignado',401,'Area restringida');
			}
			
		} else {
			show_404();
		}
	}

	public function obtenerListaPDF($gradoPeriodoID){
		$profesor = $this->session->profesor;
		$periodoActual = $this->periodo->getdata(array(
				'where' => array(array('estado_periodo_id' => 1))// estado activo
			))->row();
		if ($profesor == NULL) {
			show_error('Solo profesores',401,'Area restringida');
		}elseif ($periodoActual == NULL) {
			show_error('No se encontro un periodo activo, contacte al administrador',401,'Sin periodo activo');
		}

		$gradoDelProfesor = $this->grado_periodo->getdata(array(
				'where' => array(array('grado_periodo_id' => $gradoPeriodoID, 'profesor_id' => $profesor->profesor_id))
			))->row();

		if ($gradoDelProfesor != NULL) {
			$inscripciones = $this->inscripcion->getData(array(
					'where' => array(array('inscripcion.grado_periodo_id' => $gradoPeriodoID)),
					'select' => array(array('apellidos','nombres')),
					'order_by' => array('apellidos ASC,nombres ASC')
				),TRUE)->result_array();

			$datosGrado = $this->grado_periodo->getData(array(
					'where' => array(array('grado_periodo_id' => $gradoPeriodoID))
				),TRUE)->row();
			
			$datosPeriodo = $this->periodo->getData(array(
					'where' => array(array('periodo_id' => $gradoDelProfesor->periodo_id))
				))->row();

			$this->load->library('html2pdf');
			
			$pdf = new $this->html2pdf;
			$pdf->paper('A4','portrait');
			$pdf->folder(base_url());

			$pdf->html(utf8_decode($this->load->view('notasprofesor/obtenerListaPDF',array(
							'periodo' => $datosPeriodo,
							'grado' => $datosGrado,
							'inscripciones' => $inscripciones
						),TRUE
					)
				)
			);
			$pdf->create();
			
		} else {
			show_error('Usted no es el profesor asignado',401,'Area restringida');
		}
	}

}