<input type="hidden" value="<?php echo $config->fuente_pv ?>" id="configFuente" />

<div class="container-fluid"> <!-- START container-fluid -->
  <div class="row-fluid"> <!-- START row-fluid -->

    <!-- TICKET Y CALCULADORA  -->
    <div class="span3" id="tickettotalCalcArea"> <!-- START span3 -->

      <span class="text-title">LISTADO</span>
      <div class="ticket-produc" id="ticketArea"> <!-- START ticket-produc -->
        <table class="table table-striped table-listado" id="table-listado"> <!-- START table-listado -->
          <thead>
            <tr>
              <th>Prod.</th>
              <th>Cant.</th>
              <th>Precio</th>
              <th>Desc</th>
            </tr>
          </thead>
          <tbody>
            <!-- <tr id="">
              <td>waawawaw</td>
              <td>12</td>
              <td>100</td>
              <td><input type="text" value="0" class="span8" id=""></td>
            </tr>
          </tbody> -->
        </table> <!-- END table-listado -->
      </div><!-- END ticket-produc -->


      <div class="row-fluid" id="totalArea">
        <div class="span12">
          <table class="table">
            <thead>
              <tr>
                <!-- font-size:2em; -->
                <th style="font-size:2em; text-align:center;">TOTAL<input type="hidden" id="itotalv" value=""></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <!-- font-size:3em; -->
                <td id="ttotal" style="font-size:3em; text-align:center; color:#5bb75b;">$0.00</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>


      <div class="row-fluid calc" id="calcArea"> <!-- START row-fluid-calc -->
        <div class="span12">
          <button type="button" class="btn btn-large span3">7</button>
          <button type="button" class="btn btn-large span3">8</button>
          <button type="button" class="btn btn-large span3">9</button>
          <button type="button" class="btn btn-large span3 btn-primary">/</button>
        </div>

        <div class="span12">
          <button type="button" class="btn btn-large span3">4</button>
          <button type="button" class="btn btn-large span3">5</button>
          <button type="button" class="btn btn-large span3">6</button>
          <button type="button" class="btn btn-large span3 btn-primary">*</button>
        </div>

        <div class="span12">
          <button type="button" class="btn btn-large span3">1</button>
          <button type="button" class="btn btn-large span3">2</button>
          <button type="button" class="btn btn-large span3">3</button>
          <button type="button" class="btn btn-large span3 btn-primary">-</button>
        </div>

         <div class="span12">
          <button type="button" class="btn btn-large span3">0</button>
          <button type="button" class="btn btn-large span3">.</button>
          <button type="button" class="btn btn-large span3 btn-inverse">Supr</button>
          <button type="button" class="btn btn-large span3 btn-primary">C</button>
        </div>

        <div class="span12">
          <button type="button" class="btn btn-large span12 btn-success">ENTER</button>
        </div>

        <div class="span12">
          <!-- font-size: 1.8em; -->
          <input type="text" value="" class="span12" id="codigo-barras" placeholder="12345, 67890, asdf" style="font-size: 1.8em; height: 50px; text-align:center;">
        </div>


      </div> <!-- END row-fluid-calc -->

    </div><!-- END span3 -->


    <!-- LISTADO DE LAS FAMILIAS|PRODUCTOS -->
      <div class="span9 productos" id="productosArea">
        <span class="text-title">FAMILIAS & PRODUCTOS</span>
        <div class="myClass">
          <div class="row-fluid" id="familias-container">

            <div class="span3 familia-row" id="familiaArea">
              <ul class="thumbnails">
                <?php foreach($productos_padres as $key => $padre) {

                    $tiene_imagen = false;
                    if ($padre->imagen !== null && $padre->imagen !== ''){
                      $tiene_imagen = true;
                    }

                    $dataPbf = '';

                    if (isset($padre->productos_base_fam))
                    {
                      $json = array();
                      $jsonStr = '';

                      foreach ($padre->productos_base_fam as $prod)
                      {
                        $jsonStr = "'".$prod->base_id."' : {'base_id':'".$prod->base_id."', 'cantidad': '".$prod->cantidad."', 'precio_compra' :'".$prod->precio_compra."', 'nombre': '".$prod->nombre."'}";
                        array_push($json, $jsonStr);
                      }

                      $dataPbf = '{'.implode(',', $json).'}';
                    }

                  ?>
                  <li class="span12" id="item" data-id-padre="<?php echo $padre->id_padre; ?>" data-id="<?php echo $padre->id; ?>" data-last-nodo="<?php echo $padre->ultimo_nodo; ?>" data-precio="<?php echo $padre->precio_venta; ?>"  data-pbf="<?php echo $dataPbf ?>">
                    <div class="thumbnail" <?php echo ($padre->color1 !== null && $padre->color2 !== null) ? 'style="background: -webkit-linear-gradient(top,  '.$padre->color1.' 0%, '.$padre->color2.' 100%);"': ''?>>
                      <div class="caption" <?php echo (!$tiene_imagen) ? 'style="display: table;"' : '' ?>>
                        <?php if ($tiene_imagen){ ?>
                          <img src="<?php echo base_url('application/images/familias/'.$padre->imagen); ?>" width="80" height="80">
                        <?php } ?>
                        <p <?php echo (!$tiene_imagen) ? 'style="vertical-align: middle; display: table-cell;"' : '' ?>><?php echo $padre->nombre; ?></p>
                      </div>
                    </div>
                  </li>
                <?php } ?>
              </ul>
            </div>

            <div class="span3 familia-row" id="familiaArea">
              <ul class="thumbnails">
                <?php foreach($productos_hijos_level1 as $key => $hijo) {

                    $tiene_imagen = false;
                    if ($hijo->imagen !== null && $hijo->imagen !== ''){
                      $tiene_imagen = true;
                    }

                    $dataPbf = '';

                    if (isset($hijo->productos_base_fam))
                    {
                      $json = array();
                      $jsonStr = '';

                      foreach ($hijo->productos_base_fam as $prod)
                      {
                        $jsonStr = "'".$prod->base_id."' : {'base_id':'".$prod->base_id."', 'cantidad': '".$prod->cantidad."', 'precio_compra' :'".$prod->precio_compra."', 'nombre': '".$prod->nombre."'}";
                        array_push($json, $jsonStr);
                      }

                      $dataPbf = '{'.implode(',', $json).'}';
                    }

                  ?>
                  <li class="span12" id="item" data-id-padre="<?php echo $hijo->id_padre; ?>" data-id="<?php echo $hijo->id; ?>" data-last-nodo="<?php echo $hijo->ultimo_nodo; ?>" data-precio="<?php echo $hijo->precio_venta; ?>" data-pbf="<?php echo $dataPbf ?>">
                    <div class="thumbnail" <?php echo ($hijo->color1 !== null && $hijo->color2 !== null) ? 'style="background: -webkit-linear-gradient(top,  '.$hijo->color1.' 0%, '.$hijo->color2.' 100%);"': ''?>>
                      <div class="caption" <?php echo (!$tiene_imagen) ? 'style="display: table;"' : '' ?>>
                        <?php if ($tiene_imagen){ ?>
                          <img src="<?php echo base_url('application/images/familias/'.$hijo->imagen); ?>" width="80" height="80">
                        <?php } ?>
                        <p <?php echo (!$tiene_imagen) ? 'style="vertical-align: middle; display: table-cell;"' : '' ?>><?php echo $hijo->nombre; ?></p>
                      </div>
                    </div>
                  </li>
                <?php } ?>
              </ul>
            </div>

            <div class="span3 familia-row" id="familiaArea">
              <ul class="thumbnails">
                <?php foreach($productos_hijos_level2 as $key => $hijo) {

                    $tiene_imagen = false;
                    if ($hijo->imagen !== null && $hijo->imagen !== ''){
                      $tiene_imagen = true;
                    }

                    $dataPbf = '';

                    if (isset($hijo->productos_base_fam))
                    {
                      $json = array();
                      $jsonStr = '';

                      foreach ($hijo->productos_base_fam as $prod)
                      {
                        $jsonStr = "'".$prod->base_id."' : {'base_id':'".$prod->base_id."', 'cantidad': '".$prod->cantidad."', 'precio_compra' :'".$prod->precio_compra."', 'nombre': '".$prod->nombre."'}";
                        array_push($json, $jsonStr);
                      }

                      $dataPbf = '{'.implode(',', $json).'}';
                    }

                  ?>
                  <li class="span12" id="item" data-id-padre="<?php echo $hijo->id_padre; ?>" data-id="<?php echo $hijo->id; ?>" data-last-nodo="<?php echo $hijo->ultimo_nodo; ?>" data-precio="<?php echo $hijo->precio_venta; ?>" data-pbf="<?php echo $dataPbf ?>">
                    <div class="thumbnail" <?php echo ($hijo->color1 !== null && $hijo->color2 !== null) ? 'style="background: -webkit-linear-gradient(top,  '.$hijo->color1.' 0%, '.$hijo->color2.' 100%);"': ''?>>
                      <div class="caption" <?php echo (!$tiene_imagen) ? 'style="display: table;"' : '' ?>>
                        <?php if ($tiene_imagen){ ?>
                          <img src="<?php echo base_url('application/images/familias/'.$hijo->imagen); ?>" width="80" height="80">
                        <?php } ?>
                        <p <?php echo (!$tiene_imagen) ? 'style="vertical-align: middle; display: table-cell;"' : '' ?>><?php echo $hijo->nombre; ?></p>
                      </div>
                    </div>
                  </li>
                <?php } ?>
              </ul>
            </div>

          </div>
        </div>
      </div>

  </div> <!-- END row-fluid -->
</div> <!-- END container-fluid -->


<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Finalizando Venta</h3>
  </div>
  <div class="modal-body">

      <div class="row-fluid">

        <div class="span6">
          <div class="row-fluid">
            <div class="span12">
              <table class="table ttotales">
                <thead>
                  <tr col="3">
                    <th>VENTA</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>SUB-TOTAL<input type="hidden" id="itvtvsubtotal_no_iva" value="0"></td>
                    <td id="tvsubtotal_no_iva">$0.00</td>
                  </tr>
                  <tr>
                    <td>IVA<input type="hidden" id="itviva" value="0"></td>
                    <td id="tviva">$0.00</td>
                  </tr>
                  <tr>
                    <td>DESCUENTO</td>
                    <!-- font-size: 1em !important; -->
                    <td id="tvdesc"><input type="text" class="vpos-int" id="itvdesc" value="0"
                      style="height: 22px;width: 56px;color: rgb(13, 158, 226);text-align: center;"><span>%</span>
                      <span id="desc-dinero">($0.00)</span>
                    </td>
                  </tr>
                   <tr>
                    <td>TOTAL<input type="hidden" id="itvtotal-modal" value="0"></td>
                    <td id="tvtotal">$0.00</td>
                  </tr>

                  <tr>
                    <td>PAGO CON<input type="hidden" id="itvrecibido" value=""></td>
                    <td id="tvrecibido">$0.00</td>
                  </tr>
                  <tr>
                    <td>CAMBIO<input type="hidden" id="itvcambio" value=""></td>
                    <td id="tvcambio">$0.00</td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <span class="span12">TIPO DE PAGO</span>
                      <select name="seltipopago" class="span12" id="seltipopago">
                        <option value="efectivo">EFECTIVO</option>
                        <option value="cheque">CHEQUE</option>
                        <option value="tarjeta">TARJETA</option>
                        <option value="transferencia">TRANSFERENCIA</option>
                        <option value="deposito">DEPOSITO</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                     <label for="imprimir" style="font-size: 1.5em !important;display: inline-block;">IMPRIMIR</label>
                     <input type="checkbox" id="imprimir" value="1" name="print" style="margin-left: 14px;margin-top: -8px;">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="span6">
          <div class="row-fluid calc2" id="calculadora">

            <div class="span12">
              <input type="text" id="calcDisplay" class="input-block-level" value="" readonly>
            </div>

            <div class="span12">
              <button type="button" class="btn btn-large span4">7</button>
              <button type="button" class="btn btn-large span4">8</button>
              <button type="button" class="btn btn-large span4">9</button>
            </div>

            <div class="span12">
              <button type="button" class="btn btn-large span4">4</button>
              <button type="button" class="btn btn-large span4">5</button>
              <button type="button" class="btn btn-large span4">6</button>
            </div>

            <div class="span12">
              <button type="button" class="btn btn-large span4">1</button>
              <button type="button" class="btn btn-large span4">2</button>
              <button type="button" class="btn btn-large span4">3</button>
            </div>

            <div class="span12">
              <button type="button" class="btn btn-large span4">0</button>
              <button type="button" class="btn btn-large span4">.</button>
              <button type="button" class="btn btn-large span4 btn-primary">C</button>
            </div>

            <div class="span12">
              <button type="button" class="btn btn-large span12 btn-danger">ACEPTAR</button>
            </div>

          </div>
        </div>

        <!-- <div class="span12">
          <button type="button" class="btn btn-large span12 btn-success">ENTER</button>
        </div> -->
      </div>


  </div>
  <div class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" aria-hidden="true">Regresar</button>
    <button class="btn btn-large btn-success" id="save-venta">Finalizar</button>
  </div>
</div>

<!-- Modal Productos Base Familia -->
<div id="modalPBF" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalPBFLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="modalPBFLabel"></h3>
  </div>
  <div class="modal-body">

      <div class="row-fluid">

        <div class="span6">
          <div class="row-fluid">
            <div class="span12" id="bloqPb">
              <label>Producto</label>
              <input type="hidden" value="" id="idProdListado">

              <input type="text" id="autopb" class="input-large sikey" data-next="cantidadpb">
              <input type="hidden" id="idautopb">

              <label>Cantidad</label>
              <input type="text" id="cantidadpb" class="input-small vpositive sikey" data-next="autopb">

              <label>Precio</label>
              <input type="text" id="preciopb" class="input-small vpositive sikey" readonly>
            </div>
          </div>
        </div>

        <div class="span6">
          <div class="row-fluid" style="max-height:253px; overflow: auto;">
            <table class="table table-striped table-bordered table-hover table-condensed" id="tablePBF">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Cant.</th>
                  <th>Precio</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="row-fluid">
            <div class="control-group">
              <label class="control-label" for="name">Precio Venta</label>
              <div class="controls">
                <input type="text" name="name" id="precio-venta-pb" class="vpositive" value="">
              </div>
            </div>
          </div>
        </div>

        <!-- <div class="span12">
          <button type="button" class="btn btn-large span12 btn-success">ENTER</button>
        </div> -->
      </div>

  </div>
  <div class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" aria-hidden="true">Regresar</button>
    <button class="btn btn-large btn-success" id="btnAddProdBase">Agregar</button>
  </div>
</div>