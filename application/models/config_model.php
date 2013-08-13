<?php

class config_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * Obtiene la informacion de un cliente
	 */
	public function getInfoConfig($id){
		$res = $this->db
			->select('*')
			->from('config AS c')
			->where("c.id = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response = $res->row();
			$res->free_result();

			return $response;
		}else
			return false;
	}

	/**
	 * Modifica la informacion de un cliente
	 */
	public function updateConfig($id_conf, $data=null, $data_ext=null){
		$msg = 4;
		if ($data == null) {
			$path_img = '';
			//valida la imagen
			$upload_res = UploadFiles::uploadImgLogo();
			if(is_array($upload_res)){
				if($upload_res[0] == false)
					return array(false, $upload_res[1]);
				$path_img = 'application/images/logos/'.$upload_res[1]['file_name'];
			}

			$data = array(
				'nombre'       =>  $this->input->post('dnombre'),
				'razon_social' =>  $this->input->post('drazon_social'),
				'rfc'          =>  $this->input->post('drfc'),
				'calle'        =>  $this->input->post('dcalle'),
				'num_ext'      =>  $this->input->post('dno_exterior'),
				'num_int'      =>  $this->input->post('dno_interior'),
				'colonia'      =>  $this->input->post('dcolonia'),
				'municipio'    =>  $this->input->post('dmunicipio'),
				'estado'       =>  $this->input->post('destado'),
				'cp'           =>  $this->input->post('dcp'),
				'telefono'     =>  $this->input->post('dtelefono'),
				'url_logop'    =>  ($this->input->post('durl_logop')=='true'? 'true': 'false'),
				'email'        =>  $this->input->post('demail'),
				'pag_web'      =>  $this->input->post('dpag_web'),
				'footer'       =>  $this->input->post('dfooter'),
				'color_1'      =>  $this->input->post('dcolor_1'),
				'color_2'      =>  $this->input->post('dcolor_2'),
				'fuente_pv'    =>  $this->input->post('dfuente_pv'),
			);
			if ($path_img!='')
				$data['url_logo'] = $path_img;
		}
		$this->db->update('config', $data, "id = '".$id_conf."'");

		return array(true, '', $msg);
	}


}