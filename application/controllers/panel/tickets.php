<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class tickets extends MY_Controller {
  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('tickets/ajax_get_info_ticket/');

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
   * Default. Mustra el listado de tickets para administrarlos
   */
  public function index(){
    $this->carabiner->js(array(
        array('general/util.js'),
        array('general/msgbox.js'),
        array('general/supermodal.js'),
        array('panel/tickets/listado.js')
    ));

    $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Administrar Tickets'
    );

    $this->load->model('tickets_model');
    $params['tickets'] = $this->tickets_model->obten_tickets();

    if(isset($_GET['msg']{0}))
      $params['frm_errors'] = $this->showMsgs($_GET['msg']);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/general/menu', $params);
    $this->load->view('panel/tickets/listado', $params);
    $this->load->view('panel/footer');
  }

  /**
   * Elimina un familia
   */
  public function eliminar(){
    if(isset($_GET['id']{0})){
      $this->load->model('tickets_model');
      $respons = $this->tickets_model->cancel_ticket($_GET['id']);

      if($respons[0])
        redirect(base_url('panel/tickets/?msg=3'));
    }else
      $params['frm_errors'] = $this->showMsgs(1);
  }

  public function ajax_get_info_ticket()
  {
    $this->load->model('tickets_model');
    $respons = $this->tickets_model->get_info_ticket(true);

    echo json_encode($respons);
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
        $txt = 'La familia se cancelo correctamente.';
        $icono = 'success';
      break;
    }

    return array(
      'title' => $title,
      'msg' => $txt,
      'ico' => $icono);
  }
}