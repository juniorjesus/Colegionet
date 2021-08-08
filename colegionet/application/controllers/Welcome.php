<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct(){
		parent::__construct();
		$this->load->model('noticias/noticia_model','noticia');
	}

	public function index($page = '0')
	{
		$totalNoticias = $this->noticia->getData(array('where' => array(array('publicado' => 1))))->num_rows();
		$limit = 5;
		$config['base_url'] = site_url('welcome/index');
		$config['total_rows'] = $totalNoticias;
		$config['per_page'] = $limit;
		$config['num_links'] = 1;
		$config['enable_query_strings']=true;
		$config['reuse_query_string'] = true;
		$config['uri_segment'] = '3';

		$busqueda['where'][0]['publicado'] = 1;
		$busqueda['limit'] = array($limit,$page);
		$busqueda['order_by'] = array('fecha_creacion DESC');
		$noticias = $this->noticia->getData($busqueda)->result();
		
		$this->pagination->initialize($config);
		$pagNums = $this->pagination->create_links();

		$this->load->view('header',array('title'=>'Home'));
		$this->load->view('welcome',array('noticias' => $noticias,'pagNums' => $pagNums));
	}
}
