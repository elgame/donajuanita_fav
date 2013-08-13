<?php
class tickets_model extends CI_Model{
  /**
   * los url_accion q se asignen seran excluidos de la validacion y la funcion
   * tienePrivilegioDe regresara un true como si el usuario si tiene ese privilegio,
   * Esta enfocado para cuendo se utilice Ajax
   * @var unknown_type
   */
  public $excepcion_privilegio = array();


  function __construct(){
    parent::__construct();
  }

  /**
   * Obtiene el listado de todos los tickets
   */
  public function obten_tickets()
  {
    $sql = '';

    //paginacion
    $params = array(
        'result_items_per_page' => '40',
        'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
    );
    if($params['result_page'] % $params['result_items_per_page'] == 0)
      $params['result_page'] = ($params['result_page']/$params['result_items_per_page']);

    //Filtros para buscar
    if($this->input->get('fnombre') != '')
      $sql = " ( lower(u.usuario) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
                     lower(t.total) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
                     lower(t.folio) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%')";

    $fstatus = ($this->input->get('fstatus') === false) ? '1': $this->input->get('fstatus');
    if($fstatus != '' && $fstatus != 'todos')
      $sql .= (($sql !== '') ? ' AND ' : '') .  " t.status = '".$fstatus."'";

    $query = BDUtil::pagination("
      SELECT t.id, t.folio, t.fecha, t.total, t.status, u.usuario
      FROM tickets AS t
      INNER JOIN usuarios AS u ON u.id = t.usuario_id
      ".(($sql !== '') ? 'WHERE '.$sql : '')."
      ORDER BY t.fecha DESC
    ", $params, true);
    $res = $this->db->query($query['query']);

    $response = array(
        'tickets' => array(),
        'total_rows'    => $query['total_rows'],
        'items_per_page'  => $params['result_items_per_page'],
        'result_page'     => $params['result_page']
    );
    if($res->num_rows() > 0)
      $response['tickets'] = $res->result();

    return $response;
  }

  /**
   * Cancela un ticket
   */
  public function cancel_ticket($id = false)
  {
    $id = ($id !== false) ? $id : $this->input->get('id');
    $this->db->update('tickets', array('status'=>'0'), array('id'=>$id));
    return array(true, '');
  }

  /**
   * Obtiene la informacion basica o full(con los productos de la compra) del
   * ticket
   *
   * @param  boolean $extraInfo
   * @param  int  $id
   * @return array
   */
  public function get_info_ticket($extraInfo = false, $id = null)
  {
    $id = ($id !== null) ? $id : $this->input->post('id');

    $select = $this->db->select('*')
                        ->from('tickets')
                        ->where('id', $id)
                        ->get();

    $data = array();
    if ($select->num_rows() > 0) $data['info'] = $select->row();

    if ($extraInfo)
    {
      $select->free_result();
      $select = $this->db->select('pf.nombre, td.cantidad, td.descuento')
                         ->from('tickets_detalle as td')
                         ->join('productos_familias as pf', 'pf.id = td.familia_id', 'inner')
                         ->where('td.ticket_id', $id)
                         ->order_by('nombre', 'ASC')
                         ->get();

      if ($select->num_rows() > 0) $data['items'] = $select->result();
    }

    return $data;
  }

}