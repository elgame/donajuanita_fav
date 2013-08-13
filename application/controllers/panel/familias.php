<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class familias extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('');
	
	public function _remap($method){
		
		$this->load->model("usuarios_model");
		if($this->usuarios_model->checkSession()){
			$this->usuarios_model->excepcion_privilegio = $this->excepcion_privilegio;
			$this->info_empleado                         = $this->usuarios_model->get_usuario_info($this->session->userdata('id_usuario'), true);

			if($this->usuarios_model->tienePrivilegioDe('', get_class($this).'/'.$method.'/')){
				$this->{$method}();
			}else
				redirect(base_url('panel/home?msg=1'));
		}else
			redirect(base_url('panel/home'));
	}
	
	/**
	 * Default. Mustra el listado de familias para administrarlos
	 */
	public function index(){
		$this->carabiner->js(array(
				array('general/msgbox.js'),
				array('general/supermodal.js'),
		));
		
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Administrar Familias'
		);
		
		$this->load->model('familias_model');
		$params['familias'] = $this->familias_model->obtenFamilias();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/familias/listado', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Agrega un familias a la bd
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
			array('libs/jquery.treeview.css', 'screen'),
			array('libs/jquery.colorpicker.css', 'screen'),
			array('panel/familias_productos.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.treeview.js'),
			array('libs/jquery.colorpicker.js'),
			array('libs/jquery.numeric.js'),
			array('panel/familias/frm_addmod.js'),
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar familia'
		);

		$this->load->model('familias_model');
		$this->load->model('productos_model');
		$this->load->library('pagination');
		
		$this->configAddModFamilia();
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$respons = $this->familias_model->addFamilia();
			
			if($respons[0])
				redirect(base_url('panel/familias/agregar/?'.String::getVarsLink(array('msg')).'&msg=4'));
			else
        $params['frm_errors'] = $this->showMsgs(2, $respons[1]);
		}

		$params['productosr'] = $this->productos_model->obtenProductosBase('20'); //productos registrados
		//Listra de productos agregados
		$params['tabla_productos_r'] = $this->load->view('panel/familias/agregar_produc_listado', $params, true);
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/familias/agregar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Modificar familia
	 */
	public function modificar(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
			array('libs/jquery.treeview.css', 'screen'),
			array('libs/jquery.colorpicker.css', 'screen'),
			array('panel/familias_productos.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.treeview.js'),
			array('libs/jquery.colorpicker.js'),
			array('libs/jquery.numeric.js'),
			array('panel/familias/frm_addmod.js'),
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Modificar familia'
		);
		
		if(isset($_GET['id']{0})){
			$this->configAddModFamilia('update');

			$this->load->model('familias_model');
			$this->load->model('productos_model');
			$this->load->library('pagination');
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->familias_model->updateFamilia($_GET['id']);
				
				if($respons[0])
					redirect(base_url('panel/familias/?'.String::getVarsLink(array('msg', 'id')).'&msg=3'));
			}
			
			$params['familia'] = $this->familias_model->getInfoFamilia($_GET['id']);
			if(!is_object($params['familia']['info']))
				unset($params['familia']);

			if (isset($params['familia'])) {
				if($params['familia']['info']->color1 == $params['familia']['info']->color2)
					$params['color_plano'] = true;
			}
			
			$params['productosr'] = $this->productos_model->obtenProductosBase('20'); //productos registrados
			//Listra de productos agregados
			$params['tabla_productos_r'] = $this->load->view('panel/familias/agregar_produc_listado', $params, true);
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/familias/modificar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Elimina un familia
	 */
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('familias_model');
			$respons = $this->familias_model->updateFamilia($_GET['id'], array('status' => '0'), null, true);
			
			if($respons[0])
				redirect(base_url('panel/familias/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}

	/**
	 * activa un familia
	 */
	public function activar(){
		if(isset($_GET['id']{0})){
			$this->load->model('familias_model');
			$respons = $this->familias_model->updateFamilia($_GET['id'], array('status' => '1'), null, true);
			
			if($respons[0])
				redirect(base_url('panel/familias/?msg=6'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}


	
	
	
	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddModFamilia($type='add'){
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'dnombre',
					'label'		=> 'Nombre',
					'rules'		=> 'required|max_length[80]'),
			array('field'	=> 'dprecio_venta',
					'label'		=> 'Precio venta',
					'rules'		=> 'numeric'),
			array('field'	=> 'dfpadre',
					'label'		=> 'Familia Padre',
					'rules'		=> 'required|numeric'),
			array('field'	=> 'dcolor',
					'label'		=> 'Color',
					'rules'		=> 'max_length[7]'),

			array('field'	=> 'dpcids[]',
					'label'	=> 'ID Producto',
					'rules'	=> 'numeric'),
			array('field'	=> 'dpccantidad[]',
					'label'	=> 'Cantidad consumo',
					'rules'	=> 'numeric'),

			array('field'	=> 'dcolor_plano',
					'label'	=> 'Color plano',
					'rules'	=> '')
		);

		if ($type == 'add') {
			$rules[] = array('field'	=> 'dcodigo',
					'label'		=> 'Codigo de Barras',
					'rules'		=> 'max_length[50]|is_unique[productos_familias.codigo_barra]');
		}else{
			$rules[] = array('field'	=> 'dcodigo',
					'label'		=> 'Codigo de Barras',
					'rules'		=> 'max_length[50]|callback_valida_codigo');
		}
		$this->form_validation->set_rules($rules);
	}

	public function valida_codigo($codigo){
		if (!$this->familias_model->validCodigo($_GET['id'], $codigo)) {
			$this->form_validation->set_message('valida_codigo', 'El %s no esta disponible, intenta con otro.');
			return FALSE;
		}
		return TRUE;
	}

	
	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Productos!'){
		switch($tipo){
			case 1:
				$txt = 'El campo ID es requerido.';
				$icono = 'error';
			break;
			case 2: //Cuendo se valida con form_validation
				$txt = $msg;
				$icono = 'error';
			break;
			case 3:
				$txt = 'La familia se modifico correctamente.';
				$icono = 'success';
			break;
			case 4:
				$txt = 'La familia se agrego correctamente.';
				$icono = 'success';
			break;
			case 5:
				$txt = 'La familia se elimino correctamente.';
				$icono = 'success';
			break;
			case 6:
				$txt = 'La familia se activo correctamente.';
				$icono = 'success';
			break;
		}
		
		return array(
			'title' => $title,
			'msg' => $txt,
			'ico' => $icono);
	}
}

?>