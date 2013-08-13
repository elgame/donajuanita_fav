<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class reportes extends MY_Controller {
  /**
   * Evita la validacion (enfocado cuando se usa ajax). Ver mas en privilegios_model
   * @var unknown_type
   */
  private $excepcion_privilegio = array('reportes/inventario_pdf/', 'reportes/bajos_inventario_pdf/', 
      'reportes/ventas_pdf/', 'reportes/ventas_productos_pdf/', 'reportes/productos_vendidos/', 'reportes/familias_descripcion_pdf/');


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

  public function index()
  {
    
  }


  /****************************************
   *           REPORTES                   *
   ****************************************/

  public function inventario()
  {
    $this->carabiner->js(array(
      array('panel/reportes/inventario.js'),
    ));

    $params['info_empleado']  = $this->info_empleado['info'];
    $params['seo']        = array('titulo' => 'Reporte Inventario');

    $this->load->view('panel/reportes/inventario',$params);
  }
  public function inventario_pdf()
  {
    $this->load->model('reportes_model');
    $this->reportes_model->RInventario();
  }


  public function bajos_inventario()
  {
    $this->carabiner->js(array(
      array('panel/reportes/inventario.js'),
    ));

    $params['info_empleado']  = $this->info_empleado['info'];
    $params['seo']        = array('titulo' => 'Reporte Bajos de Inventario');

    $this->load->view('panel/reportes/bajos_inventario', $params);
  }
  public function bajos_inventario_pdf()
  {
    $this->load->model('reportes_model');
    $this->reportes_model->RBajoInventario(null, date("Y-m-d"));
  }


  public function ventas()
  {
    $this->carabiner->js(array(
      array('panel/reportes/inventario.js'),
    ));

    $params['info_empleado']  = $this->info_empleado['info'];
    $params['seo']        = array('titulo' => 'Reporte Ventas');

    $this->load->view('panel/reportes/ventas', $params);
  }
  public function ventas_pdf()
  {
    $this->load->model('reportes_model');
    $this->reportes_model->RVentas();
  }

  public function ventas_productos()
  {
    $this->carabiner->js(array(
      array('panel/reportes/inventario.js'),
    ));

    $params['info_empleado']  = $this->info_empleado['info'];
    $params['seo']        = array('titulo' => 'Reporte Ventas de productos');

    $this->load->view('panel/reportes/ventas_productos', $params);
  }
  public function ventas_productos_pdf()
  {
    $this->load->model('reportes_model');
    $this->reportes_model->RVentasProductos();
  }


  /**
   * Metodo ajax para obtener los productos mas y menos vendidos
   * @return [type] [description]
   */
  public function productos_vendidos()
  {
    $this->load->model('reportes_model');

    if ($this->input->get('tipo') == '1')
      $tipo = 'cantidad DESC LIMIT 10';
    else
      $tipo = 'cantidad ASC LIMIT 10';
    $params['data'] = $this->reportes_model->productos_vendidos($this->input->get('fecha1'), $this->input->get('fecha2'), $tipo);
    $this->load->view('panel/reportes/productos_vendidos_tds', $params);
  }


  public function familias_descripcion()
  {
    $this->carabiner->js(array(
      array('panel/reportes/inventario.js'),
    ));

    $params['info_empleado']  = $this->info_empleado['info'];
    $params['seo']        = array('titulo' => 'Listado de Familias');

    $this->load->view('panel/reportes/familias_descripcion', $params);
  }
  public function familias_descripcion_pdf()
  {
    $this->load->model('reportes_model');
    $this->reportes_model->RFamiliasDescripcion();
  }


  /**
   * Muestra mensajes cuando se realiza alguna accion
   * @param unknown_type $tipo
   * @param unknown_type $msg
   * @param unknown_type $title
   */
  private function showMsgs($tipo, $msg='', $title='Facturacion!'){
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
        $txt = 'La Factura se modifico correctamente.';
        $icono = 'success';
        break;
      case 4:
        $txt = 'La Factura se agrego correctamente.';
        $icono = 'success';
        break;
      case 5:
        $txt = 'La Factura se cancelo correctamente.';
        $icono = 'success';
        break;
      case 6:
        $txt = 'La Serie y Folio se agregaron correctamente.';
        $icono = 'success';
        break;
      case 7:
        $txt = 'La Serie y Folio se modifico correctamente.';
        $icono = 'success';
        break;
      case 8:
        $txt = $msg;
        $icono = 'success';
        break;
      case 9:
        $txt = 'La Factura se pagó correctamente.';
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