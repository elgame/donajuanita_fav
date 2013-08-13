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
          <li>Modificar</li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-edit"></i> Modificar Familia</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/familias/modificar/?'.String::getVarsLink(array('msg'))); ?>" method="post" 
                enctype="multipart/form-data" class="form-horizontal" id="form">
              <fieldset>
                <legend></legend>

                <div class="span7">
	                <div class="control-group">
                    <label class="control-label" for="dnombre">*Nombre </label>
                    <div class="controls">
                      <input type="text" name="dnombre" id="dnombre" value="<?php echo isset($familia['info']->nombre)?$familia['info']->nombre:''; ?>" 
                        class="input-xlarge" placeholder="Leche, Cafe, Cucharas" autofocus>
                    </div>
                  </div>

	                <div class="control-group">
                    <label class="control-label" for="dprecio_venta">Precio venta </label>
                    <div class="controls">
                      <input type="text" name="dprecio_venta" id="dprecio_venta" value="<?php echo isset($familia['info']->precio_venta)?$familia['info']->precio_venta:''; ?>"
                        class="input-large vpositive" placeholder="30, 20, 3 (pesos)">
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label" for="dcodigo">Codigo de Barras </label>
                    <div class="controls">
                      <input type="text" name="dcodigo" id="dcodigo" value="<?php echo isset($familia['info']->codigo_barra)? $familia['info']->codigo_barra:''; ?>" 
                        class="input-xlarge" placeholder="736HS212, 828102, 2232123">
                    </div>
                  </div>

                  <div class="control-group">
                    <label for="dimagen" class="control-label">Imagen</label>
                    <div class="controls">
                      <input type="file" name="dimagen" id="dimagen" value="" size="30">
                      <?php 
                      if (isset($familia['info']->imagen)) {
                        if ($familia['info']->imagen!='')
                          echo '<img src="'.base_url('application/images/familias/'.$familia['info']->imagen).'" width="50" height="50">';
                      }
                       ?>
                    </div>
                  </div>

                  <div class="control-group">
                    <label for="dcolor" class="control-label">Color</label>
                    <div class="controls">
                      <input type="text" name="dcolor" id="dcolor" class="pull-left" value="<?php echo isset($familia['info']->color1)?$familia['info']->color1:''; ?>" size="30" maxlength="7">
                      <label for="dcolor_plano" class="span2">Color plano <input type="checkbox" name="dcolor_plano" value="si" <?php echo set_checkbox('dcolor_plano', 'si', (isset($color_plano)? true: false) ); ?>></label>
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
                        <?php echo $this->familias_model->getFrmFamilias(0, true, (isset($familia['info']->id_padre)? $familia['info']->id_padre: null), true); ?>
                      </div>
                    </div>
                  </div>

                  <div class="control-group">
                    <label class="control-label" for="dactualiza_base">Actualiza Producto Base </label>
                    <div class="controls">
                      <input type="checkbox" name="dactualiza_base" id="dactualiza_base" value="si" data-uniform="false" <?php echo set_checkbox('dactualiza_base', 'si'); ?>>
                    </div>
                  </div>

                  <div class="control-group only_update_pbase hide">
                    <label class="control-label" for="dprecio_compra">Precio de compra </label>
                    <div class="controls">
                      <input type="text" name="dprecio_compra" id="dprecio_compra" value="<?php echo set_value('dprecio_compra') ?>" 
                        class="span10" placeholder="32, 433, 821">
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
                      <table id="tbl-pl" class="table table-condensed">
                        <tbody>
                      <?php 
                    if (isset($familia['consumos'])) {
                      if(is_array($familia['consumos'])){
                        foreach($familia['consumos'] as $key => $row){ ?>
                        <tr id="tr-pl<?php echo $row->base_id; ?>" class="tr-produclista">
                          <td style="width:66%;"><?php echo $row->nombre; ?>
                            <input type="hidden" name="dpcnombres[]" value="<?php echo $row->nombre; ?>">
                            <input type="hidden" name="dpcids[]" value="<?php echo $row->base_id; ?>"></td>
                          <td style="width:33%;">
                            <input type="text" name="dpccantidad[]" value="<?php echo $row->cantidad; ?>" class="span12"></td>
                        </tr>
                      <?php } 
                      }
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

