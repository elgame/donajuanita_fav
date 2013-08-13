
    <div id="content" class="span10">
      <!-- content starts -->


      <div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
          </li>
          <li>
            Tickets
          </li>
        </ul>
      </div>

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-list-alt"></i> Tickets</h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <form action="<?php echo base_url('panel/tickets/'); ?>" method="get" class="form-search">
              <fieldset>
                <legend>Filtros</legend>

                <label for="fnombre">buscar:</label>
                <input type="text" name="fnombre" id="fnombre" value="<?php echo set_value_get('fnombre'); ?>" class="input-large"
                  placeholder="Admin, 10, 50" autofocus> |

                <label for="fstatus">Estado</label>
                <select name="fstatus">
                  <option value="1" <?php echo set_select('fstatus', '1', false, $this->input->get('fstatus')); ?>>ACTIVOS</option>
                  <option value="0" <?php echo set_select('fstatus', '0', false, $this->input->get('fstatus')); ?>>CANCELADOS</option>
                  <option value="todos" <?php echo set_select('fstatus', 'todos', false, $this->input->get('fstatus')); ?>>TODOS</option>
                </select>

                <button class="btn">Buscar</button>
              </fieldset>
            </form>

            <?php
            // echo $this->usuarios_model->getLinkPrivSm('familias/agregar/', array(
            //         'params'   => '',
            //         'btn_type' => 'btn-success pull-right',
            //         'attrs' => array('style' => 'margin: 0px 0 10px 10px;') )
            //     );
             ?>
            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>Folio</th>
                  <th>Usuario</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody>
            <?php foreach($tickets['tickets'] as $ticket){ ?>
                <tr>
                  <td><?php echo $ticket->folio ?></td>
                  <td><?php echo $ticket->usuario ?></td>
                  <td><?php echo $ticket->fecha ?></td>
                  <td><?php echo String::formatoNumero($ticket->total) ?></td>
                  <td>
                    <?php
                      if($ticket->status == 1){
                        $v_status    = 'Activo';
                        $vlbl_status = 'label-success';
                      }else{
                        $v_status    = 'Cancelado';
                        $vlbl_status = 'label-important';
                      }
                    ?>
                    <span class="label <?php echo $vlbl_status; ?>"><?php echo $v_status; ?></span>
                  </td>
                  <td class="center">
                    <?php
                    if ($ticket->status == 1)
                      echo $this->usuarios_model->getLinkPrivSm('tickets/eliminar/', array(
                          'params'   => 'id='.$ticket->id,
                          'btn_type' => 'btn-danger',
                          'attrs' => array('onclick' => "msb.confirm('Estas seguro de cancelar el ticket?', 'Tickets', this); return false;"))
                      );

                      echo $this->usuarios_model->getLinkPrivSm('punto_venta/imprime_ticket/', array(
                          'params'   => 'id='.$ticket->id,
                          'btn_type' => 'btn-primary',
                          'attrs' => array('target' => '_BLANK'))
                      );
                    ?>

                    <button class="btn btn-info" id="btn-ver-detalle" type="button" data-id="<?php echo $ticket->id ?>"
                        data-toggle="modal" data-target="#detalle">
                      <i class="icon-eye-open icon-white"></i><span class="hidden-tablet"> Ver Detalle</span>
                    </button>

                  </td>
              </tr>
          <?php }?>
              </tbody>
            </table>

            <?php
            //Paginacion
            $this->pagination->initialize(array(
                'base_url'      => base_url($this->uri->uri_string()).'?'.String::getVarsLink(array('pag')).'&',
                'total_rows'    => $tickets['total_rows'],
                'per_page'      => $tickets['items_per_page'],
                'cur_page'      => $tickets['result_page']*$tickets['items_per_page'],
                'page_query_string' => TRUE,
                'num_links'     => 1,
                'anchor_class'  => 'pags corner-all',
                'num_tag_open'  => '<li>',
                'num_tag_close' => '</li>',
                'cur_tag_open'  => '<li class="active"><a href="#">',
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


<!-- Modal -->
<div id="detalle" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">DETALLE DE TICKET</h3>
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
                    <td>SUB-TOTAL</td>
                    <td id="tvsubtotal">$0.00</td>
                  </tr>
                   <tr>
                    <td>DESCUENTO (<span id="tvdesc-perc">0</span>%)</td>
                    <td id="tvdesc">$0.00</span>
                    </td>
                  </tr>
                   <tr>
                    <td>TOTAL</td>
                    <td id="tvtotal">$0.00</td>
                  </tr>

                  <tr>
                    <td>PAGO CON</td>
                    <td id="tvrecibido">$0.00</td>
                  </tr>
                  <tr>
                    <td>CAMBIO</td>
                    <td id="tvcambio">$0.00</td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <span class="span12">TIPO DE PAGO</span>
                      <select name="seltipopago" class="span12" id="seltipopago" disabled>
                        <option value="efectivo">EFECTIVO</option>
                        <option value="cheque">CHEQUE</option>
                        <option value="tarjeta">TARJETA</option>
                        <option value="transferencia">TRANSFERENCIA</option>
                        <option value="deposito">DEPOSITO</option>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="span6">
          <div class="row-fluid">

            <table class="table table-striped table-bordered table-condensed" id="table-item-list">
              <thead>
                <tr>
                  <th>NOMBRE</th>
                  <th>CANT</th>
                  <th>DESC</th>
                </tr>

              </thead>
              <tbody>
              </tbody>
            </table>

          </div>
        </div>
      </div>


  </div>

  <div class="modal-footer">
    <button class="btn btn-large" data-dismiss="modal" aria-hidden="true">Cerrar</button>
  </div>

</div>
<!-- END MODAL -->

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


