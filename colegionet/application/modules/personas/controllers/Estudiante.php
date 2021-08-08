<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estudiante extends MX_Controller {
	
	private $rules = array(
		'methods' => array('crear','leer','actualizar','borrar','ver','agregarEstudianteRepresentante',
							'borrarEstudianteRepresentante'),
		'filter' => array('crear','leer','actualizar','borrar','ver','agregarEstudianteRepresentante',
							'borrarEstudianteRepresentante'),
		'navs' => array('leer'=>'Estudiantes'),
		'login' => array(
			'allow' => '*',
			'ajax'	=> array('leer','borrar','agregarEstudianteRepresentante','actualizar','borrarEstudianteRepresentante'),
			'GET'	=> array('leer','actualizar'),
			'POST'	=> array('crear','actualizar','borrar','agregarEstudianteRepresentante','borrarEstudianteRepresentante')
		),
		'unlogged' => array(
			'deny' => '*',
			'redirect' => ''//base_url()
		)
	);

	public function __construct() {
		parent::__construct();
		$this->load->library('rbac',$this->rules);
		$this->load->model('estudiante_model','estudiante');
		$this->load->model('representante_model','representante');
		$this->load->model('persona_model','persona');
		$this->load->model('estudiante_representante_model','estudiante_representante');
		$this->load->model('parentesco_model','parentesco');
		$this->load->model('academico/periodo_model','periodo');
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
			$data = $this->input->get('estudiante');
			if ($data['matricula'] != '') {
				$busqueda['like'][0]['matricula'] = $data['matricula'];
			}
			if ($data['nombres'] != '') {
				$busqueda['like'][0]['nombres'] = $data['nombres'];
			}
			if ($data['apellidos'] != '') {
				$busqueda['like'][0]['apellidos'] = $data['apellidos'];
			}
		}
		$totalEstudiantes = $this->estudiante->getData($busqueda,true)->num_rows();
		$limit = 15;
		$config['base_url'] = site_url('personas/estudiante/leer');
		$config['total_rows'] = $totalEstudiantes;
		$config['per_page'] = $limit;
		$config['num_links'] = 3;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '4';

		$busqueda['limit'] = array($limit,$page);
		$estudiantes = $this->estudiante->getData($busqueda,true)->result_array();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();
		
		$this->load->view('header',array('title'=>'Estudiantes'));
		$this->load->view('estudiante/leer',array(
				'estudiantes' => $estudiantes,
				'pagNums'	=> $pagNums,
			)
		);
	}

	/**
	* @param none
	* crea un estudiante
	*/
	public function crear(){
		$mensaje = array();
		if ($this->input->post()) {
			$persona = new $this->persona;
			$data = $this->input->post('persona');
			$this->form_validation->set_data($data);
			$persona->rules('crear');
			if ($this->form_validation->run() == TRUE) {
				$this->db->trans_begin();
				$persona->identificacion = $data['identificacion'] != '' ? $data['identificacion'] : null;
				$persona->nombres = $data['nombres'];
				$persona->apellidos = $data['apellidos'];
				$persona->sexo = $data['sexo'];
				$persona->fecha_nac = $data['fecha_nac'];
				$persona->telefono_hab = $data['telefono_hab'];
				$persona->telefono_mov = $data['telefono_mov'];
				if ($persona->saveValues()) {
					$estudiante = new $this->estudiante;
					$estudiante->matricula = $this->crearMatricula($persona->persona_id);
					$estudiante->persona_id = $persona->persona_id;
					if ($estudiante->saveValues()) {
						$this->db->trans_commit();
						$this->session->set_flashdata('success','Estudiante creado satisfactoriamente');
						redirect('personas/estudiante/actualizar/'.$estudiante->estudiante_id,'refresh');
					} else {
						$this->db->trans_rollback();
						$mensaje[] = "Ha ocurrido un error al crear al estudiante";
					}
				} else {
					$this->db->trans_rollback();
					$mensaje[] = "Ha ocurrido un error al crear la persona";
				}
				$this->db->trans_complete();		
			}
		}
		$this->load->view('header',array('title'=>'Registrar estudiante'));
		$this->load->view('estudiante/crear',array('mensaje' => $mensaje));
	}

	/**
	* @param personaID id de la persona estudiante
	* crea una matricula
	*/
	private function crearMatricula($personaID){
		$periodo = $this->periodo->getData(array(
			'limit' => array(1),
			'order_by' => array('periodo_id DESC')
		))->row();

		return $periodo->anio_inicio.$personaID;
	}

	/**
	* @param $estudianteID int, id del estudiante
	* actualiza los datos personales de un estudiante
	*/
	public function actualizar($estudianteID){
		$mensaje = array();
		$estudiante = $this->estudiante->getData(array(
				'where' => array(array('estudiante_id' => $estudianteID))
			),true)->row();
		if ($estudiante != NULL) {
			if ($this->input->post()) {
				$persona = new $this->persona();
				$data = $this->input->post('persona');
				$this->form_validation->set_data($data);
				$persona->rules('actualizar');
				if ($this->form_validation->run() == TRUE) {
					$persona->persona_id = $estudiante->persona_id;
					$persona->identificacion = isset($data['identificacion']) && $data['identificacion'] != '' 
													? $data['identificacion'] : $estudiante->identificacion;
					$persona->nombres = $data['nombres'];
					$persona->apellidos = $data['apellidos'];
					$persona->sexo = $data['sexo'];
					$persona->fecha_nac = $data['fecha_nac'];
					$persona->telefono_hab = $data['telefono_hab'];
					$persona->telefono_mov = $data['telefono_mov'];
					if ($persona->updateValues()) {
						$this->session->set_flashdata('success','Estudiante actualizado satisfactoriamente');
						redirect('personas/estudiante/actualizar/'.$estudiante->estudiante_id,'refresh');
					}else{
						$mensaje[] = "Ha ocurrido un error al actualizar al estudiante";
					}
				}
			}

			$estudianteRepresentante = $this->estudiante_representante->getData(array(
					'where' => array(array('estudiante_representante.estudiante_id' => $estudianteID)),
					'select' => array(array('p2.*','parentesco.*','estudiante_representante.*'))
				),TRUE)->result();

			$this->load->view('header',array('title'=>'actualizar estudiante'));
			$this->load->view('estudiante/actualizar',array(
					'estudiante' => $estudiante,
					'mensaje' => $mensaje,
					'estudianteRepresentante' => $estudianteRepresentante
				)
			);
		}else {
			show_404();
		}
	}

	/**
	* @param $estudianteID int, id del estudiante
	* muestra los datos de un estudiante
	*/
	public function ver($estudianteID){
		$estudiante = $this->estudiante->getData(array(
				'where' => array(array('estudiante_id' => $estudianteID))
			),true)->row();
		if ($estudiante != NULL) {

			$estudianteRepresentante = $this->estudiante_representante->getData(array(
					'where' => array(array('estudiante_representante.estudiante_id' => $estudianteID)),
					'select' => array(array('p2.*','parentesco.*','estudiante_representante.*'))
				),TRUE)->result();

			$this->load->view('header',array('title'=>'Ver estudiante'));
			$this->load->view('estudiante/ver',array(
					'estudiante' => $estudiante,
					'estudianteRepresentante' => $estudianteRepresentante
				)
			);
		} else {
			show_404();
		}
	}

	/**
	* @param $estudianteID int, id del estudiante
	* agrega un representante al estudiante
	* el representante debe haber sido registrado previamente
	*/
	public function agregarEstudianteRepresentante($estudianteID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$datos = $this->input->post('persona');

			$estudiante = $this->estudiante->getData(array(
				'where' => array(array('estudiante_id' => $estudianteID))
			),TRUE)->row();


			$representante = $this->representante->getData(array(
				'where' => array(array('identificacion' => $datos['identificacion']))
			),TRUE)->row();

			if ($representante != NULL) {
				$dataEstudianteRepresentante = $this->estudiante_representante->getData(array(
					'where' => array(array('estudiante_id' => $estudianteID,'representante_id' =>$representante->representante_id))
				))->row();
				
				if ($dataEstudianteRepresentante == NULL) {

					$dataEstudianteRepresentanteParentesco = $this->estudiante_representante->getData(array(
						'where' => array(array('estudiante_id' => $estudianteID,'parentesco_id' => $datos['parentesco_id']))
					))->row();

					if ($dataEstudianteRepresentanteParentesco == NULL) {

						if ($representante->identificacion != $estudiante->identificacion) {
							$estudianteRepresentante = new $this->estudiante_representante;
							$estudianteRepresentante->estudiante_id = $estudianteID;
							$estudianteRepresentante->representante_id = $representante->representante_id;
							$estudianteRepresentante->parentesco_id = $datos['parentesco_id'];
							if ($estudianteRepresentante->saveValues()) {
								$result['success'] = TRUE;
								$result['mensaje'] = 'Agregado satisfactoriamente';
							} else {
								$result['mensajeError'] = 'Ha ocurrido un error al agregar el representante al estudiante';
							}
						} else {
							$result['mensajeError'] = 'El representante no puede ser el mismo estudiante';
						}
					} else {
						$result['mensajeError'] = 'El estudiante ya tiene un representante asociado con el mismo parentesco';
					}
				} else {
					$result['mensajeError'] = 'El representante ya se encuentra asociado al estudiante';
				}
			} else {
				$result['mensajeError'] = 'No existe representante con los datos proporcionados';
			}


		}
		echo json_encode($result);
	}

	/**
	* @param $estudianteRepresentanteID int, id del estudiante_repreentante asociado
	* elimina un representante al estudiante
	*/
	public function borrarEstudianteRepresentante($estudianteRepresentanteID){
		$result['success'] = FALSE;
		if ($this->input->is_ajax_request() && $this->input->post()) {
			$dataEstudianteRepresentante = $this->estudiante_representante->getData(array(
					'where' => array(array('estudiante_representante_id' => $estudianteRepresentanteID))
				))->row();
			if ($dataEstudianteRepresentante != NULL) {
				$estudianteRepresentante = new $this->estudiante_representante;
				$estudianteRepresentante->estudiante_representante_id = $estudianteRepresentanteID;
				if ($estudianteRepresentante->deleteValues()) {
					$result['success'] = TRUE;
					$result['mensaje'] = 'Eliminado satisfactoriamente';
				} else {
					$result['mensajeError'] = 'Ha ocurrido un error al eliminar el representante del estudiante';
				}
			} else {
				$result['mensajeError'] = 'La asociacion del representante al estudiante no se encuentra registrada';
			}
		}
		echo json_encode($result);
	}
}