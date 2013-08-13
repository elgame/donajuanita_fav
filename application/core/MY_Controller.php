<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
	protected $info_empleado;
	
	function MY_Controller($redirect=true){
		date_default_timezone_set('America/Mexico_City');
		parent::__construct();
		
		$this->limpiaParams();
		$this->updateSessionExp();
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('carabiner');
		$this->carabiner->config(
			array(
			    'base_uri'   => base_url(),
			    'combine'    => false,
			    'dev'        => true
		));
		$this->setConfig();
		$this->db->query("SET SQL_BIG_SELECTS=1");
	}
	
	private function limpiaParams(){
		foreach ($_POST as $key => $value)
    		$_POST[$key] = String::limpiarTexto(($value));
		
		foreach ($_GET as $key => $value)
			$_GET[$key] = String::limpiarTexto(($value));
	}

	private function setConfig(){
		$this->load->model('config_model');
		$data = $this->config_model->getInfoConfig(1);

		$this->config->set_item('empresa_nombre', $data->nombre);
		$this->config->set_item('empresa_razon_social', $data->razon_social);
		$this->config->set_item('empresa_rfc', $data->rfc);
		$this->config->set_item('empresa_calle', $data->calle);
		$this->config->set_item('empresa_num_ext', $data->num_ext);
		$this->config->set_item('empresa_num_int', $data->num_int);
		$this->config->set_item('empresa_colonia', $data->colonia);
		$this->config->set_item('empresa_municipio', $data->municipio);
		$this->config->set_item('empresa_estado', $data->estado);
		$this->config->set_item('empresa_cp', $data->cp);
		$this->config->set_item('empresa_telefono', $data->telefono);
		$this->config->set_item('empresa_url_logo', $data->url_logo);
		$this->config->set_item('empresa_url_logop', ($data->url_logop=='true'? true: false) );
		$this->config->set_item('empresa_email', $data->email);
		$this->config->set_item('empresa_pag_web', $data->pag_web);
		$this->config->set_item('empresa_footer', $data->footer);
		$this->config->set_item('empresa_color_1', $data->color_1);
		$this->config->set_item('empresa_color_2', $data->color_2);
		$this->config->set_item('empresa_fuente_pv', $data->fuente_pv);
	}

	/*
	|	Verifica si existe la session o cookie con el parametro remember que indica si el usuario 
	| al momento de loguearse marco el campo "no cerrar sesion"
	*/
	public function updateSessionExp()
	{
		if ($this->session->userdata('remember'))
		{
			$this->session->sess_expiration      = 60*60*24*365;
			$this->session->sess_expire_on_close = FALSE;

			$unset_data = array('id_usuario' => '', 
													'username'   => '', 
													'email'      => '',
													'remember'   => '', 
													'acceso'     => '', 
													'idunico'     => '',
													'tipo'       => '');
			
			$user_data  = array('id_usuario'=> $this->session->userdata('id_usuario'),
													'username'  => $this->session->userdata('username'),
													'email'     => $this->session->userdata('email'),
													'remember'	=> TRUE,
													'acceso'    => $this->session->userdata('acceso'), 
													'idunico'   => $this->session->userdata('idunico'),
													'tipo'      => $this->session->userdata('tipo'));
			
			$this->session->unset_userdata($unset_data);
			$this->session->set_userdata($user_data);
		}	
	}
}
?>