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
          <li>Modificar</li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-edit"></i> Modificar Producto</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/productos/modificar/?'.String::getVarsLink(array('msg'))); ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="form">
              <fieldset>
                <legend></legend>

                <div class="span11">
	                <div class="control-group">
	                  <label class="control-label" for="dnombre">*Nombre </label>
	                  <div class="controls">
											<input type="text" name="dnombre" id="dnombre" value="<?php echo isset($producto->nombre)?$producto->nombre:''; ?>" 
												class="input-xlarge" placeholder="Leche, Cafe, Cucharas" autofocus>
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="dstock_min">*Stock Min </label>
	                  <div class="controls">
	                    <input type="text" name="dstock_min" id="dstock_min" value="<?php echo isset($producto->stock_min)?$producto->stock_min:''; ?>" 
	                    	class="input-xlarge vpositive" placeholder="5, 1, 10 (unidades)">
	                  </div>
	                </div>

                  <div class="control-group">
                    <label class="control-label" for="dmarca">Marca </label>
                    <div class="controls">
                      <input type="text" name="dmarca" id="dmarca" value="<?php echo isset($producto->marca)?$producto->marca:''; ?>"
                        class="input-large" placeholder="truper, surtek">
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label" for="dproveedor">Proveedor</label>
                    <div class="controls">
                      <input type="text" name="dproveedor" class="input-xlarge" id="dproveedor" 
                        value="<?php echo isset($producto->nombre_fiscal)?$producto->nombre_fiscal:''; ?>" size="73" placeholder="Ferreteria, laminado">
                      <input type="hidden" name="did_proveedor" id="did_proveedor" 
                        value="<?php echo isset($producto->proveedor_id)?$producto->proveedor_id:''; ?>">
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label" for="ddescripcion">Descripción</label>
                    <div class="controls">
                      <textarea name="ddescripcion" id="ddescripcion" class="span6" placeholder="El producto contiene cloruro de magnesia"><?php 
                        echo isset($producto->descripcion)?$producto->descripcion:''; ?></textarea>
                    </div>
                  </div>

								</div> <!--/span -->

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

