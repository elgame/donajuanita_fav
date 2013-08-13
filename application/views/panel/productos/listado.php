
		<div id="content" class="span10">
			<!-- content starts -->
			

			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						Productos base
					</li>
				</ul>
			</div>

			<div class="row-fluid">		
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-book"></i> Productos base</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form action="<?php echo base_url('panel/productos/'); ?>" method="get" class="form-search">
							<fieldset>
								<legend>Filtros</legend>

								<label for="fnombre">buscar:</label> 
								<input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>" class="input-large" 
									placeholder="Modificar, usuarios/agregar" autofocus> | 

								<label for="fstatus">Estado</label>
								<select name="fstatus">
									<option value="1" <?php echo set_select('fstatus', '1', false, $this->input->get('fstatus')); ?>>ACTIVOS</option>
									<option value="0" <?php echo set_select('fstatus', '0', false, $this->input->get('fstatus')); ?>>ELIMINADOS</option>
									<option value="todos" <?php echo set_select('fstatus', 'todos', false, $this->input->get('fstatus')); ?>>TODOS</option>
								</select>
								
								<button class="btn">Buscar</button>
							</fieldset>
						</form>

						<?php 
						echo $this->usuarios_model->getLinkPrivSm('productos/agregar/', array(
										'params'   => '',
										'btn_type' => 'btn-success pull-right',
										'attrs' => array('style' => 'margin: 0px 0 10px 10px;') )
								);
						 ?>
						<table class="table table-striped table-bordered bootstrap-datatable">
						  <thead>
							  <tr>
								  <th>Nombre</th>
								  <th>Stock Minimo</th>
								  <th>Precio compra</th>
								  <th>Existencia</th>
								  <th>Status</th>
								  <th>Opciones</th>
							  </tr>
						  </thead>   
						  <tbody>
						<?php foreach($productos['productos'] as $producto){ ?>
								<tr>
									<td><?php echo $producto->nombre?></td>
									<td><?php echo $producto->stock_min; ?></td>
									<td><?php echo String::formatoNumero($producto->precio_compra); ?></td>
									<td><?php echo String::formatoNumero($producto->existencia, 0, ''); ?></td>
									<td>
										<?php
											if($producto->status == 1){
												$v_status    = 'Activo';
												$vlbl_status = 'label-success';
											}else{
												$v_status    = 'Eliminado';
												$vlbl_status = 'label-important';
											}
										?>
										<span class="label <?php echo $vlbl_status; ?>"><?php echo $v_status; ?></span>
									</td>
									<td class="center">
										<?php 
										echo $this->usuarios_model->getLinkPrivSm('productos/modificar/', array(
												'params'   => 'id='.$producto->id,
												'btn_type' => 'btn-success')
										);
										if ($producto->status == 1) {
											echo $this->usuarios_model->getLinkPrivSm('productos/eliminar/', array(
													'params'   => 'id='.$producto->id,
													'btn_type' => 'btn-danger',
													'attrs' => array('onclick' => "msb.confirm('Estas seguro de eliminar el producto?', 'Productos', this); return false;"))
											);
										}else{
											echo $this->usuarios_model->getLinkPrivSm('productos/activar/', array(
													'params'   => 'id='.$producto->id,
													'btn_type' => 'btn-danger',
													'attrs' => array('onclick' => "msb.confirm('Estas seguro de activar el producto?', 'Usuarios', this); return false;"))
											);
										}
										echo $this->usuarios_model->getLinkPrivSm('productos/agregar_inventario/', array(
												'params'   => 'id='.$producto->id,
												'btn_type' => 'btn-info',
												'attrs'    => array(
													'rel'   => 'superbox-50x400'
													)
												)
										);
										?>
									</td>
							</tr>
					<?php }?>
						  </tbody>
					  </table>

					  <?php
						//Paginacion
						$this->pagination->initialize(array(
								'base_url' 			=> base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
								'total_rows'		=> $productos['total_rows'],
								'per_page'			=> $productos['items_per_page'],
								'cur_page'			=> $productos['result_page']*$productos['items_per_page'],
								'page_query_string'	=> TRUE,
								'num_links'			=> 1,
								'anchor_class'	=> 'pags corner-all',
								'num_tag_open' 	=> '<li>',
								'num_tag_close' => '</li>',
								'cur_tag_open'	=> '<li class="active"><a href="#">',
								'cur_tag_close' => '</a></li>'
						));
						$pagination = $this->pagination->create_links();
						echo '<div class="pagination pagination-centered"><ul>'.$pagination.'</ul></div>'; 
						?>
					</div>
				</div><!--/span-->
			
			</div><!--/row-->
				  

					<!-- content ends -->
		</div><!--/#content.span10-->


<!-- Bloque de alertas -->
<?php if(isset($frm_errors)){
	if($frm_errors['msg'] != ''){ 
?>
<script type="text/javascript" charset="UTF-8">
	$(document).ready(function(){
		noty({"text":"<?php echo $frm_errors['msg']; ?>", "layout":"topRight", "type":"<?php echo $frm_errors['ico']; ?>"});
	});
</script>
<?php }
}?>
<!-- Bloque de alertas -->


