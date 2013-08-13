
		<div id="content" class="span10">
			<!-- content starts -->
			

			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						Familias
					</li>
				</ul>
			</div>

			<div class="row-fluid">		
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-book"></i> Familias</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form action="<?php echo base_url('panel/familias/'); ?>" method="get" class="form-search">
							<fieldset>
								<legend>Filtros</legend>

								<label for="fnombre">buscar:</label> 
								<input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>" class="input-large" 
									placeholder="Cafe negro, Torta, 40, 828102" autofocus> | 

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
						echo $this->usuarios_model->getLinkPrivSm('familias/agregar/', array(
										'params'   => '',
										'btn_type' => 'btn-success pull-right',
										'attrs' => array('style' => 'margin: 0px 0 10px 10px;') )
								);
						 ?>
						<table class="table table-striped table-bordered bootstrap-datatable">
						  <thead>
							  <tr>
							  	<th>Imagen</th>
								  <th>Nombre</th>
								  <th>Precio venta</th>
								  <th>Color</th>
								  <th>C. de Barra</th>
								  <th>Status</th>
								  <th>Opciones</th>
							  </tr>
						  </thead>   
						  <tbody>
						<?php foreach($familias['familias'] as $familia){ ?>
								<tr>
									<td><?php echo ($familia->imagen!=''? '<img src="'.base_url('application/images/familias/'.$familia->imagen).'" class="center" width="50" height="50">': '')?></td>
									<td><?php echo $familia->nombre?></td>
									<td><?php echo String::formatoNumero($familia->precio_venta); ?></td>
									<td><span class="center" style="display:block; border:1px #ccc solid; width:20px; height:20px; background-color: <?php echo $familia->color1; ?>"></span></td>
									<td><?php echo $familia->codigo_barra; ?></td>
									<td>
										<?php
											if($familia->status == 1){
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
										echo $this->usuarios_model->getLinkPrivSm('familias/modificar/', array(
												'params'   => 'id='.$familia->id,
												'btn_type' => 'btn-success')
										);
										if ($familia->status == 1) {
											echo $this->usuarios_model->getLinkPrivSm('familias/eliminar/', array(
													'params'   => 'id='.$familia->id,
													'btn_type' => 'btn-danger',
													'attrs' => array('onclick' => "msb.confirm('Estas seguro de eliminar la familia?', 'Familias', this); return false;"))
											);
										}else{
											echo $this->usuarios_model->getLinkPrivSm('familias/activar/', array(
													'params'   => 'id='.$familia->id,
													'btn_type' => 'btn-danger',
													'attrs' => array('onclick' => "msb.confirm('Estas seguro de activar la familia?', 'Familias', this); return false;"))
											);
										}
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
								'total_rows'		=> $familias['total_rows'],
								'per_page'			=> $familias['items_per_page'],
								'cur_page'			=> $familias['result_page']*$familias['items_per_page'],
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


