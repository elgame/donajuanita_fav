<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class productos extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('productos/ajax_productos_addmod/');
	
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
	 * Default. Mustra el listado de productos base para administrarlos
	 */
	public function index(){
		$this->carabiner->js(array(
				array('general/msgbox.js'),
				array('general/supermodal.js'),
		));
		
		$this->load->library('pagination');
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Administrar Productos Base'
		);
		
		$this->load->model('productos_model');
		$params['productos'] = $this->productos_model->obtenProductosBase();
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/productos/listado', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Agrega un productos base a la bd
	 */
	public function agregar(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
			array('libs/jquery.treeview.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.treeview.js'),
			array('libs/jquery.numeric.js'),
			array('general/util.js'),
			array('panel/productos/frm_addmod.js'),
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar Producto'
		);

		$this->load->model('familias_model');
		
		$this->configAddModProducto();
		
		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$this->load->model('productos_model');
			$respons = $this->productos_model->addProducto();
			
			if($respons[0])
				redirect(base_url('panel/productos/agregar/?'.String::getVarsLink(array('msg')).'&msg=4'));
		}
		
		if(isset($_GET['msg']{0}))
			$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/productos/agregar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Modificar productos bases
	 */
	public function modificar(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
			array('libs/jquery.treeview.css', 'screen')
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.treeview.js'),
			array('libs/jquery.numeric.js'),
			array('general/util.js'),
			array('panel/productos/frm_addmod.js'),
		));
		
		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Modificar producto'
		);
		
		if(isset($_GET['id']{0})){
			$this->configAddModProducto('edit');

			$this->load->model('productos_model');
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->productos_model->updateProducto($_GET['id']);
				
				if($respons[0])
					redirect(base_url('panel/productos/?'.String::getVarsLink(array('msg', 'id')).'&msg=3'));
			}
			
			$params['producto'] = $this->productos_model->getInfoProducto($_GET['id']);
			if(!is_object($params['producto']))
				unset($params['producto']);
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);
		}else
			$params['frm_errors'] = $this->showMsgs(1);
		
		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/productos/modificar', $params);
		$this->load->view('panel/footer');
	}
	
	/**
	 * Elimina un productos de la bd
	 */
	public function eliminar(){
		if(isset($_GET['id']{0})){
			$this->load->model('productos_model');
			$respons = $this->productos_model->updateProducto($_GET['id'], array('status' => '0'));
			
			if($respons[0])
				redirect(base_url('panel/productos/?msg=5'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}

	/**
	 * activa un productos de la bd
	 */
	public function activar(){
		if(isset($_GET['id']{0})){
			$this->load->model('productos_model');
			$respons = $this->productos_model->updateProducto($_GET['id'], array('status' => '1'));
			
			if($respons[0])
				redirect(base_url('panel/productos/?msg=6'));
		}else
			$params['frm_errors'] = $this->showMsgs(1);
	}

	/**
	 * Obtiene el listado de productos registrados utilizando Ajax, solo la tabla
	 * la uso para buscar productos en Agregar y Modificar productos
	 */
	public function ajax_productos_addmod(){
		$this->load->model('productos_model');
		$this->load->library('pagination');
		
		//en modificar quito el producto q se esta modificando
		$sql = isset($_GET['id_producto'])? " pb.id != '".$_GET['id_producto']."'": '';
		$params['productosr'] = $this->productos_model->obtenProductosBase('20', $sql); //productos registrados

		$this->load->view('panel/familias/agregar_produc_listado', $params);
	}



	/**
	 * ***********************************************
	 * *********  Agregar Inventario *****************
	 * ***********************************************
	 * @return [type] [description]
	 */
	public function agregar_inventario(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.numeric.js'),
		));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
			'titulo' => 'Agregar inventario'
		);


		if(isset($_GET['id']{0})){ //id del producto
			$this->load->model('productos_model');
			
			$this->configAddInventario();
			
			if($this->form_validation->run() == FALSE){
				$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
			}else{
				$respons = $this->productos_model->addInventario($_GET['id']);
				
				if($respons[0])
					redirect(base_url('panel/productos/agregar_inventario/?'.String::getVarsLink(array('msg')).'&msg=7'));
			}

			$params['producto'] = $this->productos_model->getInfoProducto($_GET['id']);
			if(!is_object($params['producto']))
				unset($params['producto']);
			
			if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		}else
			$params['frm_errors'] = $this->showMsgs(1);

		$this->load->view('panel/productos/inventario/agregar_inventario', $params);
	}


	
	
	
	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddModProducto($type='add'){
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'dnombre',
					'label'		=> 'Nombre',
					'rules'		=> 'required|max_length[80]'),
			array('field'	=> 'dstock_min',
					'label'		=> 'Stock minimo',
					'rules'		=> 'required|numeric'),
			
			array('field'	=> 'dmarca',
					'label'		=> 'Marca',
					'rules'		=> 'max_length[80]'),
			array('field'	=> 'did_proveedor',
					'label'		=> 'Proveedor id',
					'rules'		=> 'numeric'),
			array('field'	=> 'dproveedor',
					'label'		=> 'Proveedor',
					'rules'		=> 'max_length[180]'),
			array('field'	=> 'ddescripcion',
					'label'		=> 'Descripción',
					'rules'		=> ''),

			array('field'	=> 'dis_same_fam',
					'label'		=> 'Registrar en familias',
					'rules'		=> 'max_length[2]'),
		);

		if ($type == 'add') {
			$rules[] = array('field'	=> 'dprecio_compra',
					'label'		=> 'Precio compra',
					'rules'		=> 'required|numeric');
			$rules[] = array('field'	=> 'dcantidad',
					'label'		=> 'Cantidad inicial',
					'rules'		=> 'required|numeric');

			if ($this->input->post('dis_same_fam') == 'si') {
				$rules[] = array('field'	=> 'dprecio_venta',
					'label'		=> 'Precio venta',
					'rules'		=> 'required|numeric');
				$rules[] = array('field'	=> 'dcodigo',
					'label'		=> 'Codigo de Barras',
					'rules'		=> 'max_length[50]|is_unique[productos_familias.codigo_barra]');
				$rules[] = array('field'	=> 'dfpadre',
					'label'		=> 'Familia Padre',
					'rules'		=> 'required|numeric');
			}
		}
		$this->form_validation->set_rules($rules);
	}

	/**
	 * Configura los metodos de agregar inventario
	 */
	private function configAddInventario(){
		$this->load->library('form_validation');
		$rules = array(
			array('field'	=> 'dcantidad',
					'label'		=> 'Cantidad',
					'rules'		=> 'required|numeric'),
			array('field'	=> 'dprecio_compra',
					'label'		=> 'Precio compra',
					'rules'		=> 'required|numeric'),
		);
		$this->form_validation->set_rules($rules);
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
				$txt = 'El producto se modifico correctamente.';
				$icono = 'success';
			break;
			case 4:
				$txt = 'El producto se agrego correctamente.';
				$icono = 'success';
			break;
			case 5:
				$txt = 'El producto se elimino correctamente.';
				$icono = 'success';
			break;
			case 6:
				$txt = 'El producto se activo correctamente.';
				$icono = 'success';
			break;

			case 7:
				$txt = 'Se registro el inventario correctamente.';
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