<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class punto_venta extends MY_Controller {
  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array(
    'punto_venta/ajax_get_padres/',
    'punto_venta/ajax_get_hijos/',
    'punto_venta/ajax_save_venta/',
    'punto_venta/imprime_ticket/',
    'punto_venta/ajax_codigo_barras/',
    'punto_venta/ajax_get_pbf/',
  );

  public function _remap($method){

    $this->load->model("usuarios_model");
    if($this->usuarios_model->checkSession()){
      $this->usuarios_model->excepcion_privilegio = $this->excepcion_privilegio;
      $this->info_empleado = $this
        ->usuarios_model
        ->get_usuario_info($this->session->userdata('id_usuario'), true);

      if($this->usuarios_model->tienePrivilegioDe('', get_class($this).'/'.$method.'/')){
        $this->{$method}();
      }else
        redirect(base_url('panel/home?msg=1'));
    }else
      redirect(base_url('panel/home'));
  }

  /**
   * Default. Muestra la interfaz grafica del Punto de Venta
   */
  public function index(){

    $this->carabiner->css(array(
      array('panel/punto_venta.css')
    ));

    $this->carabiner->js(array(
        // array('general/msgbox.js'),
        array('general/supermodal.js'),
        array('general/util.js'),
        array('libs/jquery.numeric.js'),
        array('libs/jquery.event.move.js'),
        array('libs/jquery.event.swipe.js'),
        array('general/keyjump.js'),
        array('panel/punto_venta/pv.js'),
        array('panel/punto_venta/calc.js'),
    ));

    $this->load->model('punto_venta_model');
    $this->load->model('config_model');
    // $this->load->library('pagination');

    $params['info_empleado'] = $this->info_empleado['info']; //info empleado
    $params['seo'] = array(
      'titulo' => 'Punto de Venta'
    );

    $params['productos_padres'] = $this->punto_venta_model->getPadres();

    // echo "<pre>";
    //   var_dump($params);
    // echo "</pre>";exit;

    $params['productos_hijos_level1'] =$this->punto_venta_model->getHijos(
        (isset($params['productos_padres'][0]->id)? $params['productos_padres'][0]->id: 0) );

    $params['productos_hijos_level2'] =$this->punto_venta_model->getHijos(
        (isset($params['productos_hijos_level1'][0]->id)? $params['productos_hijos_level1'][0]->id: 0) );

    $params['config'] = $this->config_model->getInfoConfig(1);

    $this->load->view('panel/header', $params);
    $this->load->view('panel/punto_venta/punto_venta');
    $this->load->view('panel/footer', $params);
  }

  /**
   * Peticion ajax para obtener los padres.
   * Recibe un $_GET['id_padre']
   */
  public function ajax_get_padres()
  {
    $this->load->model('punto_venta_model');
    $padres = $this->punto_venta_model->getPadresFromHijo($this->input->get('id_padre'));

    echo json_encode($padres);
  }

  /**
   * Peticion ajax para obtener los hijos de un padre.
   * Recibe un $_GET['id']
   */
  public function ajax_get_hijos()
  {
    $this->load->model('punto_venta_model');
    $hijos = $this->punto_venta_model->getHijos($this->input->get('id'));

    echo json_encode($hijos);
  }

  public function ajax_save_venta()
  {
    header('content-type: application/json');

    $imprimir = false;
    // Verifica si se va a imprimir la venta.
    if ($_POST['venta'][0] === 'imprimir')
    {
      array_shift($_POST['venta']);
      $imprimir = true;
    }

    $this->load->model('punto_venta_model');
    $res = $this->punto_venta_model->save_venta();

    if ($res)
      echo json_encode(array($res, $this->showMsgs(3), 'imprimir' => $imprimir));
    else
      echo json_encode(array(0, $this->showMsgs(2, "Ocurrio un problema al intentar guardar la venta."), 'imprimir' => $imprimir));
  }

  public function ajax_codigo_barras()
  {
    $this->load->model('punto_venta_model');
    $res = $this->punto_venta_model->get_producto_by_codigo_barras($this->input->post('codigo_barras'));

    if ($res)
    {
      echo json_encode($res);
    }
    else
    {
      echo json_encode($this->showMsgs(2, 'El codigo de barras no fue encontrado.'));
    }
  }

  public function ajax_get_pbf()
  {
    $this->load->model('punto_venta_model');
    $res = $this->punto_venta_model->getProductosBaseAjax();

    echo json_encode($res);
  }

  public function imprime_ticket()
  {
    $this->load->model('punto_venta_model');
    $this->punto_venta_model->imprime_ticket($this->input->get('id'));
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
        $txt = 'La venta se guardo correctamente.';
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

/* End of file touch.php */
/* Location: ./application/controllers/panel/touch.php */