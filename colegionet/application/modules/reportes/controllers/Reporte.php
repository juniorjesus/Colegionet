<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte extends MX_Controller {
	
	private $rules = array(
		'methods' => array('reporteInscritos','reporteInscritosPDF','reportePersonas'),
		'filter' => array('reporteInscritos','reporteInscritosPDF','reportePersonas'),
		'navs' => array('reporteInscritos' => 'Inscritos','reportePersonas' => 'Personas'),
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
		$this->load->model('academico/periodo_model','periodo');
		//$this->load->model('rbac_item_model','rbac_item');
		//$this->load->model('rbac_rol_item_model','rbac_rol_item');
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
	*/
	public function reporteInscritos(){
		$this->load->view('header',array('title'=>'Inscritos'));
		$this->load->view('reporte/reporteInscritos');
	}


	/**
	* @param $periodoID int, id del periodo
	*/
	public function reporteInscritosPDF($periodoID){
		$periodo = new $this->periodo($periodoID);

		$query = "SELECT *,p.apellidos AS p_apellidos,p.nombres AS p_nombres,pe.* FROM inscripcion i
					JOIN estudiante e ON e.estudiante_id = i.estudiante_id
					JOIN persona pe ON pe.persona_id = e.persona_id
					JOIN grado_periodo gp ON gp.grado_periodo_id = i.grado_periodo_id
					JOIN grado g ON g.grado_id = gp.grado_id
					LEFT JOIN turno t ON t.turno_id = gp.turno_id
					LEFT JOIN profesor pr on pr.profesor_id = gp.profesor_id
					LEFT JOIN persona p on p.persona_id = pr.persona_id
				WHERE i.periodo_id = ?
				ORDER BY pe.apellidos,pe.nombres";
		$inscritos = $this->db->query($query,array($periodoID))->result();
		
		$datos = array();
		$datosInscritos = array();
		$total = count($inscritos);

		foreach ($inscritos as $key => $value) {
			if (!isset($datos[$value->grado_id]['cant'])) {
				$datos[$value->grado_id]['cant'] = 0;
			}
			$datos[$value->grado_id]['cant']++;
			$datos[$value->grado_id]['grado'] = $value->grado." [". $value->numero."][".$value->seccion."]";
			$datos[$value->grado_id]['profesor'] = trim($value->p_apellidos." ".$value->p_nombres);
			$datos[$value->grado_id]['turno'] = $value->turno;

			$datosInscritos[$value->grado_id]['grado'] = $value->grado;
			$datosInscritos[$value->grado_id]['turno'] = $value->turno;
			$datosInscritos[$value->grado_id]['numero'] = $value->numero;
			$datosInscritos[$value->grado_id]['seccion'] = $value->seccion;
			$datosInscritos[$value->grado_id]['profesor'] = trim($value->p_apellidos." ".$value->p_nombres);
			$datosInscritos[$value->grado_id]['inscritos'][]['nombres'] = $value->apellidos." ".$value->nombres;

		}

		$this->load->library('html2pdf');

		$pdf = new $this->html2pdf;
		$pdf->paper('A4','portrait');
		$pdf->folder(base_url());
		$pdf->html(utf8_decode($this->load->view('reporte/reporteInscritosPDF',array(
									'datos' => $datos,
									'datosInscritos' => $datosInscritos,
									'total' => $total,
									'periodo' => $periodo,
								),true
							)
						)
					);
					
		$pdf->create();
					/*$this->load->view('reporte/reporteInscritosPDF',array(
									'datos' => $datos,
									'datosInscritos' => $datosInscritos,
									'total' => $total,
									'periodo' => $periodo,
								));*/
	}

	public function reportePersonas(){
		$this->load->view('header',array('title'=>'Personas'));
		$this->load->view('reporte/reportePersonas');
	}
}