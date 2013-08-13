<?php 
            if (isset($data['info']))
              foreach($data['info'] as $product) {?>
                <tr>
                  <td><?php echo $product->nombre; ?></td>
                  <td><?php echo $product->cantidad; ?></td>
                  <td><?php echo String::formatoNumero($product->importe); ?></td>
                </tr>
            <?php }?>