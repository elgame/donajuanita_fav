<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class config extends MY_Controller {
	/**
	 * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
	 * @var unknown_type
	 */
	private $excepcion_privilegio = array('');

	public function _remap($method){

		$this->load->model("usuarios_model");
		if($this->usuarios_model->checkSession()){
			$this->usuarios_model->excepcion_privilegio = $this->excepcion_privilegio;
			$this->info_empleado                        = $this->usuarios_model->get_usuario_info($this->session->userdata('id'), true);

			$this->{$method}();
		}else
			$this->{'login'}();
	}

	public function index(){
		$this->carabiner->css(array(
			array('libs/jquery.uniform.css', 'screen'),
			array('libs/jquery.colorpicker2.css', 'screen'),
		));
		$this->carabiner->js(array(
			array('libs/jquery.uniform.min.js'),
			array('libs/jquery.numeric.js'),
			array('libs/jquery.colorpicker2.js'),
			array('libs/jquery.colorpicker2eye.js'),
			array('libs/jquery.colorpicker2layout.js'),
			array('libs/jquery.colorpicker2utils.js'),
			array('general/msgbox.js'),
			array('panel/config/frm_mod.js')
		));

		$params['info_empleado'] = $this->info_empleado['info']; //info empleado
		$params['seo'] = array(
				'titulo' => 'Configuración'
		);

		$this->configAddModEmpl();
		$this->load->model('config_model');

		if($this->form_validation->run() == FALSE){
			$params['frm_errors'] = $this->showMsgs(2, preg_replace("[\n|\r|\n\r]", '', validation_errors()));
		}else{
			$respons = $this->config_model->updateConfig(1);

			if($respons[0])
				redirect(base_url('panel/config/?'.String::getVarsLink(array('msg')).'&msg='.$respons[2]));
		}

		$params['info'] = $this->config_model->getInfoConfig(1);

		if(isset($_GET['msg']{0}))
				$params['frm_errors'] = $this->showMsgs($_GET['msg']);

		$this->load->view('panel/header', $params);
		$this->load->view('panel/general/menu', $params);
		$this->load->view('panel/config/modificar', $params);
		$this->load->view('panel/footer');
	}

	/**
	 * Configura los metodos de agregar y modificar
	 */
	private function configAddModEmpl(){
		$this->load->library('form_validation');

			$rules = array(
				array('field'	=> 'dnombre',
						'label'	=> 'Nombre',
						'rules'	=> 'max_length[150]'),
				array('field'	=> 'drazon_social',
						'label'	=> 'Razon social',
						'rules'	=> 'max_length[150]'),
				array('field'	=> 'drfc',
						'label'	=> 'RFC',
						'rules'	=> 'max_length[15]'),
				array('field'	=> 'dcalle',
						'label'	=> 'Calle',
						'rules'	=> 'max_length[100]'),
				array('field'	=> 'dno_exterior',
						'label'	=> 'No exterior',
						'rules'	=> 'max_length[12]'),
				array('field'	=> 'dno_interior',
						'label'	=> 'No interior',
						'rules'	=> 'max_length[12]'),
				array('field'	=> 'dcolonia',
						'label'	=> 'Colonia',
						'rules'	=> 'max_length[100]'),
				array('field'	=> 'dmunicipio',
						'label'	=> 'Municipio',
						'rules'	=> 'max_length[100]'),
				array('field'	=> 'destado',
						'label'	=> 'Estado',
						'rules'	=> 'max_length[100]'),
				array('field'	=> 'dcp',
						'label'	=> 'CP',
						'rules'	=> 'max_length[20]'),
				array('field'	=> 'dtelefono',
						'label'	=> 'Teléfono',
						'rules'	=> 'max_length[50]'),
				array('field'	=> 'dpag_web',
						'label'	=> 'Pag web',
						'rules'	=> 'max_length[130]'),
				array('field'	=> 'demail',
						'label'	=> 'Email',
						'rules'	=> 'valid_email|max_length[80]'),
				array('field'	=> 'dfooter',
						'label'	=> 'Texto al final del Ticket',
						'rules'	=> ''),
				// array('field'	=> 'dlogo',
				// 		'label'	=> 'Nombre',
				// 		'rules'	=> 'max_length[120]'),
				array('field'	=> 'durl_logop',
						'label'	=> 'Imp. Logo en Ticket',
						'rules'	=> 'max_length[10]'),
				array('field'	=> 'dcolor_1',
						'label'	=> 'Color 1',
						'rules'	=> 'max_length[15]'),
				array('field'	=> 'dcolor_2',
						'label'	=> 'Color 1',
						'rules'	=> 'max_length[15]'),
				array('field'	=> 'dfuente_pv',
						'label'	=> 'Tamaño de fuente en punto de venta',
						'rules'	=> 'max_length[15]'),

			);
		$this->form_validation->set_rules($rules);
	}


	/**
	 * Muestra mensajes cuando se realiza alguna accion
	 * @param unknown_type $tipo
	 * @param unknown_type $msg
	 * @param unknown_type $title
	 */
	private function showMsgs($tipo, $msg='', $title='Clientes!'){
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
				$txt = 'La configuración se agrego correctamente.';
				$icono = 'success';
			break;
			case 4:
				$txt = 'La configuración se modifico correctamente.';
				$icono = 'success';
				break;
			case 5:
				$txt = 'La configuración se elimino correctamente.';
				$icono = 'success';
				break;
			case 6:
				$txt = 'La configuración se activo correctamente.';
				$icono = 'success';
			break;
			case 7:
				$txt = 'El contacto se elimino correctamente.';
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