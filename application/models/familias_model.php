<?php
class familias_model extends CI_Model{
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
	public function obtenFamilias(){
		$sql = '';
		//paginacion
		$params = array(
				'result_items_per_page' => '40',
				'result_page' => (isset($_GET['pag'])? $_GET['pag']: 0)
		);
		if($params['result_page'] % $params['result_items_per_page'] == 0)
			$params['result_page'] = ($params['result_page']/$params['result_items_per_page']);
		
		//Filtros para buscar
		if($this->input->get('fnombre') != ''){
			//buscar en descripcion de productos base
			$filt_sql = '';
			$palabras = explode(' ', mb_strtolower($this->input->get('fnombre'), 'UTF-8'));
			foreach ($palabras as $key => $value) {
				$filt_sql .= "AND lower(pb.descripcion) LIKE '%".$value."%' ";
			}
			$filt_sql = substr($filt_sql, 3);

			$sql = " AND ( lower(pf.nombre) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(pf.precio_venta) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR 
				lower(pf.codigo_barra) LIKE '%".mb_strtolower($this->input->get('fnombre'), 'UTF-8')."%' OR
				(".$filt_sql.") )";
		}

		$fstatus = $this->input->get('fstatus')===false? '1': $this->input->get('fstatus');
		if($fstatus != '' && $fstatus != 'todos')
			$sql .= " AND pf.status = '".$fstatus."'";
		
		$query = BDUtil::pagination("
			SELECT pf.id, pf.id_padre, pf.nombre, pf.precio_venta, pf.codigo_barra, pf.imagen, pf.color1, pf.color2, pf.ultimo_nodo, pf.status
			FROM productos_familias AS pf 
				LEFT JOIN productos_base_familia AS pbf ON pf.id = pbf.familia_id
				LEFT JOIN productos_base AS pb ON pb.id = pbf.base_id
			WHERE pf.id <> 1 ".$sql."
			GROUP BY pf.id 
			ORDER BY pf.nombre ASC
		", $params, true);
		$res = $this->db->query($query['query']);
		
		$response = array(
				'familias' => array(),
				'total_rows' 		=> $query['total_rows'],
				'items_per_page' 	=> $params['result_items_per_page'],
				'result_page' 		=> $params['result_page']
		);
		if($res->num_rows() > 0)
			$response['familias'] = $res->result();
		
		return $response;
	}
	
	/**
	 * Obtiene toda la informacion de un producto
	 * @param unknown_type $id
	 */
	public function getInfoFamilia($id, $basic=false){
		$res = $this->db
			->select('*')
			->from('productos_familias')
			->where("id = '".$id."'")
		->get();
		if($res->num_rows() > 0){
			$response['info'] = $res->row();
			$res->free_result();
			if ($basic)
				return $response;

			$res = $this->db
				->select('pbf.familia_id, pbf.base_id, pb.nombre, pbf.cantidad')
				->from('productos_base_familia AS pbf')
					->join('productos_base AS pb', 'pbf.base_id = pb.id', 'inner')
				->where("pbf.familia_id = '".$id."'")->get();
			$response['consumos'] = $res->result();

			$res->free_result();
			return $response;
		}else
			return false;
	}
	
	/**
	 * Modifica la informacion de un familia
	 */
	public function updateFamilia($id, $data=null, $data_cons=null, $update_nodo=true){
		if ($data == null) {
			$path_img = '';
			//valida la imagen
			$upload_res = UploadFiles::uploadImgFamilia();
			if(is_array($upload_res)){
				if($upload_res[0] == false)
					return array(false, $upload_res[1]);
				$path_img = $upload_res[1]['file_name'];
			}

			$color1 = $this->input->post('dcolor')!=''? $this->input->post('dcolor'): '#ffffff';
			$color2 = hexdec( str_replace('#', '', $this->input->post('dcolor')) );
			if($this->input->post('dcolor_plano')!='si')
				$color2 += 90;

			$data = array(
				'id_padre'     => $this->input->post('dfpadre'),
				'nombre'       => $this->input->post('dnombre'),
				'precio_venta' => $this->input->post('dprecio_venta'),
				'codigo_barra' => ($this->input->post('dcodigo')!=''? $this->input->post('dcodigo'): NULL),
				'color1'       => $color1,
				'color2'       => '#'.dechex($color2),
				// 'ultimo_nodo' => 
			);
			if ($path_img!='')
				$data['imagen'] = $path_img;
		}

		if ($data_cons == null) { //consumos
			$data_cons = array(
				'ids'       => $this->input->post('dpcids'),
				'cantidads' => $this->input->post('dpccantidad'),
			);
		}

		if ($update_nodo) //para verificar el ultimo nodo del padre
			$datos_fam = $this->getInfoFamilia($id, true);

		$this->db->update('productos_familias', $data, "id = '".$id."'");

		//Productos que consume
		if (is_array($data_cons['ids'])) {
			$this->db->delete('productos_base_familia', "familia_id = '".$id."'");
			$data_consumos = array();
			foreach ($data_cons['ids'] as $key => $idp) {
				$data_consumos[] = array(
					'familia_id' => $id,
					'base_id'    => $idp,
					'cantidad'   => $data_cons['cantidads'][$key]
				);
			}
			if (count($data_consumos) > 0){
				$this->db->insert_batch('productos_base_familia', $data_consumos);

				//si porducto consumo es 1 y se quiere actualizar el producto base
				if(count($data_consumos) == 1 && $this->input->post('dactualiza_base') === 'si'){
					$this->load->model('productos_model');
					//se actualiza el producto base y el nuevo precio
					$this->productos_model->updateProducto($data_consumos[0]['base_id'], array('nombre' => $this->input->post('dnombre')));
					$this->productos_model->addInventario($data_consumos[0]['base_id'], 
							array(
								'base_id'       => $data_consumos[0]['base_id'],
								'cantidad'      => '0',
								'precio_compra' => $this->input->post('dprecio_compra'),
								'importe'       => '0')
						);
				}
			}
		}

		//Actualizar el ultimo nodo del padre anterior y de si mismo
		if ($update_nodo){
			$result = $this->db->query("SELECT valida_ultimo_nodo(".$datos_fam['info']->id_padre.") AS padre_old"
				.(isset($data['id_padre'])? ",valida_ultimo_nodo(".$data['id_padre'].") AS padre_new": '') )->row();
			$this->db->update('productos_familias', array('ultimo_nodo' => $result->padre_old), 'id = '.$datos_fam['info']->id_padre);
			if (isset($data['id_padre']))
				$this->db->update('productos_familias', array('ultimo_nodo' => $result->padre_new), 'id = '.$data['id_padre']);
		}

		return array(true, '');
	}
	
	/**
	 * Agrega un familia a la bd
	 */
	public function addFamilia($data=null, $data_cons=null){
		if ($data == null) {
			$path_img = '';
			//valida la imagen
			$upload_res = UploadFiles::uploadImgFamilia();

			if(is_array($upload_res)){
				if($upload_res[0] == false)
					return array(false, $upload_res[1]);
				$path_img = $upload_res[1]['file_name']; //APPPATH.'images/series_folios/'.$upload_res[1]['file_name'];
			}

			$color1 = $this->input->post('dcolor')!=''? $this->input->post('dcolor'): '#ffffff';
			$color2 = hexdec( str_replace('#', '', $this->input->post('dcolor')) );
			if($this->input->post('dcolor_plano')!='si')
				$color2 += 90;

			$data = array(
				'id_padre'     => $this->input->post('dfpadre'),
				'nombre'       => $this->input->post('dnombre'),
				'precio_venta' => $this->input->post('dprecio_venta'),
				'codigo_barra' => ($this->input->post('dcodigo')!=''? $this->input->post('dcodigo'): NULL),
				'imagen'       => $path_img,
				'color1'       => $color1,
				'color2'       => '#'.dechex($color2),
			);
		}

		if ($data_cons == null) {
			$data_cons = array(
				'ids'       => $this->input->post('dpcids'),
				'cantidads' => $this->input->post('dpccantidad'),
			);
		}

		$this->db->insert('productos_familias', $data);
		$id_familia = $this->db->insert_id();

		//Productos que consume
		$tiene_productos = false;
		if (is_array($data_cons['ids'])) {
			$data_consumos = array();
			foreach ($data_cons['ids'] as $key => $idp) {
				$data_consumos[] = array(
					'familia_id' => $id_familia,
					'base_id'    => $idp,
					'cantidad'   => $data_cons['cantidads'][$key]
				);
			}
			if (count($data_consumos) > 0){
				$this->db->insert_batch('productos_base_familia', $data_consumos);
				$tiene_productos = true;
			}
		}

		// //si no tiene productos base lo pone como si no fuera el ultimo nodo
		// if($tiene_productos==false)
		// 	$this->updateFamilia($id_familia, array('ultimo_nodo' => '0'), null, false);
		//ya no es ultimo nodo el padre
		$this->updateFamilia($data['id_padre'], array('ultimo_nodo' => '0'), null, false);
		return array(true, '');
	}
	


	public function getFrmFamilias($id_submenu=NULL, $firs=true, $tipo=null, $showp=false){
		$txt = "";
		$bande = true;
		
		$res = $this->db
			->select("p.id, p.nombre, p.id_padre, p.precio_venta, p.imagen, p.ultimo_nodo")
			->from('productos_familias AS p')
			->where("(p.id_padre = '".$id_submenu."'".($firs? ' OR p.id_padre IS NULL': '').") AND p.status = '1'")
			->order_by('p.nombre', 'asc')
		->get();
		$txt .= $firs? '<ul class="treeview">': '<ul>';
		foreach($res->result() as $data){
			// $res1 = $this->db
			// 	->select('Count(p.id) AS num')
			// 	->from('privilegios AS p')
			// 	->where("p.id_padre = '".$data->id."'")
			// ->get();
			// $data1 = $res1->row();
			
			if($tipo != null && !is_array($tipo)){
				$set_nombre = 'dfpadre';
				$set_val = set_radio($set_nombre, $data->id, ($tipo==$data->id? true: false));
				$tipo_obj = 'radio';
			}else{	
				$set_nombre = 'dfpadre[]';
				if(is_array($tipo))
					$set_val = set_checkbox($set_nombre, $data->id, 
							(array_search($data->id, $tipo)!==false? true: false) );
				else
					$set_val = set_checkbox($set_nombre, $data->id);
				$tipo_obj = 'checkbox';
			}
			
			// if($bande==true && $firs==true && $showp==true){
			// 	$txt .= '<li><label style="font-size:11px;">
			// 	<input type="'.$tipo_obj.'" name="'.$set_nombre.'" data-uniform="false" value="0" '.$set_val.($data->id_padre==0?  ' checked': '').'> Padre</label>
			// 	</li>';
			// 	$bande = false;
			// }
			
			if($data->ultimo_nodo == 0){
				$txt .= '<li><label style="font-size:11px;">
					<input type="'.$tipo_obj.'" name="'.$set_nombre.'" data-uniform="false" value="'.$data->id.'" '.$set_val.'> '.$data->nombre.'</label>
					'.$this->getFrmFamilias($data->id, false, $tipo).'
				</li>';
			}else{
				$txt .= '<li><label style="font-size:11px;">
					<input type="'.$tipo_obj.'" name="'.$set_nombre.'" data-uniform="false" value="'.$data->id.'" '.$set_val.'> '.$data->nombre.'</label>
				</li>';
			}
			// $res1->free_result();
		}
		$txt .= '</ul>';
		$res->free_result();
		
		return $txt;
	}


	public function validCodigo($id, $codigo){
		$data = $this->db->query("SELECT Count(*) AS t
		                           FROM productos_familias
		                           WHERE id <> ".$id." AND Lower(codigo_barra) = '".mb_strtolower($codigo)."'")->row();
		if ($data->t > 0) {
			return false;
		}
		return true;
	}

}