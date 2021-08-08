<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Noticia_model extends CI_Model {

	# defined field for this model
	private $table 	= "noticia";
	public $noticia_id;
	public $titulo;
	public $imagen;
	public $contenido;
	public $fecha_creacion;
	public $rbac_user_id;
	public $publicado = 1;

	public function __construct($noticiaID = NULL){
		parent::__construct();
		if ($noticiaID != NULL) {
			$data = $this->getData(array('where' => array(array('noticia_id' => $noticiaID))))->row();
			if ($data != null) {
				$this->noticia_id = $data->noticia_id;
				$this->titulo 	= $data->titulo;
				$this->imagen 	= $data->imagen;
				$this->contenido = $data->contenido;
				$this->fecha_creacion = $data->fecha_creacion;
				$this->rbac_user_id = $data->rbac_user_id;
				$this->publicado = $data->publicado;
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
			'noticia_id'	=> 'Noticia ID',
			'titulo'	=> 'Titulo',
			'iamgen'	=> 'Imagen',
			'contenido'	=> 'Contenido',
			'fecha_creacion' => 'Fecha Creacion',
			'rbac_user_id' => 'RBAC User ID',
			'publicado' => 'publicado'
		);
	}

	public function joins(){
		$this->db->join('rbac_user','rbac_user.rbac_user_id = noticia.rbac_user_id');
	}

	public function rules(){
		$configRules = array(
			array(
				'field'	=>	'noticia_id',
				'label'	=>	'Noticia Id',
				'rules'	=>	array('trim','numeric')
			),
			array(
				'field'	=>	'titulo',
				'label'	=>	'Titulo',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Záéíóú]+[A-Z0-9a-zñáéíóú,.:\s]+[A-Z0áéíóú-9a-zñ.]$/]',
								array('isunique',array($this,'isunique'))),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros'),
			),
			/*array(
				'field'	=>	'imagen',
				'label'	=>	'Imagen',
				'rules'	=>	array('trim','regex_match[/^[0-9a-zñA-Z]+[A-Z0-9a-zñ,.:\s]+[A-Z0-9a-zñ.]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros'),
			),*/
			array(
				'field'	=>	'contenido',
				'label'	=>	'Contenido',
				'rules'	=>	array('required','trim','regex_match[/^[0-9a-zñA-Záéíóú]+[A-Z0-9a-zñáéíóú,.:\s]+[A-Z0-9a-zñáéíóú.]$/]'),
				'errors'=>	array('regex_match'=>'El campo {field} solo puede contener letras y numeros'),
			),
			array(
				'field'	=>	'rbac_user_id',
				'label'	=>	'RBAC User ID',
				'rules'	=>	array('required','trim','numeric')
			),
			array(
				'field'	=>	'publicado',
				'label'	=>	'publicado',
				'rules'	=>	array('required','trim','numeric')
			)
		);
		$messageRules = array(
			'required'				=>	'El campo {field} es obligatorio',
			'min_length'			=>	'El campo {field} debe contener minimo {param} caracteres',
			'max_length'			=>	'El campo {field} debe contener maximo {param} caracteres',
			'alpha_numeric'			=>	'El campo {field} solo puede contener caracteres alfa numericos sin espacios',
			'alpha_numeric_spaces'	=>	'El campo {field} solo puede contener caracteres alfa numericos',
			'numeric'				=>	'El campo {field} solo puede contener numeros',
			'exact_length'			=>	'El campo {field} solo puede ser de {param} caracter',
		);
		$this->form_validation->set_rules($configRules);
		$this->form_validation->set_message($messageRules);
	}

	public function isunique($titulo){
		$data = $this->getData(array('where' => array(array('titulo' => $titulo))))->row();
		if ($data != null && $this->noticia_id != $data->noticia_id) {
			$this->form_validation->set_message('isunique','Ya existe una noticia con el mismo titulo');
			return FALSE;
		}
		return TRUE;
	}

	public function saveValues(){
		$this->db->set($this);
		if ($this->db->insert($this->table)) {
			$this->noticia_id = $this->db->insert_id();
			return $this->db->insert_id();		
		}else{
			log_message('error',__METHOD__." Ha ocurrido un error al insertar los datos");
			return false;
		}
	}

	public function updateValues(){
		if (isset($this->noticia_id)) {
			$this->db->set($this);
			$this->db->where('noticia_id',$this->noticia_id);
			if ($this->db->update($this->table)) {
				return true;
			} else {
				log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos");
				return false;
			}
		} else {
			log_message('error',__METHOD__." Ha ocurrido un error al actualizar los datos, grado_id nulo");
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