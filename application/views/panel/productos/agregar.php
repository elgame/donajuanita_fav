		<div id="content" class="span10">
			<!-- content starts -->


			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="<?php echo base_url('panel/productos'); ?>">Productos base</a> <span class="divider">/</span>
					</li>
					<li>Agregar</li>
				</ul>
			</div>

			<div class="row-fluid">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> Agregar Producto</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form action="<?php echo base_url('panel/productos/agregar'); ?>" method="post" class="form-horizontal" id="form">
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
	                  <label class="control-label" for="dstock_min">*Stock Min </label>
	                  <div class="controls">
	                    <input type="text" name="dstock_min" id="dstock_min" value="<?php echo set_value('dstock_min'); ?>" 
	                    	class="input-xlarge vpositive" placeholder="5, 1, 10 (unidades)">
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="dprecio_compra">*Precio compra </label>
	                  <div class="controls">
	                    <input type="text" name="dprecio_compra" id="dprecio_compra" value="<?php echo set_value('dprecio_compra'); ?>"
	                    	class="input-large vpositive" placeholder="30, 20, 3 (pesos)">
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="dcantidad">*Cantidad inicial </label>
	                  <div class="controls">
	                    <input type="text" name="dcantidad" id="dcantidad" value="<?php echo set_value('dcantidad'); ?>"
	                    	class="input-large vpositive" placeholder="0, 30, 20, 3 (unidades)">
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="dmarca">Marca </label>
	                  <div class="controls">
	                    <input type="text" name="dmarca" id="dmarca" value="<?php echo set_value('dmarca'); ?>"
	                    	class="input-large" placeholder="truper, surtek">
	                  </div>
	                </div>

	                <div class="control-group">
					          <label class="control-label" for="dproveedor">Proveedor</label>
					          <div class="controls">
					            <input type="text" name="dproveedor" class="input-xlarge" id="dproveedor" 
					            	value="<?php echo set_value('dproveedor'); ?>" size="73" placeholder="Ferreteria, laminado">
					            <input type="hidden" name="did_proveedor" id="did_proveedor" value="<?php echo set_value('did_proveedor'); ?>">
					          </div>
					        </div>

					        <div class="control-group">
					          <label class="control-label" for="ddescripcion">Descripción</label>
					          <div class="controls">
					          	<textarea name="ddescripcion" id="ddescripcion" class="span9" placeholder="El producto contiene cloruro de magnesia"><?php echo set_value('ddescripcion'); ?></textarea>
					          </div>
					        </div>

					        <div class="control-group">
					          <label class="control-label" for="dis_same_fam">Registrar en familias?</label>
					          <div class="controls">
					            <input type="checkbox" name="dis_same_fam" class="input-xlarge" id="dis_same_fam" 
					            	value="si" <?php echo set_checkbox('dis_same_fam', 'si'); ?>>
					          </div>
					        </div>

								</div> <!--/span -->

								<div class="span4" id="same_family" style="display:none;">
									<div class="control-group">
	                  <label class="control-label" for="dprecio_venta">Precio venta </label>
	                  <div class="controls">
	                    <input type="text" name="dprecio_venta" id="dprecio_venta" value="<?php echo set_value('dprecio_venta'); ?>"
	                    	class="vpositive" placeholder="30, 20, 3 (pesos)">
	                  </div>
	                </div>
	                
	                <div class="control-group">
	                  <label class="control-label" for="dcodigo">Codigo de Barras </label>
	                  <div class="controls">
											<input type="text" name="dcodigo" id="dcodigo" value="<?php echo set_value('dcodigo'); ?>" 
												class="" placeholder="736HS212, 828102, 2232123">
	                  </div>
	                </div>

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

