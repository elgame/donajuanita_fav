		<div id="content" class="span10">
			<!-- content starts -->


			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/familias'); ?>">Familias</a> <span class="divider">/</span>
					</li>
					<li>Agregar</li>
				</ul>
			</div>

			<div class="row-fluid">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> Agregar Familia</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form action="<?php echo base_url('panel/familias/agregar'); ?>" method="post" class="form-horizontal" id="form" 
							enctype="multipart/form-data">
						  <fieldset>
								<legend></legend>

								<div class="span7">
	                <div class="control-group">
	                  <label class="control-label" for="dnombre">*Nombre </label>
	                  <div class="controls">
											<input type="text" name="dnombre" id="dnombre" value="<?php echo set_value('dnombre'); ?>" 
												class="input-xlarge" placeholder="Leche, Cafe, Cucharas" autofocus>
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="dprecio_venta">Precio venta </label>
	                  <div class="controls">
	                    <input type="text" name="dprecio_venta" id="dprecio_venta" value="<?php echo set_value('dprecio_venta'); ?>"
	                    	class="input-large vpositive" placeholder="30, 20, 3 (pesos)">
	                  </div>
	                </div>
	                
	                <div class="control-group">
	                  <label class="control-label" for="dcodigo">Codigo de Barras </label>
	                  <div class="controls">
											<input type="text" name="dcodigo" id="dcodigo" value="<?php echo set_value('dcodigo'); ?>" 
												class="input-xlarge" placeholder="736HS212, 828102, 2232123">
	                  </div>
	                </div>

	                <div class="control-group">
										<label for="dimagen" class="control-label">Imagen</label>
										<div class="controls">
											<input type="file" name="dimagen" id="dimagen" value="<?php echo set_value('dimagen') ?>" size="30">
										</div>
									</div>

									<div class="control-group">
										<label for="dcolor" class="control-label">Color</label>
										<div class="controls">
											<input type="text" name="dcolor" id="dcolor" class="pull-left" value="<?php echo set_value('dcolor', '#ffffff') ?>" size="30" maxlength="7">
											<label for="dcolor" class="span2">Color plano <input type="checkbox" name="dcolor_plano" value="si" <?php echo set_checkbox('dcolor_plano', 'si'); ?>></label>
											<div class="clearfix"></div>
											<div id="colorpicker"></div>
										</div>
									</div>
								</div> <!--/span -->

								<div class="span4">
	                <div class="control-group">
	                  <label class="control-label" style="width: 100px;">Familia Padre </label>
	                  <div class="controls" style="margin-left: 120px;">
	                  	<div style="height: 300px; overflow: auto; border:1px #ddd solid;">
	                  		<?php echo $this->familias_model->getFrmFamilias(0, true, 'radio', true); ?>
	                    </div>
	                  </div>
	                </div>
	              </div> <!--/span-->

	              <div class="clearfix"></div>

	              <div class="row-fluid products_cosns">
	              	<fieldset class="span6">
										<legend style="margin-bottom: 0px;">Productos registrados</legend>
										<span style="display:block; text-align:center; margin: 4px 0;">
											<input type="search" id="buscar_pr" class="b-all corner-all" size="30" placeholder="Buscar...">
										</span>
										<table class="table table-condensed" style="margin-bottom: 0px;">
											<thead>
												<tr>
													<th>Nombre</th>
												</tr>
											</thead>
										</table>
										<div id="tbl_productos_r" style="min-height: 260px;">
										<?php 
										if(isset($tabla_productos_r))
											echo $tabla_productos_r;
										?>
										</div>
									</fieldset>
									
									<fieldset class="span6 products_cosns">
										<legend>Productos que consume</legend>
										<table class="table table-condensed" style="margin-bottom: 0px;">
											<thead>
												<tr>
													<th style="width:66%;">Nombre</th>
													<th style="width:33%;">Cantidad</th>
												</tr>
											</thead>
										</table>
										<div id="" style="height: 260px;overflow-y:auto;">
											<table id="tbl-pl" class="table table-condensed table-addproducf">
												<tbody>
											<?php 
											if(is_array($this->input->post('dpcids'))){
												foreach($this->input->post('dpcids') as $key => $id){ ?>
												<tr id="tr-pl<?php echo $id; ?>" class="tr-produclista">
													<td style="width:66%;"><?php echo $_POST['dpcnombres'][$key]; ?>
														<input type="hidden" name="dpcnombres[]" value="<?php echo $_POST['dpcnombres'][$key]; ?>">
														<input type="hidden" name="dpcids[]" value="<?php echo $id; ?>"></td>
													<td style="width:33%;">
														<input type="text" name="dpccantidad[]" value="<?php echo $_POST['dpccantidad'][$key]; ?>" class="span12"></td>
												</tr>
											<?php } 
											} ?>
												</tbody>
											</table>
										</div>
									</fieldset>
									<a class="btn btnaddpro">>></a>
									<a class="btn btnquitpro"><<</a>
	              </div>

								<div class="form-actions">
								  <button type="submit" class="btn btn-primary">Guardar</button>
								</div>
						  </fieldset>
						</form>

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

