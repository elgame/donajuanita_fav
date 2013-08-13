<?php
class productos_model extends CI_Model{
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
	 * Obtiene el listado de todos los productos base paginados
	 */
	public function obtenProductosBase($per_pag='40', $sqlp=''){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => $per_pag,
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		
		//Filtros para buscar
		if($this->input->get('fnombre') != '')
			$sql = "WHERE ( lower(pb.nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(pb.stock_min) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
				lower(pb.descripcion) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' )";

		$fstatus = $this->input->get('fstatus')===false? '1': $this->input->get('fstatus');
		if($fstatus != '' && $fstatus != 'todos')
			$sql .= ($sql==''? 'WHERE': ' AND')." pb.status = '".$fstatus."'";

		$sql .= ($sqlp!=''? ($sql==''? 'WHERE': ' AND').$sqlp: '');
		
		$query = BDUtil::pagination("
			SELECT pb.id, pb.nombre, pb.stock_min, pb.status, pe.existencia, pe.precio_compra
			FROM productos_base AS pb INNER JOIN productos_base_existencias AS pe ON pe.id = pb.id
			".$sql."
			ORDER BY pb.nombre ASC
		", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'productos' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['productos'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene toda la informacion de un producto
	 * @param unknown_type $id
	 */
	public function getInfoProducto($id){
		$res = $this->db
			->select('pb.id, pb.proveedor_id, pb.nombre, pb.stock_min, pbe.precio_compra, pb.marca, pb.status, 
				pb.descripcion, p.nombre_fiscal')
			->from('productos_base AS pb')
				->join('productos_base_existencias AS pbe', 'pbe.id = pb.id', 'inner')
				->join('proveedores AS p', 'pb.proveedor_id = p.id', 'left')
			->where("pb.id = '".$id."'")
		->get();
		if($res->num_rows() > 0)
			return $res->row();
		else
			return false;
	}
	
	/**
	 * Modifica la informacion de un producto base
	 */
	public function updateProducto($id, $data=null){
		if ($data == null) {
			$data = array(
				'nombre'       => $this->input->post('dnombre'),
				'stock_min'    => $this->input->post('dstock_min'),
				'marca'        => $this->input->post('dmarca'),
				'proveedor_id' => ($this->input->post('did_proveedor')==''? NULL: $this->input->post('did_proveedor')),
				'descripcion'  => $this->input->post('ddescripcion'),
			);
		}
		$this->db->update('productos_base', $data, "id = '".$id."'");
		return array(true, '');
	}
	
	/**
	 * Agrega un producto base a la bd
	 */
	public function addProducto($data=null, $data_entrada=null){
		if ($data == null) {
			$data = array(
				'nombre'       => $this->input->post('dnombre'),
				'stock_min'    => $this->input->post('dstock_min'),
				'marca'        => $this->input->post('dmarca'),
				'proveedor_id' => ($this->input->post('did_proveedor')==''? NULL: $this->input->post('did_proveedor')),
				'descripcion'  => $this->input->post('ddescripcion'),
			);
		}
		if ($data_entrada == null) {
			$data_entrada = array(
				'base_id'       => '',
				'precio_compra' => $this->input->post('dprecio_compra'),
				'cantidad'      => $this->input->post('dcantidad'),
				'importe'       => ($this->input->post('dprecio_compra') * $this->input->post('dcantidad')),
			);
		}
		$this->db->insert('productos_base', $data);
		$id_product = $this->db->insert_id();

		$data_entrada['base_id'] = $id_product;
		$this->db->insert('productos_base_entradas', $data_entrada);

		if($this->input->post('dis_same_fam') == 'si') {
			$this->load->model('familias_model');

			$data_familia = array(
				'id_padre'     => $this->input->post('dfpadre'),
				'nombre'       => $this->input->post('dnombre'),
				'precio_venta' => $this->input->post('dprecio_venta'),
				'codigo_barra' => ($this->input->post('dcodigo')!=''? $this->input->post('dcodigo'): NULL),
				'imagen'       => '',
				'color1'       => '#ffffff',
				'color2'       => '#dddddd',
			);
			$data_cons = array(
				'ids'       => array($id_product),
				'cantidads' => array('1'),
			);
			$this->familias_model->addFamilia($data_familia, $data_cons);
		}

		return array(true, '');
	}
	


	public function addInventario($id, $data=null){
		if ($data == null) {
			$data = array(
				'base_id'       => $id,
				'cantidad'      => $this->input->post('dcantidad'),
				'precio_compra' => $this->input->post('dprecio_compra'),
				'importe'       => String::float($this->input->post('dcantidad') * $this->input->post('dprecio_compra')),
			);
		}
		$this->db->insert('productos_base_entradas', $data);
		return array(true, '');
	}

}