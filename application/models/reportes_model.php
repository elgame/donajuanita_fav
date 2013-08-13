<?php
class reportes_model extends CI_Model{

	function __construct(){
		parent::__construct();
	}


   /****************************************
   *           REPORTES                   *
   ****************************************/
   /**
    * Reporte de inventario, obtiene la informacion para ese reporte
    * @param  [type] $fecha1 [description]
    * @param  [type] $fecha2 [description]
    * @return [type]         [description]
    */
   public function getDataRInventario($fecha1=null, $fecha2=null){
    $response = array('info' => array(), 'titulo3' => '');

    if (empty($_GET['ffecha1']) && empty($_GET['ffecha2'])){
      $_GET['ffecha1'] = $fecha1==null? '': $fecha1;
      $_GET['ffecha2'] = $fecha2==null? date("Y-m-d"): $fecha2;
    }

    $response['titulo1'] = $this->config->item('empresa_nombre');
    $response['logo'] = $this->config->item('empresa_url_logo');

    $sql = '';
    if (!empty($_GET['ffecha1']) && !empty($_GET['ffecha2'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al ".$_GET['ffecha2']."";
      $sql = "WHERE Date(fecha) BETWEEN '".$_GET['ffecha1']."' AND '".$_GET['ffecha2']."' ";
      $sql1 = " Date(pbs.fecha) BETWEEN '".$_GET['ffecha1']."' AND '".$_GET['ffecha2']."' ";
    }elseif (!empty($_GET['ffecha1'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al dia de Hoy";
      $sql = "WHERE Date(fecha) >= '".$_GET['ffecha1']."' ";
      $sql1 = " Date(pbs.fecha) >= '".$_GET['ffecha1']."' ";
    }elseif (!empty($_GET['ffecha2'])){
      $response['titulo3'] = "Hasta ".$_GET['ffecha2'];
      $sql = "WHERE Date(fecha) <= '".$_GET['ffecha2']."' ";
      $sql1 = " Date(pbs.fecha) <= '".$_GET['ffecha2']."' ";
    }

     $result = $this->db->query("SELECT 
          pb.id, pb.nombre, 
          ifnull(pbe.cantidad,0) AS entradas, 
          ifnull(pbs.cantidad,0) AS salidas, 
          (ifnull(pbe.cantidad,0) - ifnull(pbs.cantidad,0)) AS existencia,
          ifnull( (SELECT `precio_compra` FROM `productos_base_entradas` WHERE `base_id` = pb.id AND `cantidad` >= 0 ORDER BY fecha DESC LIMIT 1), 0 ) AS precio_compra
        FROM productos_base AS pb 
          LEFT JOIN (SELECT base_id, Sum(cantidad) AS cantidad, Sum(precio_compra) AS precio_compra, Sum(importe) AS importe 
            FROM productos_base_entradas 
             ".$sql."
            GROUP BY base_id
          ) AS pbe ON pbe.base_id = pb.id 
          LEFT JOIN (SELECT pbs.base_id, SUM( pbs.cantidad ) AS cantidad, SUM( pbs.precio_compra ) AS precio_compra, SUM( pbs.importe ) AS importe
            FROM tickets AS t
              INNER JOIN productos_base_salidas AS pbs ON pbs.ticket_id = t.id
            WHERE t.status =1 AND ".$sql1."
            GROUP BY pbs.base_id
          ) AS pbs ON pbs.base_id = pb.id
        WHERE pb.status = 1 ");
     if($result->num_rows() > 0)
      $response['info'] = $result->result();

    return $response;
   }
   /**
    * Genera el reporte en pdf
    */
   public function RInventario()
   {
      $this->load->library('mypdf');
      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('L', 'mm', 'Letter');
      $pdf->show_head = true;
      $pdf->titulo2 = 'Reporte de inventario';
      
      $data = $this->getDataRInventario();
      $pdf->titulo1 = $data['titulo1'];
      $pdf->titulo3 = $data['titulo3'];
      $pdf->logo    = $data['logo'];

      $pdf->AliasNbPages();
      // $links = array('', '', '', '');
      $pdf->SetY(30);
      $aligns = array('L', 'C', 'C', 'C','C', 'C');
      $widths = array(76, 38, 38, 38, 30, 45);
      $header = array('Nombre', 'Entradas', 'Salidas', 'Existencia', 'Precio compra', 'Total');
      $total = 0;

      foreach($data['info'] as $key => $item)
      {
        $band_head = false;
        if($pdf->GetY() >= $pdf->limiteY || $key==0) //salta de pagina si exede el max
        {
          $pdf->AddPage();

          $pdf->SetFont('Arial','B',8);
          $pdf->SetTextColor(255,255,255);
          $pdf->SetFillColor(160,160,160);
          $pdf->SetX(6);
          $pdf->SetAligns($aligns);
          $pdf->SetWidths($widths);
          $pdf->Row($header, true);
        }

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);

        $importe = String::float( ($item->existencia * $item->precio_compra) );
        $datos = array(
          $item->nombre, 
          String::formatoNumero($item->entradas, 0, ''), 
          String::formatoNumero($item->salidas, 0, ''), 
          String::formatoNumero($item->existencia, 0, ''), 
          String::formatoNumero($item->precio_compra), 
          String::formatoNumero($importe),
        );
        $total += $importe;

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false);
      }

      $pdf->SetX(6);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetTextColor(255,255,255);
      $pdf->Row(array('', '', '', '', 'Total:', String::formatoNumero($total)), true);

      $pdf->Output('reporte_inventario.pdf', 'I');
  }


  /**
   * Reporte bajos de inventario, obtiene los datos para generar el reporte
   * @param  [type] $fecha1 [description]
   * @param  [type] $fecha2 [description]
   * @return [type]         [description]
   */
  public function getDataRBajoInventario($fecha1=null, $fecha2=null){
    $response = array('info' => array(), 'titulo3' => '');

    if ($fecha1==null && $fecha2==null) {
      if (empty($_GET['ffecha1']) && empty($_GET['ffecha2'])){
        $_GET['ffecha1'] = $fecha1==null? date("Y-m").'-01': $fecha1;
        $_GET['ffecha2'] = $fecha2==null? date("Y-m-d"): $fecha2;
      }
    }else{
      $_GET['ffecha1'] = $fecha1==null? '': $fecha1;
      $_GET['ffecha2'] = $fecha2==null? '': $fecha2;
    }

    $response['titulo1'] = $this->config->item('empresa_nombre');
    $response['logo'] = $this->config->item('empresa_url_logo');

    $sql = $sql1 = $sql2 = '';
    if (!empty($_GET['ffecha1']) && !empty($_GET['ffecha2'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al ".$_GET['ffecha2']."";
      $sql = "WHERE Date(fecha) BETWEEN '".$_GET['ffecha1']."' AND '".$_GET['ffecha2']."' ";
      $sql1 = " Date(pbs.fecha) BETWEEN '".$_GET['ffecha1']."' AND '".$_GET['ffecha2']."' ";
    }elseif (!empty($_GET['ffecha1'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al dia de Hoy";
      $sql = "WHERE Date(fecha) >= '".$_GET['ffecha1']."' ";
      $sql1 = " Date(pbs.fecha) >= '".$_GET['ffecha1']."' ";
    }elseif (!empty($_GET['ffecha2'])){
      $response['titulo3'] = "Hasta ".$_GET['ffecha2'];
      $sql = "WHERE Date(fecha) <= '".$_GET['ffecha2']."' ";
      $sql1 = " Date(pbs.fecha) <= '".$_GET['ffecha2']."' ";
    }

    if(isset($_GET['did_proveedor']{0})){
      $sql2 = " AND p.id = ".$_GET['did_proveedor'];
    }

     $result = $this->db->query("SELECT 
          pb.id, pb.nombre, 
          CONCAT(p.nombre_fiscal, '\\n Tel. ', p.telefono1) AS proveedor,
          CONCAT(p.calle, ' #', p.no_exterior, ', ', p.colonia, ', ', p.municipio, ', ', p.estado) AS direccion,
          ifnull(pbe.cantidad,0) AS entradas, 
          ifnull(pbs.cantidad,0) AS salidas, 
          (ifnull(pbe.cantidad,0) - ifnull(pbs.cantidad,0)) AS existencia,
          pb.stock_min,
          ifnull( (SELECT `precio_compra` FROM `productos_base_entradas` WHERE `base_id` = pb.id AND `cantidad` >= 0 ORDER BY fecha DESC LIMIT 1), 0 ) AS precio_compra
        FROM productos_base AS pb 
          LEFT JOIN proveedores AS p ON pb.proveedor_id = p.id 
          LEFT JOIN (SELECT base_id, Sum(cantidad) AS cantidad, Sum(precio_compra) AS precio_compra, Sum(importe) AS importe 
            FROM productos_base_entradas 
             ".$sql."
            GROUP BY base_id
          ) AS pbe ON pbe.base_id = pb.id 
          LEFT JOIN (SELECT pbs.base_id, SUM( pbs.cantidad ) AS cantidad, SUM( pbs.precio_compra ) AS precio_compra, SUM( pbs.importe ) AS importe
            FROM tickets AS t
              INNER JOIN productos_base_salidas AS pbs ON pbs.ticket_id = t.id
            WHERE t.status =1 AND ".$sql1."
            GROUP BY pbs.base_id
          ) AS pbs ON pbs.base_id = pb.id
        WHERE pb.status = 1 ".$sql2." AND (ifnull(pbe.cantidad,0) - ifnull(pbs.cantidad,0)) < pb.stock_min");
     if($result->num_rows() > 0)
      $response['info'] = $result->result();

    return $response;
   }
   /**
    * Genera el reporte en pdf reporte bajos de inventario
    */
   public function RBajoInventario($fecha1=null, $fecha2=null)
   {
      $this->load->library('mypdf');
      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('L', 'mm', 'Letter');
      $pdf->show_head = true;
      $pdf->titulo2 = 'Reporte bajos de inventario';
      
      $data = $this->getDataRBajoInventario($fecha1, $fecha2);
      $pdf->titulo1 = $data['titulo1'];
      $pdf->titulo3 = $data['titulo3'];
      $pdf->logo    = $data['logo'];

      $pdf->AliasNbPages();
      // $links = array('', '', '', '');
      $pdf->SetY(30);
      $aligns = array('L', 'C', 'C', 'C', 'C', 'C', 'L', 'L');
      $widths = array(65, 25, 15, 25, 20, 30, 40, 50);
      $header = array('Nombre', 'Existencia', 'Stock min', 'Faltante min', 'Precio compra', 'Gasto', 'Proveedor', 'Direccion');
      $total = 0;

      foreach($data['info'] as $key => $item)
      {
        $band_head = false;
        if($pdf->GetY() >= $pdf->limiteY || $key==0) //salta de pagina si exede el max
        {
          $pdf->AddPage();

          $pdf->SetFont('Arial','B',8);
          $pdf->SetTextColor(255,255,255);
          $pdf->SetFillColor(160,160,160);
          $pdf->SetX(6);
          $pdf->SetAligns($aligns);
          $pdf->SetWidths($widths);
          $pdf->Row($header, true);
        }

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);

        $faltante = String::float( ($item->stock_min - $item->existencia) );
        $importe  = String::float( ($faltante * $item->precio_compra) );
        $datos    = array(
          $item->nombre, 
          String::formatoNumero($item->existencia, 0, ''), 
          String::formatoNumero($item->stock_min, 0, ''), 
          String::formatoNumero( $faltante, 0, ''), 
          String::formatoNumero($item->precio_compra, 2, ''), 
          String::formatoNumero( $importe, 2, ''), 
          $item->proveedor, 
          $item->direccion, 
        );
        $total += $importe;

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false, true);
      }

      $pdf->SetX(6);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetTextColor(255,255,255);
      $pdf->Row(array('', '', '', '', 'Gasto min:', String::formatoNumero($total)), true);

      $pdf->Output('reporte_bajos_inventario.pdf', 'I');
  }


  /**
   * Reporte ventas por producto
   * @param  [type] $fecha1 [description]
   * @param  [type] $fecha2 [description]
   * @return [type]         [description]
   */
  public function getDataRVentas($fecha1=null, $fecha2=null, $desglosado=null){
    $response = array('info' => array(), 'titulo3' => '');

    $response['titulo1'] = $this->config->item('empresa_nombre');
    $response['logo'] = $this->config->item('empresa_url_logo');

    if ($fecha1==null && $fecha2==null) {
      if (empty($_GET['ffecha1']) && empty($_GET['ffecha2'])){
        $_GET['ffecha1'] = date("Y-m").'-01';
        $_GET['ffecha2'] = date("Y-m-d");
      }
    }else{
      $_GET['ffecha1'] = $fecha1;
      $_GET['ffecha2'] = $fecha2;
    }
    if ( empty($_GET['fdesglosado']) ){
      $_GET['fdesglosado'] = $desglosado==null? false: $desglosado;
    }

    $sql = '';
    if (!empty($_GET['ffecha1']) && !empty($_GET['ffecha2'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al ".$_GET['ffecha2']."";
      $sql = " AND Date(fecha) BETWEEN '".$_GET['ffecha1']."' AND '".$_GET['ffecha2']."' ";
    }elseif (!empty($_GET['ffecha1'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al dia de Hoy";
      $sql = " AND Date(fecha) >= '".$_GET['ffecha1']."' ";
    }elseif (!empty($_GET['ffecha2'])){
      $response['titulo3'] = "Hasta ".$_GET['ffecha2'];
      $sql = " AND Date(fecha) <= '".$_GET['ffecha2']."' ";
    }

    if(isset($_GET['did_vendedor']{0})){
      $sql .= " AND u.id = ".$_GET['did_vendedor'];
    }

     $result = $this->db->query("SELECT 
          t.id, t.folio, Date(t.fecha) AS fecha, t.total, u.nombre AS vendedor, 
          (SELECT Sum(importe) FROM productos_base_salidas WHERE ticket_id = t.id) AS compra
        FROM tickets AS t INNER JOIN usuarios AS u ON t.usuario_id = u.id
        WHERE t.status = 1 ".$sql."
        ORDER BY t.fecha DESC, t.folio DESC
        ");
      if($result->num_rows() > 0){
        $response['info'] = $result->result();

        if ($_GET['fdesglosado'] == 'si'){
          foreach ($response['info'] as $key => $value) {
            $result = $this->db->query("SELECT 
                pf.id, pf.nombre, td.cantidad, td.precio_venta, td.importe, 
                (SELECT Sum(importe) FROM productos_base_salidas WHERE ticket_id = td.ticket_id AND familia_id = td.familia_id) AS compra
              FROM tickets_detalle AS td INNER JOIN productos_familias AS pf ON td.familia_id = pf.id
              WHERE td.ticket_id = ".$value->id."
              ");
            
            $response['info'][$key]->familias = $result->result();
            $result->free_result();
          }
        }

      }
    return $response;
   }
   /**
    * Genera el reporte en pdf reporte bajos de inventario
    */
   public function RVentas()
   {
      $this->load->library('mypdf');
      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('L', 'mm', 'Letter');
      $pdf->show_head = true;
      $pdf->titulo2 = 'Reporte de Ventas';
      
      $data = $this->getDataRVentas();
      $pdf->titulo1 = $data['titulo1'];
      $pdf->titulo3 = $data['titulo3'];
      $pdf->logo    = $data['logo'];

      $pdf->AliasNbPages();
      $links = array('', '', '', '');
      $pdf->SetY(30);
      $aligns = array('L', 'C', 'C', 'C', 'C', 'C');
      $widths = array(38, 38, 76, 38, 38, 38);
      $header = array('Folio', 'Fecha', 'Vendedor', 'Total', 'Compra', 'Utilidad');
      $total = 0;
      $total_compra = 0;

      foreach($data['info'] as $key => $item)
      {
        $band_head = false;
        if($pdf->GetY() >= $pdf->limiteY || $key==0) //salta de pagina si exede el max
        {
          $this->headerRVentas($pdf, $aligns, $widths, $header);
        }

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);

        $datos    = array(
          $item->folio, 
          $item->fecha, 
          $item->vendedor, 
          String::formatoNumero( $item->total, 2, ''),
          String::formatoNumero( $item->compra, 2, ''),
          String::formatoNumero( ($item->total-$item->compra), 2, ''),
        );
        $total += $item->total;
        $total_compra += $item->compra;

        $pdf->SetFillColor(255,255,255);
        if ($_GET['fdesglosado'] == 'si')
          $pdf->SetFillColor(226,226,226);
        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, true, true);

        if ($_GET['fdesglosado'] == 'si'){
          foreach ($item->familias as $key2 => $value) {
            $pdf->SetX(6);
            $pdf->Row(array(
                $value->nombre,
                $value->cantidad, 
                String::formatoNumero($value->precio_venta), 
                String::formatoNumero($value->importe),
                String::formatoNumero($value->compra),
                String::formatoNumero( ($value->importe-$value->compra), 2, ''),
              ), false, true);
            if($pdf->GetY() >= $pdf->limiteY) //salta de pagina si exede el max
            {
              $this->headerRVentas($pdf, $aligns, $widths, $header);
            }
          }
        }

      }

      $pdf->SetFillColor(160,160,160);
      $pdf->SetX(6);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetTextColor(255,255,255);
      $pdf->Row(array('', '', 'Total', String::formatoNumero($total), String::formatoNumero($total_compra), String::formatoNumero($total-$total_compra) ), true);

      $pdf->Output('reporte_ventas.pdf', 'I');
  }

  private function headerRVentas(&$pdf, &$aligns, &$widths, &$header){
    $pdf->AddPage();

    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFillColor(160,160,160);
    $pdf->SetX(6);
    $pdf->SetAligns($aligns);
    $pdf->SetWidths($widths);
    $pdf->Row($header, true);
  }



  /**
   * Reporte ventas por producto
   * @param  [type] $fecha1 [description]
   * @param  [type] $fecha2 [description]
   * @return [type]         [description]
   */
  public function getDataRVentasProductos($fecha1=null, $fecha2=null, $desglosado=null){
    $response = array('info' => array(), 'titulo3' => '');

    $response['titulo1'] = $this->config->item('empresa_nombre');
    $response['logo']    = $this->config->item('empresa_url_logo');

    if ($fecha1==null && $fecha2==null) {
      if (empty($_GET['ffecha1']) && empty($_GET['ffecha2'])){
        $_GET['ffecha1'] = date("Y-m").'-01';
        $_GET['ffecha2'] = date("Y-m-d");
      }
    }else{
      $_GET['ffecha1'] = $fecha1;
      $_GET['ffecha2'] = $fecha2;
    }

    $sql = '';
    if (!empty($_GET['ffecha1']) && !empty($_GET['ffecha2'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al ".$_GET['ffecha2']."";
      $sql = " AND Date(fecha) BETWEEN '".$_GET['ffecha1']."' AND '".$_GET['ffecha2']."' ";
    }elseif (!empty($_GET['ffecha1'])){
      $response['titulo3'] = "Del ".$_GET['ffecha1']." al dia de Hoy";
      $sql = " AND Date(fecha) >= '".$_GET['ffecha1']."' ";
    }elseif (!empty($_GET['ffecha2'])){
      $response['titulo3'] = "Hasta ".$_GET['ffecha2'];
      $sql = " AND Date(fecha) <= '".$_GET['ffecha2']."' ";
    }

     $result = $this->db->query("SELECT 
          id, codigo_barra, nombre, Sum(cantidad) AS cantidad, Sum(importe) AS importe
        FROM rpt_ventas_productos
        WHERE 1 ".$sql."
        GROUP BY id 
        ORDER BY nombre ASC
        ");
      if($result->num_rows() > 0){
        $response['info'] = $result->result();
      }
    return $response;
   }
   /**
    * Genera el reporte en pdf reporte bajos de inventario
    */
   public function RVentasProductos()
   {
      $this->load->library('mypdf');
      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('L', 'mm', 'Letter');
      $pdf->show_head = true;
      $pdf->titulo2 = 'Reporte Ventas de Productos';
      
      $data = $this->getDataRVentasProductos();
      $pdf->titulo1 = $data['titulo1'];
      $pdf->titulo3 = $data['titulo3'];
      $pdf->logo    = $data['logo'];

      $pdf->AliasNbPages();
      $links = array('', '', '', '');
      $pdf->SetY(30);
      $aligns = array('C', 'C', 'C', 'C');
      $widths = array(56, 108, 46, 46);
      $header = array('Cod. Barra', 'Nombre', 'Unidades', 'Importe');
      $total_unidad = $total_importe = 0;

      foreach($data['info'] as $key => $item)
      {
        $band_head = false;
        if($pdf->GetY() >= $pdf->limiteY || $key==0) //salta de pagina si exede el max
        {
          $this->headerRVentasProductos($pdf, $aligns, $widths, $header);
        }

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);

        $datos    = array(
          $item->codigo_barra, 
          $item->nombre, 
          String::formatoNumero( $item->cantidad, 2, ''),
          String::formatoNumero( $item->importe, 2, ''),
        );
        $total_importe += $item->importe;

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false, true);
      }

      $pdf->SetFillColor(160,160,160);
      $pdf->SetX(6);
      $pdf->SetFont('Arial','B',8);
      $pdf->SetTextColor(255,255,255);
      $pdf->Row(array('', '', 'Total', String::formatoNumero($total_importe) ), true);

      $pdf->Output('reporte_ventas.pdf', 'I');
  }

  private function headerRVentasProductos(&$pdf, &$aligns, &$widths, &$header){
    $pdf->AddPage();

    $pdf->SetFont('Arial','B',8);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFillColor(160,160,160);
    $pdf->SetX(6);
    $pdf->SetAligns($aligns);
    $pdf->SetWidths($widths);
    $pdf->Row($header, true);
  }




  public function productos_vendidos($fecha1=null, $fecha2=null, $tipo='asc'){
    $response = array('info' => array(), 'titulo3' => '');
    if ($fecha1==null && $fecha2==null){
      $fecha1 = date("Y-m").'-01';
      $fecha2 = date("Y-m-d");
    }

    $sql = '';
    if (!empty($fecha1) && !empty($fecha2)){
      $response['titulo3'] = "Del ".$fecha1." al ".$fecha2."";
      $sql = " AND Date(t.fecha) BETWEEN '".$fecha1."' AND '".$fecha2."' ";
    }elseif (!empty($fecha1)){
      $response['titulo3'] = "Del ".$fecha1." al dia de Hoy";
      $sql = " AND Date(t.fecha) >= '".$fecha1."' ";
    }elseif (!empty($fecha2)){
      $response['titulo3'] = "Hasta ".$fecha2;
      $sql = " AND Date(t.fecha) <= '".$fecha2."' ";
    }

    $result = $this->db->query("SELECT pf.id, pf.nombre, Sum(td.cantidad) AS cantidad, Sum(td.importe) AS importe
                               FROM tickets AS t INNER JOIN tickets_detalle AS td ON td.ticket_id = t.id
                                INNER JOIN productos_familias AS pf ON td.familia_id = pf.id
                               WHERE t.status = 1 ".$sql."
                               GROUP BY pf.id ORDER BY ".$tipo
                               );
    if($result->num_rows() > 0){
      $response['info'] = $result->result();
    }

    return $response;
  }


  /**
   * Reporte bajos de inventario, obtiene los datos para generar el reporte
   * @param  [type] $fecha1 [description]
   * @param  [type] $fecha2 [description]
   * @return [type]         [description]
   */
  public function getDataRFamiliasDescripcion(){
    $response = array('info' => array(), 'titulo3' => '');

    $response['titulo1'] = $this->config->item('empresa_nombre');
    $response['logo']    = $this->config->item('empresa_url_logo');

    $sql = '';

    if(isset($_GET['ddescripcion']{0})){
      $sql = " AND pb.descripcion LIKE '%".$_GET['ddescripcion']."%'";
    }

     $result = $this->db->query("SELECT 
          pf.id, pf.nombre, pf.precio_venta, pbe.existencia, pb.descripcion, Count(pf.id) AS num 
      FROM productos_familias AS pf 
        INNER JOIN productos_base_familia AS pbf ON pbf.familia_id = pf.id
        INNER JOIN productos_base AS pb ON pb.id = pbf.base_id
        INNER JOIN productos_base_existencias AS pbe ON pbe.id = pb.id
      WHERE pf.id <> 1 ".$sql."
      GROUP BY pf.id 
      HAVING num = 1
      ORDER BY pf.nombre ASC
      ");
     if($result->num_rows() > 0)
      $response['info'] = $result->result();

    return $response;
   }
   /**
    * Genera el reporte en pdf reporte bajos de inventario
    */
   public function RFamiliasDescripcion()
   {
      $this->load->library('mypdf');
      // Creación del objeto de la clase heredada
      $pdf = new MYpdf('L', 'mm', 'Letter');
      $pdf->show_head = true;
      $pdf->titulo2 = 'Listado de familias';
      
      $data = $this->getDataRFamiliasDescripcion();
      $pdf->titulo1 = $data['titulo1'];
      $pdf->titulo3 = $data['titulo3'];
      $pdf->logo    = $data['logo'];

      $pdf->AliasNbPages();
      // $links = array('', '', '', '');
      $pdf->SetY(30);
      $aligns = array('L', 'C', 'C', 'L');
      $widths = array(90, 25, 25, 130);
      $header = array('Nombre', 'Existencia', 'Precio venta', 'Descripcion');

      foreach($data['info'] as $key => $item)
      {
        $band_head = false;
        if($pdf->GetY() >= $pdf->limiteY || $key==0) //salta de pagina si exede el max
        {
          $pdf->AddPage();

          $pdf->SetFont('Arial','B',8);
          $pdf->SetTextColor(255,255,255);
          $pdf->SetFillColor(160,160,160);
          $pdf->SetX(6);
          $pdf->SetAligns($aligns);
          $pdf->SetWidths($widths);
          $pdf->Row($header, true);
        }

        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);

        $datos    = array(
          $item->nombre, 
          String::formatoNumero($item->existencia, 0, ''), 
          String::formatoNumero($item->precio_venta, 2, ''), 
          $item->descripcion, 
        );

        $pdf->SetX(6);
        $pdf->SetAligns($aligns);
        $pdf->SetWidths($widths);
        $pdf->Row($datos, false, true);
      }

      $pdf->Output('reporte_familias_descrip.pdf', 'I');
  }

}