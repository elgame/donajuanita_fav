$(function(){
  $('button#btn-ver-detalle').on('click', function(event) {
    var btn       = $(this),
        subtotal  = $('#tvsubtotal'),
        desc_perc = $('#tvdesc-perc'),
        desc      = $('#tvdesc'),
        total     = $('#tvtotal'),
        recibido  = $('#tvrecibido'),
        cambio    = $('#tvcambio'),
        tipoPago  = $('#seltipopago'),

        table = $('#table-item-list');

    $.post(base_url + 'panel/tickets/ajax_get_info_ticket', {'id': btn.attr('data-id')}, function(data) {
      // console.log(data);

      desc_dinero = parseFloat(parseFloat(data.info.subtotal) * (parseInt(data.info.descuento, 10) / 100)).toFixed(2) // Calcula el descuento real

      subtotal.html(util.darFormatoNum(data.info.subtotal));
      desc_perc.html(data.info.descuento)
      desc.html(util.darFormatoNum(desc_dinero));
      total.html(util.darFormatoNum(data.info.total));
      recibido.html(util.darFormatoNum(data.info.recibido));
      cambio.html(util.darFormatoNum(data.info.cambio));
      tipoPago.val(data.info.tipo_pago);

      var tdHtml = '';
      data.items.forEach(function(e, i) {
        tdHtml += '<tr>' +
                    '<td>'+e.nombre+'</td>' +
                    '<td>'+e.cantidad+'</td>' +
                    '<td>'+e.descuento+'%</td>' +
                  '</tr>';
      });

      table.find('tbody').html(tdHtml);

      $('#myModal').modal('toggle')

    }, 'json');

  });
});