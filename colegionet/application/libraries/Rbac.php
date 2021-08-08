<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
* 
*/
class Rbac
{
	//group of rules
	private $rules;
	//access to check
	private $item;
	//rules to use by cases (logged or not)
	private $rulesToUse;

	public function __construct($rules = array())
	{
		$this->load->model('rbac/rbac_item_model','rbac_item');
		$this->rules = (object)$rules;
		$this->item = $this->_access_name();
		$this->createAccess();
		$this->_init();
	}

	public function __get($var)
	{
		return get_instance()->$var;
	}

	private function _init()
	{
		$isLogged = $this->session->userdata('login');
		switch ($isLogged) {
			case true:
				$this->rulesToUse = $this->rules->login;
				break;
			
			default:
				$this->rulesToUse = $this->rules->unlogged;
				break;
		}
		$this->checkRules();
	}

	private function checkRules()
	{
		$rules = $this->rulesToUse;
		$allowIn = TRUE;
		$method = $this->router->method == 'index' ? 'leer' : $this->router->method;
		$code = 403;
		$message = 'Acceso restringido';

		//si existe el metodo en las reglas
		if (in_array($method, $this->rules->methods)) {
			
			if (isset($rules['allow'])) {
				
				$allows = $rules['allow'];
				if (is_array($allows)) {
					if (!in_array($method, $allows)) {
						$allowIn = FALSE;
					}
				}elseif ($allows != '*') {
					$allowIn = FALSE;
				}

			}elseif (isset($rules['deny'])) {
				
				$denys = $rules['deny'];
				if ($denys == '*' ) {
					$allowIn = FALSE;
				}elseif (is_array($denys)) {
					if (in_array($method, $denys)) {
						$allowIn = FALSE;
					}
				}

			}

			if ($allowIn) {
				if ($this->input->is_ajax_request()) {
					
					if (isset($rules['ajax'])) {
						$ajaxs = $rules['ajax'];
						if (is_array($ajaxs)) {
							if (!in_array($method,$ajaxs)) {
								$allowIn = FALSE;
							}
						}elseif ($ajaxs != '*') {
							$allowIn = FALSE;
						}
					}

				}

				if ($this->input->get()) {
					
					if (isset($rules['GET'])) {
						$gets = $rules['GET'];
						if (is_array($gets)) {
							if (!in_array($method,$gets)) {
								$allowIn = FALSE;
							}
						}elseif ($gets != '*') {
							$allowIn = FALSE;
						}
					}

				}
				if ($this->input->post()) {
					
					if (isset($rules['POST'])) {
						$posts = $rules['POST'];
						if (is_array($posts)) {
							if (!in_array($method,$posts)) {
								$allowIn = FALSE;
							}
						}elseif ($posts != '*') {
							$allowIn = FALSE;
						}
					}

				}
			}

			if (!$allowIn) {
				//si existe regla de redireccionamiento y no es una peticion ajax
				if (isset($rules['redirect']) && !$this->input->is_ajax_request()) {
					$this->session->set_flashdata('error', $message);
					redirect($rules['redirect'],'refresh',$code);
				}else{
					show_error($message,$code,'Denied');
				}
			//si esta permitido entrar entonces chequeo que sea un metodo filtrador
			} else {
				if (in_array($method, $this->rules->filter)) {
					$this->checkAccess();
				}
			}
		} 
		
	}

	private function checkAccess()
	{
		if ($this->session->login) {
			$items = $this->session->items;
			$item = strtolower(implode('_', $this->item));
			if (!in_array($item, array_map('strtolower',array_column($items, 'item')))) {
				show_error('Missing item: '.$item,401,'401 Acceso denegado');
			}
		}
	}

	private function createAccess()
	{
		$methods = $this->rules->methods;
		$filter = $this->rules->filter;
		$navs	= $this->rules->navs;
		$controller_suffix = $this->config->item('controller_suffix');

		$module = $this->_getModule();
		$class  = str_replace($controller_suffix, '', $this->router->class);
		
		$this->db->trans_begin();
		foreach ($methods as $key => $value) {
			if (in_array($value, $filter)) {
				$data = array('where' => array(array('module' => $module,'class'  => $class,'method' => $value)));
				$datos = $this->rbac_item->getData($data);
				if (count($datos->result()) == 0) {
					$rbacItem = new $this->rbac_item;
					$rbacItem->module = $module;
					$rbacItem->class = $class;
					$rbacItem->method = $value;
					$rbacItem->navbar = is_array($navs) ? (array_key_exists($value, $navs) ? '1' : NULL) : $navs == '*' ? '1' : NULL;
					$rbacItem->navtext = is_array($navs) ? (array_key_exists($value, $navs) ? $navs[$value] : NULL) : NULL;
					if (!$rbacItem->saveValues()) {
						$this->db->trans_rollback();
					}
				}
			}
		}
		$this->db->trans_complete();
		
	}

	private function _getModule(){
		$module = '';
		if (isset($this->router->module)) {
			if (!empty($this->router->module)) {
				$module = $this->router->module;
			}
		} elseif (isset($this->router->directory)) {
			if (!empty($this->router->directory)) {
				$directory = substr($this->router->directory, 0, strlen($this->router->directory) - 1);
				$directory = implode('_', explode('/', $directory));
				$module = $directory;
			}
		}
		return $module;
	}

	private function _access_name()
	{
		$itemName = array();
		$controller_suffix = $this->config->item('controller_suffix');
		//$method_preffix = $this->method_preffix;
		$itemName['module'] = '';
		
		if (isset($this->router->module)) {
			if (!empty($this->router->module)) {
				$module = $this->router->module;
				$itemName['module'] = $module;
			}
		} elseif (isset($this->router->directory)) {
			if (!empty($this->router->directory)) {
				$directory = substr($this->router->directory, 0, strlen($this->router->directory) - 1);
				$directory = implode('__', explode('/', $directory));
				$itemName['module'] = $directory;
			}
		}
		
		$class  = str_replace($controller_suffix, '', $this->router->class);
		$method = $this->router->method == 'index' ? 'leer' : $this->router->method;
		
		$itemName['class'] = $class;
		$itemName['method'] = $method;

		return $itemName;
	}

}

?>