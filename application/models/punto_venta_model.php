<?php
class punto_venta_model extends CI_Model{

  public $excepcion_privilegio = array();

  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene los Padres Principales
   * @return Obj
   */
  public function getPadres()
  {
    // $res = $this->db->query("SELECT id, id_padre, nombre, precio_venta, imagen, color1, color2, ultimo_nodo, status
    //                          FROM productos_familias
    //                          WHERE id_padre = 1 AND status = 1
    //                          ORDER BY nombre ASC");
    $res = $this->getHijos(1);

    return $res;
  }

  /**
   * Obtiene los Hijos de un padre
   * @param  [int] $id_padre [ID del Padre del que se obtendran los hijos]
   * @return Obj
   */
  public function getHijos($id_padre)
  {
    $res = $this->db->query("SELECT pf.id,
                                    pf.id_padre,
                                    pf.nombre,
                                    pf.precio_venta,
                                    pf.imagen,
                                    pf.color1,
                                    pf.color2,
                                    IF(Count(pbf.familia_id)=0, 0,pf.ultimo_nodo) AS ultimo_nodo,
                                    pf.status
                            FROM productos_familias AS pf
                            LEFT JOIN productos_base_familia AS pbf ON pbf.familia_id = pf.id
                            WHERE pf.id_padre = ". $id_padre ." AND pf.status = 1
                            GROUP BY pf.id
                            ORDER BY pf.nombre ASC");


    $productos_familias = array();
    if ($res->num_rows() > 0)
    {
      $productos_familias = $res->result();

      foreach($productos_familias as $hijo) {
        if ($hijo->ultimo_nodo == 1)
        {
          $query = $this->db->query("SELECT base_id,
                                            cantidad,
                                            IFNULL( (SELECT precio_compra FROM productos_base_entradas
                                              WHERE base_id = pbf.base_id ORDER BY fecha DESC LIMIT 1), 0) AS precio_compra,
                                            (SELECT nombre FROM productos_base WHERE id = base_id) AS nombre
                                     FROM productos_base_familia AS pbf
                                     WHERE familia_id = ".$hijo->id."");

          if ($query->num_rows() > 0)
            $hijo->productos_base_fam = $query->result();
        }
      }
    }


    return $productos_familias;
  }

  /**
   * Obtiene el padre de un hijo y todos los demas padres que pertencen al mismo nivel que el padre del hijo.
   * @param  [int] $id_padre [ID del padre]
   * @return Obj
   */
  public function getPadresFromHijo($id_padre)
  {
    $res = $this->db->query("SELECT id, id_padre, nombre, precio_venta, imagen, color1, color2, ultimo_nodo, status
                             FROM productos_familias
                             WHERE id_padre = (SELECT id_padre FROM productos_familias WHERE id = " . $id_padre . ") AND status = 1
                             ORDER BY nombre ASC");

    return $res->result();
  }

  /**
   * Guarda la venta
   * @param  array $data [Array con los productos]:
   *
   *        - La primera posicion del array debe ser el total de la venta.
   *        - La segunda posicion del array debe ser la cantidad recibida.
   *        - La tercera posicion del array debe ser el cambio de la venta.
   *
   *        - Los demas indices del array son los items/productos con la estructura string:
   *            "id, cantidad, precio, importe"
   *
   *        Ejemplo:
   *        array(2) {[1]=>  string(13) "7,3,20.5,61.5" [2] => string(9) "5,1,25,25"}
   *
   * @return [boolean]
   */
  public function save_venta($data = false)
  {
    // Asigna $data o $_POST['venta']
    $venta = (is_array($data)) ? $data : $this->input->post('venta');

    // Si es un array
    if (is_array($venta))
    {
      $subtotal_no_iva  = array_shift($venta); // Obtiene el subtotal sin iva
      $iva       = array_shift($venta); // Obtiene el iva
      $subtotal  = array_shift($venta); // Obtiene el subtotal de la venta
      $recibido  = array_shift($venta); // Obtiene lo recibido/entregado
      $cambio    = array_shift($venta); // Obtiene el cambio de la venta
      $descuento = array_shift($venta); // Obtiene el descuento
      $total     = array_shift($venta); // Obtiene el total

      $tipo_pago = array_shift($venta); // Obtiene el tipo de pago

      // Realiza la consulta para obtener el ultimo folio
      $query = $this->db->query('SELECT folio FROM tickets ORDER BY id DESC LIMIT 1');
      if ($query->num_rows() > 0) // Si existe algun folio lo obtiene y le suma 1
      {
        $qres = $query->result();
        $folio = $qres[0]->folio + 1;
      }
      else // si no existe ningun folio inicia con el 1
      {
        $folio = 1;
      }

      // Array con la informacion del ticket
      $data_ticket = array('usuario_id' => $this->session->userdata('id'),
                           // 'cliente_id' => ,
                           'folio'     => $folio,
                           'fecha'     => date('Y-m-d H:i:s'),
                           'subtotal_no_iva' => $subtotal_no_iva,
                           'iva'       => $iva,
                           'subtotal'  => $subtotal,
                           'recibido'  => $recibido,
                           'cambio'    => $cambio,
                           'descuento' => $descuento,
                           'total'     => $total,
                           'tipo_pago' => $tipo_pago);

      $this->db->insert('tickets', $data_ticket); // Insert la informacion del ticket
      $id_venta = $this->db->insert_id(); // Obtiene el id autoincrement con el que se guardo el ticket

      $data_detalle           = array(); // Array que almacena los productos del ticket
      $data_prod_base_salidas = array(); // Array que almacena los productos base para las salidas
      // Ciclo que recorre los productos para construir el array $data_detalle
      foreach($venta as $key => $val)
      {
        $info = explode(',', $val);

        $data_detalle[] = array('ticket_id'    => $id_venta,
                                'familia_id'   => $info[0],
                                'cantidad'     => $info[1],
                                'precio_venta' => $info[2],
                                'importe'      => $info[3],
                                'descuento'    => $info[4]);


        $jsonPb = json_decode(str_replace("’", '"', str_replace('#', ',', $info[5])));

        // var_dump($jsonPb);

        foreach ($jsonPb as $prod_base)
        {
          $cantidad = $prod_base->cantidad * $data_detalle[$key]['cantidad'];

          $data_prod_base_salidas[] = array('ticket_id'     => $id_venta,
                                            'familia_id'    => $data_detalle[$key]['familia_id'],
                                            'base_id'       => $prod_base->base_id,
                                            'fecha'         => date('Y-m-d H:i:s'),
                                            'cantidad'      => $cantidad,
                                            'precio_compra' => floatval($prod_base->precio_compra),
                                            'importe'       => floatval($cantidad) * floatval($prod_base->precio_compra));
        }



        // $query = $this->db->query("SELECT base_id, (cantidad * ". $data_detalle[$key]['cantidad'] .") AS cantidad,
        //                                   (SELECT precio_compra FROM productos_base_entradas
        //                                     WHERE base_id = pbf.base_id ORDER BY fecha DESC LIMIT 1) AS precio
        //                            FROM productos_base_familia AS pbf
        //                            WHERE familia_id = ".$data_detalle[$key]['familia_id']."");

        // if ($query->num_rows() > 0)
        // {
        //   foreach ($query->result() as $prod_base)
        //   {
        //     $data_prod_base_salidas[] = array('ticket_id'     => $id_venta,
        //                                       'familia_id'    => $data_detalle[$key]['familia_id'],
        //                                       'base_id'       => $prod_base->base_id,
        //                                       'fecha'         => date('Y-m-d H:i:s'),
        //                                       'cantidad'      => $prod_base->cantidad,
        //                                       'precio_compra' => $prod_base->precio,
        //                                       'importe'       => floatval($prod_base->cantidad) * floatval($prod_base->precio));
        //   }
        // }
      }

      // Inserta los productos del ticket
      $this->db->insert_batch('tickets_detalle', $data_detalle);
      $this->db->insert_batch('productos_base_salidas', $data_prod_base_salidas);

      return $id_venta;
    }
    else
    {
      return false;
    }
  }

  public function get_producto_by_codigo_barras($codigo_barras = false)
  {
    $codigo_barras = ($codigo_barras) ? $codigo_barras : $this->input->post('codigo_barras');

    $query = $this->db->query("SELECT id, nombre, precio_venta
                               FROM productos_familias
                               WHERE codigo_barra = '" . $codigo_barras . "' AND ultimo_nodo = 1 AND status = 1");


    if ($query->num_rows() > 0)
    {
      $prod = $query->result();

      $query2 = $this->db->query("SELECT base_id,
                                            cantidad,
                                            IFNULL( (SELECT precio_compra FROM productos_base_entradas
                                              WHERE base_id = pbf.base_id ORDER BY fecha DESC LIMIT 1), 0) AS precio_compra,
                                            (SELECT nombre FROM productos_base WHERE id = base_id) AS nombre
                                     FROM productos_base_familia AS pbf
                                     WHERE familia_id = ".$prod[0]->id."");
      if ($query->num_rows() > 0)
        $prod[0]->productos_base_fam = $query2->result();

        return $prod;
    }

    return false;
  }

  /**
   * Imprime el ticket
   * @param  array  $data [description]
   * @return void
   */
  public function imprime_ticket()
  {
    $this->load->library('mypdf_ticket'); // Carga libreria

    // Query para obtener la informacion del ticket
    $query = $this->db->query("SELECT t.subtotal_no_iva, t.iva, t.subtotal, t.recibido, t.cambio, t.descuento,
                                      t.total, t.folio AS id_ticket, t.tipo_pago, t.fecha
                               FROM tickets AS t
                               WHERE t.id = " . $_GET['id']);

    $data = $query->result(); // Obtiene el resultado del query
    // $data[0]->recibido = $_GET['e'];
    // $data[0]->cambio   = floatval($_GET['e']) - floatval($data[0]->total);

    $data[0]->subtotal_no_iva  = $data[0]->subtotal_no_iva;
    $data[0]->iva       = $data[0]->iva;
    $data[0]->subtotal  = $data[0]->subtotal;
    $data[0]->recibido  = $data[0]->recibido;
    $data[0]->cambio    = $data[0]->cambio;
    $data[0]->descuento = $data[0]->descuento;
    $data[0]->total     = $data[0]->total;
    $data[0]->fecha     = $data[0]->fecha;


    //Datos de la empresa
    $data[0]->empresa_nombre       = $this->config->item('empresa_nombre');
    $data[0]->empresa_razon_social = $this->config->item('empresa_razon_social');
    $data[0]->empresa_rfc          = $this->config->item('empresa_rfc');
    $data[0]->empresa_calle        = $this->config->item('empresa_calle');
    $data[0]->empresa_num_ext      = $this->config->item('empresa_num_ext');
    $data[0]->empresa_num_int      = $this->config->item('empresa_num_int');
    $data[0]->empresa_colonia      = $this->config->item('empresa_colonia');
    $data[0]->empresa_municipio    = $this->config->item('empresa_municipio');
    $data[0]->empresa_estado       = $this->config->item('empresa_estado');
    $data[0]->empresa_cp           = $this->config->item('empresa_cp');
    $data[0]->empresa_telefono     = $this->config->item('empresa_telefono');
    $data[0]->empresa_url_logo     = $this->config->item('empresa_url_logo');
    $data[0]->empresa_url_logop    = $this->config->item('empresa_url_logop');
    $data[0]->empresa_email        = $this->config->item('empresa_email');
    $data[0]->empresa_pag_web      = $this->config->item('empresa_pag_web');
    $data[0]->empresa_footer       = $this->config->item('empresa_footer');

    // Query que obtiene los productos/items del ticket o venta
    $query = $this->db->query("SELECT pf.nombre, td.cantidad, td.precio_venta, td.importe, td.descuento
                               FROM tickets_detalle as td
                               INNER JOIN productos_familias AS pf ON pf.id = td.familia_id
                               WHERE td.ticket_id = " . $_GET['id']);

    $data_prod = $query->result();

    $pdf = new mypdf_ticket();
    $pdf->SetFont('Arial','',8);
    $pdf->AddPage();

    $pdf->printTicket($data, $data_prod);

    $pdf->AutoPrint(true);
    $pdf->Output();
  }

  /**
   * Obtiene el listado de camiones para usar ajax
   * @param term. termino escrito en la caja de texto, busca en las placas, modelo, marca
   */
  public function getProductosBaseAjax(){
    $sql = '';
    if ($this->input->get('term') !== false)
      $sql = " AND ( lower(pb.nombre) LIKE '%".mb_strtolower($this->input->get('term'), 'UTF-8')."%')";

    $res = $this->db->query("
        SELECT pb.id,
               pb.nombre,
               0 as cantidad,
               (SELECT precio_compra FROM productos_base_entradas
                  WHERE base_id = pb.id ORDER BY fecha DESC LIMIT 1) AS precio_compra
        FROM productos_base AS pb
        WHERE status = '1' ".$sql."
        ORDER BY pb.nombre ASC
        LIMIT 20");

    $response = array();
    if($res->num_rows() > 0){
      foreach($res->result() as $itm){
        $response[] = array(
            'id'    => $itm->id,
            'label' => $itm->nombre,
            'value' => $itm->nombre,
            'item'  => $itm,
        );
      }
    }

    return $response;
  }

}

/* End of file punto_venta.php */
/* Location: ./application/models/punto_venta.php */