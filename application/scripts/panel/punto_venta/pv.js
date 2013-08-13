var styleP = '',
    calcTouch = true; // Bandera Auxiliar para saber con que calculadora realizar los
                     // eventos al presionar desde el teclado
$(function(){

  setConfig(); // Asigna la configuracion de la fuente.

  setDynamicHeight(); // Funcion que recalcula el alto.

  // swipeHorizontal(); // Inicializa el swipe horizontal para touch o mouseevents.
  swipeVertical(); // Inicializa el swipe vertical para touch o mouseevents.

  calculadoraPv.init(); // Inicializa la calculadora del touch

   // Evento para los span3 del listado de productos/items
  $(document).on('click', 'li#item', function(event) {
    // alert($(this).attr('data-id') + ' - ' + $(this).attr('data-last-nodo') + ' - ' + $(this).parent().parent().attr('data-level'));

    var data_id_padre  = $(this).attr('data-id-padre'), // almancea el id padre al que pertenece
        data_id        = $(this).attr('data-id'), // almacena el id
        data_last_nodo = $(this).attr('data-last-nodo'), // almacena el last_nodo
        data_level     = $(this).parent().parent().attr('data-level'), // almacena el nivel en que se encuentra
        data_precio    = parseFloat($(this).attr('data-precio')), // almacena el precio del producto
        nombre         = $(this).find('p').html(), // almacena el nombre del producto
        li_html        = '',  // contendra los elemento <li> que se construyen para los hijos
        li_padres_html = '', // contendra los elemento <li> que se construyen para los padres

        parent = $(this).parent().parent(),

        data_pbf = $(this).attr('data-pbf');

    // Si el item <li> que se le da click es padre
    if (data_last_nodo == 0) {

      $.get(base_url + 'panel/punto_venta/ajax_get_hijos', {'id': data_id}, function(data) {
        var leftPos = $('div.myClass').scrollLeft(); // Obtiene la posicion del scroll hacia la izquierda

        li_html = build_padres_hijos(data); // pasa data a la funcion para contruir los tag <li> de los hijos
        $(parent).nextAll().remove(); // Elimina todo despues del padre del <li> que se le dio click
        $(li_html).insertAfter(parent); // inserta los nuevo hijos

        // Mueve el scroll con un efecto animado
        $("div.myClass").animate({
            scrollLeft: leftPos + 230
        }, 300);

      }, "json");

    } else { // Si el item <li> que se le da click es ultimo nodo

      var item = {'id': data_id, 'nombre': nombre, 'precio': data_precio, 'pbf': data_pbf};

      buildItemList(item);

    }

  });

  /**
   * Evento Click para seleccionar un item del listado
   */
  $('#table-listado').on('click', 'tbody tr', function(event) {
    if ($('.clicked').length === 3) {
      if ($('td.clicked').parent().attr('id') !== $(this).attr('id')) {
        $('td.clicked').toggleClass('clicked');
      }
    }
    $(this).find('td').toggleClass('clicked');
  });

  /**
   * Evento DblClick para los items del listado. Abre modal para agregar
   * mas Productor Base.
   */
  $('#table-listado').on('dblclick', 'tbody tr', function(event) {
    var $this = $(this),
        idProdListado = $this.find('#iid').val();
        $tbody = $('#tablePBF').find('tbody').html(''),
        jsonPBF = JSON.parse($this.attr('data-pbf').replace(/'/g, '"')),
        trHtml = '';

    $('#idProdListado').val(idProdListado)

    for (var i in jsonPBF) {
      // console.log(i);
      trHtml += '<tr>';
      trHtml +=  '<td id="json_nombre">'+jsonPBF[i].nombre+'<input type="hidden" value="'+jsonPBF[i].base_id+'" id="json_base_id"/></td>'
      trHtml +=  '<td><input type="text" value="'+jsonPBF[i].cantidad+'" class="vpos-int" id="json_cantidad" style="width: 40px;"/></td>'
      trHtml +=  '<td><input type="text" value="'+jsonPBF[i].precio_compra+'" class="vpositive" id="json_precio_compra" style="width: 40px;" readonly/></td>'
      trHtml += '</tr>';
    }

    $('#precio-venta-pb').val($this.find('#iprecio').val());

    calcTouch = "no";

    $(trHtml).appendTo($tbody);

    $(".vpos-int").removeNumeric();
    $(".vpositive").removeNumeric();
    $(".vpos-int").numeric({ decimal: false, negative: false });
    $(".vpositive").numeric({ negative: false });

    $('#modalPBF').modal('toggle');

  });

  $('#modalPBF').on('hidden', function (e) {
    calcTouch = true;
  });

  $('#autopb').autocomplete({
    source: base_url + 'panel/punto_venta/ajax_get_pbf/',
    minLength: 1,
    selectFirst: true,
    select: function( event, ui ) {
      $("#idautopb").val(ui.item.id);
      $("#cantidadpb").val(ui.item.item.cantidad);
      $("#preciopb").val(ui.item.item.precio_compra);

      $("#autopb").val(ui.item.label).css({'background-color': '#99FF99'});
    }
  }).keydown(function(e){
    if (e.which === 8) {
      $(this).css({'background-color': '#FFD9B3'});
      $("#idautopb").val('');
      $("#cantidadpb").val('');
      $("#preciopb").val('');
    }
  });

  $('#bloqPb').keyJump();

  $('#cantidadpb').keypress(function (e) {
    if (e.charCode == 13) {
      var $this = $(this),
          $tbody = $('#tablePBF').find('tbody'),
          // jsonPBF = JSON.parse($this.attr('data-pbf').replace(/'/g, '"')),
          trHtml = '',

          $prod     = $('#autopb'),
          $base_id  =  $('#idautopb'),
          $cantidad = $('#cantidadpb'),
          $precio   = $('#preciopb');

        trHtml += '<tr>';
        trHtml +=  '<td id="json_nombre">'+$prod.val()+'<input type="hidden" value="'+$base_id.val()+'" id="json_base_id"/></td>'
        trHtml +=  '<td><input type="text" value="'+$cantidad.val()+'" class="vpos-int" id="json_cantidad" style="width: 40px;"/></td>'
        trHtml +=  '<td><input type="text" value="'+$precio.val()+'" class="vpositive" id="json_precio_compra" style="width: 40px;" readonly/></td>'
        trHtml += '</tr>';

        $(trHtml).appendTo($tbody);

        $(".vpos-int").removeNumeric();
        $(".vpositive").removeNumeric();
        $(".vpos-int").numeric({ decimal: false, negative: false });
        $(".vpositive").numeric({ negative: false });

        $prod.val('');
        $('#idautopb').val('');
        $cantidad.val('');
        $precio.val('');
    }

  });

  $('#btnAddProdBase').on('click', function(event) {

    var $tbody = $('#tablePBF').find('tbody'),
        jsonProdBaseFam = [],
        jsonStr = '';

    $tbody.find('tr').each(function (e, i) {
      var $tr = $(this),
          jsonNombre       = $tr.find('#json_nombre').text(),
          jsonBaseid       = $tr.find('#json_base_id').val(),
          jsonCantidad     = $tr.find('#json_cantidad').val(),
          jsonPrecioCompra = $tr.find('#json_precio_compra').val();

      jsonProdBaseFam.push("'"+jsonBaseid+"' : {'base_id':'"+jsonBaseid+"', 'cantidad': '"+jsonCantidad+"', 'precio_compra' :'" +jsonPrecioCompra+"', 'nombre': '"+jsonNombre+"'}");

    });

    jsonStr = '{' + jsonProdBaseFam.join(', ') + '}';

    var $trSelected = $('#tr' + $('#idProdListado').val()),
        cantidad = parseInt($trSelected.find('#icantidad').val(), 10),
        precioVenta = parseFloat($('#precio-venta-pb').val());

    $trSelected.attr('data-pbf', jsonStr); // asigna json
    $trSelected.find('#iprecio').val(precioVenta); // asigna precio
    $trSelected.find('#td-total').html(util.darFormatoNum(cantidad * parseFloat(precioVenta))); // asigna total de prod
    $trSelected.find('#itotal').val(cantidad * parseFloat(precioVenta)); // asigna total de prod input


    $('li[data-id="'+$('#idProdListado').val()+'"]').attr('data-precio', precioVenta);

    $('#modalPBF').modal('toggle');

    calcula_total();
  });

  // Evento Click Btn "finalizar"
  $('#save-venta').on('click', function(event) {

    if (parseFloat($('#itvrecibido').val()) > 0) {

      if (parseFloat($('#itvcambio').val()) >= 0) {

        var venta = [], tr;

        if ($('#imprimir').is(':checked')) {
          venta.push('imprimir');
        }

        venta.push($('#itvtvsubtotal_no_iva').val()); // Subtotal sin iva
        venta.push($('#itviva').val()); // iva

        venta.push($('#itotalv').val()); // Subtotal
        venta.push($('#itvrecibido').val()); // Recibido
        venta.push($('#itvcambio').val()); // Cambio
        venta.push($('#itvdesc').val()); // Descuento
        venta.push($('#itvtotal-modal').val()); // Total

        venta.push($('#seltipopago option:selected').val()); // tipo de pago

        // Recorre todos los item del listado para obtener sus datos
        $('#table-listado').find('tbody tr').each(function(i, e) {
            var tr = $(this),
                id = tr.find('#iid').val();

            venta.push([id,
                        tr.find('#icantidad').val(),
                        tr.find('#iprecio').val(),
                        tr.find('#itotal').val(),
                        tr.find('#iiDesc' + id).val(),
                        tr.attr('data-pbf').replace(/,/g, '#')]);
        });

        $.post(base_url + 'panel/punto_venta/ajax_save_venta/', {'venta[]': venta}, function(data) {

          // console.log(data);

          noty({"text": data[1].msg, "layout":"topRight", "type": data[1].ico});

          if (data.imprimir) {
            var win=window.open(base_url + 'panel/punto_venta/imprime_ticket?id=' + data[0], '_blank');
            win.focus();
          }

          setTimeout("location.reload(true);",1500);
        },'json');

      } else {
        noty({"text": 'La cantidad recibida no puede ser menor que el total', "layout":"topRight", "type": 'error'});
      }

    } else {
      noty({"text": 'Especifique la cantidad recibida', "layout":"topRight", "type": 'error'});
    }

  });

  // Evento para el Teclado
  $(window).keydown(function(e) {

    if (calcTouch == true) { // Si el teclado del Touch es el que esta activo
      calculadoraPv.teclado(e);
    } else if(calcTouch == false){ // Si el teclado del modal es el activo
      calculadora.teclado(e);
    }

  });

  // Evento para resetear la bandera auxiliar que indica cual calculadora esta
  // activa
  $('#myModal').on('hidden', function () {
    calcTouch = true;
  })

  // Evento para el codigo de barras en la caja de texto
  $('#codigo-barras').on('change', function(event) {
    var input = $(this),
        codigo = input.val();

    // console.log(codigo);

    $.post(base_url + 'panel/punto_venta/ajax_codigo_barras', {'codigo_barras': codigo}, function(data) {

      if (!data.hasOwnProperty('ico')) {

        var jsonProdBaseFam = [], jsonStr = '';
        if (data[0].hasOwnProperty('productos_base_fam')) {

          data[0].productos_base_fam.forEach(function (e, i) {
            // jsonProdBaseFam.push('"'+e.base_id+'" : ' + JSON.stringify(e).replace('"', " "));
            jsonProdBaseFam.push("'"+e.base_id+"' : {'base_id':'"+e.base_id+"', 'cantidad': '"+e.cantidad+"', 'precio_compra' :'" + e.precio_compra+"', 'nombre': '"+e.nombre+"'}");

          });
          jsonStr = '{' + jsonProdBaseFam.join(', ') + '}';
        }

        var item = {'id': data[0].id, 'nombre': data[0].nombre, 'precio': data[0].precio_venta, 'pbf': jsonStr};
        // console.log(item);
        buildItemList(item);
      } else {
        noty({"text": data.msg, "layout":"topRight", "type": data.ico});
      }

      input.val('');
    }, 'json');

  }).on('focus', function(){
    calcTouch = "no";
  }).on('focusout', function(){
    calcTouch = true;
    $("body").focus();
  });

  // Eventos para el text input del descuento que esta en el modal
  $('#itvdesc').on('focus', function() {
    calcTouch = "no";
  }).on('focusout', function(){
    calcTouch = false;
  }).on('keyup', function() {
    var input = $(this), // Obj del input del descuento
        desc = $(input).val(),
        subtotal = $('#itotalv'), // Obj del input del total
        totalDesc = 0;

    if (desc === '') desc = 0; // Si el input del descuento esta vacio el desc lo iguala a cero

    // Si el desc es menor a cero o mayor a cien lanza una alerta
    if (parseInt(desc, 10) < 0 || parseInt(desc, 10) > 100) {

      desc = desc.slice(0, desc.length - 1); // Elimina el ultimo caracter de la cadena
      input.val(desc);

      noty({"text": 'El descuento no puede ser mayor a 100', "layout":"topRight", "type": 'error'});
    }

    var iva = (parseFloat(subtotal.val()) - (parseFloat(subtotal.val())/1.16)).toFixed(2),
        suttotal_no_iva = (parseFloat(subtotal.val()) - iva).toFixed(2);
    desc = suttotal_no_iva * (parseFloat(desc) / 100); // Calcula el descuento real

    // desc = parseFloat(parseFloat(subtotal.val() * (parseFloat(desc) / 100))).toFixed(2) // Calcula el descuento real
    $('#desc-dinero').html('(' + util.darFormatoNum(desc) + ')');

    totalDesc = parseFloat(suttotal_no_iva - desc);
    iva = parseFloat(totalDesc*0.16).toFixed(2);
    totalDesc = parseFloat(totalDesc)+parseFloat(iva); // Obtiene el total con el descuento
    
    $('#itviva').val(iva);
    $('#tviva').html(util.darFormatoNum(iva));

    // Obtengo el total recibido para recalcular el cambio
    recibido = parseFloat($('#itvrecibido').val());

    if (recibido > 0) {
      cambio   = recibido - totalDesc;

      $('#tvcambio').html(util.darFormatoNum(cambio));
      $('#itvcambio').val(cambio);
    }

    $('#itvtotal-modal').val(totalDesc);
    $('#tvtotal').html(util.darFormatoNum(totalDesc));

  });

   // Eventos para el text input del descuento que esta en el listado
  $('#table-listado').find('tbody').on('keyup', 'input', function(event) {

    calcDescItemList(this);

  }).on('focus', 'input', function(event) {
    calcTouch = "no";
  }).on('focusout', 'input', function(){
    calcTouch = true;
  });;

});

var setConfig = function () {
  var configFuente = $('#configFuente').val();
  $('<style type="text/css">.thumbnails .caption p{font-size:'+configFuente+'px !important;}</style>')
  .appendTo('head')
};

// Funcion para signar el swipe horizontal, detecta movimientos de touch o mouseevents.
var swipeHorizontal = function () {
  var $wrap = $('#productosArea'), // Wrapper.
      $myClass = $('.myClass'), // DIV con el scroll.
      leftPos, // Almacena la posicion del scroll.
      lastMove; // Almacena el ultimo movimiento.

  $wrap
  .on('swipeleft', function(e) {
    // console.log('swipeleft');
  })
  .on('swiperight', function(e) {
    // console.log('swiperight');
  })
  .on('movestart', function(e) {
    // console.log('movestart');

    // Asigna el primer movimiento al iniciar.
    lastMove = e.distX;

    if ((e.distX > e.distY && e.distX < -e.distY) ||
        (e.distX < e.distY && e.distX > -e.distY)) {
      e.preventDefault();
      return;
    }
  })
  .on('move', function(e) {

    // console.log(e);
    // console.log($myClass.get(0).scrollWidth > $myClass.width());

    // Verifica si el elemento tiene scroll.
    if ($myClass.get(0).scrollWidth > $myClass.width()) {

      // Calcula la cantidad a moverse hacia la izquiera o derecha.
      var move = Math.abs(100 * (e.distX / $(this).width()));

      leftPos = $myClass.scrollLeft(); // Obtiene la posicion actual del scroll.

      // console.log(move);
      // console.log(e.distX + ' & ' + lastMove);

      // Si el movimiento es hacia la izquierda entonces mueve el scroll hacia
      // la derecha.
      if ((e.distX < 0 && e.distX < lastMove) || (lastMove > e.distX && e.distX > 0)) {

        // Si el movimiento realizado fue derecha a izquierda recalcula la cantidad
        // hacia la izquierda.
        if (lastMove > e.distX && e.distX > 0) move = lastMove - e.distX;

        // Mueve el scroll.
        $myClass.scrollLeft(leftPos + move);

        // Asigna el ultimo movimiento.
        lastMove = e.distX;

        // console.log('izq');
      }

      if ((e.distX > 0 && e.distX > lastMove) || (lastMove < e.distX && e.distX < 0)) {
        // Si el movimiento es hacia la derecha entonces mueve el scroll hacia
        // la izquierda.

        // Si el movimiento realizado fue derecha a izquierda recalcula la cantidad
        // hacia la derecha.
        if (lastMove < e.distX && e.distX < 0) move = Math.abs(lastMove) + e.distX;

        // Mueve el scroll.
        $myClass.scrollLeft(leftPos - move);

        // Asigna el ultimo movimiento.
        lastMove = e.distX;

        // console.log('der');
      }

    }

  })
  .on('moveend', function(e) {
    // console.log('moveend');
  });
};

// Funcion para signar el swipe vertical, detectar movimientos de touch o mouseevents.
var swipeVertical = function () {
  var $document = $(document), // Wrapper.
      topPos, // Almacena la posicion del scroll.
      lastMove; // Almacena el ultimo movimiento.

  $document
  .on('swipeleft', 'div#familiaArea', function(e) {
    // console.log('swipeleft');
  })
  .on('swiperight', 'div#familiaArea', function(e) {
    // console.log('swiperight');
  })
  .on('movestart', 'div#familiaArea', function(e) {
    // console.log('movestart');
    lastMove = e.distY;
  })
  .on('move', 'div#familiaArea', function(e, i) {

    // console.log(e);
    // console.log($(this).get(0).scrollHeight > $(this).height());

    // Verifica si el elemento tiene scroll.
    if ($(this).get(0).scrollHeight > $(this).height()) {

      // Calcula la cantidad a moverse hacia la izquiera o derecha.
      var move = Math.abs(100 * (e.distY / $(this).height()));

      topPos = $(this).scrollTop(); // Obtiene la posicion actual del scroll.

      // console.log(move);
      // console.log(e.distX + ' & ' + lastMove);

      // Si el movimiento es hacia la izquierda entonces mueve el scroll hacia
      // la derecha.
      if ((e.distY < 0 && e.distY < lastMove) || (lastMove > e.distY && e.distY > 0)) {

        // Si el movimiento realizado fue derecha a izquierda recalcula la cantidad
        // hacia la izquierda.
        if (lastMove > e.distY && e.distY > 0) move = lastMove - e.distY;

        // Mueve el scroll.
        $(this).scrollTop(topPos + move);

        // Asigna el ultimo movimiento.
        lastMove = e.distY;

        // console.log('izq');
      }

      if ((e.distY > 0 && e.distY > lastMove) || (lastMove < e.distY && e.distY < 0)) {
        // Si el movimiento es hacia la derecha entonces mueve el scroll hacia
        // la izquierda.

        // Si el movimiento realizado fue derecha a izquierda recalcula la cantidad
        // hacia la derecha.
        if (lastMove < e.distY && e.distY < 0) move = Math.abs(lastMove) + e.distY;

        // Mueve el scroll.
        $(this).scrollTop(topPos - move);

        // Asigna el ultimo movimiento.
        lastMove = e.distY;

        // console.log('der');
      }

    }

  })
  .on('moveend', 'div#familiaArea', function(e) {
    // console.log('moveend');
  });
};


/**
 * Obtiene el Alto visible del browser y lo reasigna a los elementos del
 * ticket y de las familias&productos
 */
var setDynamicHeight = function() {
  var browserVisibleHeight = $(window).outerHeight(), // Alto visible del browser
      titleHeight = $('.text-title').outerHeight(), // Alto de los titulos
      totalAreaHeight = $('#totalArea').outerHeight(), // Alto del Total
      calcAreaHeight = $('#calcArea').outerHeight(); // Alto de la calculadora

      // console.log('browser: ' + browserVisibleHeight);
      // console.log('titulos: ' + titleHeight);
      // console.log('total: ' + totalAreaHeight);
      // console.log('calculadora: ' + calcAreaHeight);

  // Si el Alto visible del browser es mayor a 0 entra
  if (browserVisibleHeight > 0) {
    // alert(browserVisibleHeight);

    // Reasigna el height y max-height de familias&productos
    $('#ticketTotalCalcArea').css({'height': browserVisibleHeight});
    $('#ticketArea').css({'height': browserVisibleHeight - titleHeight - totalAreaHeight - calcAreaHeight - 15});

    // Reasigna el height y max-height de familias&productos
    $('#productosArea').css({
      'height': browserVisibleHeight,
      'max-height': browserVisibleHeight
    });

    $('div#familiaArea').css({
      'height': browserVisibleHeight - titleHeight,
      'max-height': browserVisibleHeight - titleHeight
    });
    $('.myClass').css({
      'height': browserVisibleHeight - titleHeight ,
      'max-height': browserVisibleHeight - titleHeight
    });

    styleP = 'style="height: ' + (browserVisibleHeight - titleHeight) + 'px; max-height: '+ (browserVisibleHeight - titleHeight) +'px;"';
  }
};

/**
 * Recibe data tipo json y contruye los tags <li> y los retorna
 */
var build_padres_hijos = function(data) {
  var li_html = '', i, tiene_imagen, jsonProdBaseFam, jsonStr;
  // cliclo for para constriuir los elementos <li></li> de los productos o familias

  li_html = '<div class="span3 familia-row" id="familiaArea" '+styleP+'> '+
              '<ul class="thumbnails">';
  for (i in data) {

    jsonProdBaseFam = [];
    jsonStr = '';

    // console.log(data[i]);
    tiene_imagen = false;
    if (data[i].imagen !== null && data[i].imagen !== '') {
      tiene_imagen = true;
    }

    if (data[i].hasOwnProperty('productos_base_fam')) {
      data[i].productos_base_fam.forEach(function (e, i) {
        // jsonProdBaseFam.push('"'+e.base_id+'" : ' + JSON.stringify(e).replace('"', " "));
        jsonProdBaseFam.push("'"+e.base_id+"' : {'base_id':'"+e.base_id+"', 'cantidad': '"+e.cantidad+"', 'precio_compra' :'" + e.precio_compra+"', 'nombre': '"+e.nombre+"'}");

      });
      jsonStr = '{' + jsonProdBaseFam.join(', ') + '}';
    }

    // console.log(jsonStr);

    li_html += '<li class="span12" id="item" data-id-padre="'+data[i].id_padre+'" data-id="'+data[i].id+'" data-last-nodo="'+data[i].ultimo_nodo+'" data-precio="'+data[i].precio_venta+'" data-pbf="'+jsonStr+'">' +
               '<div class="thumbnail" '+ ((data[i].color1 !== null && data[i].color2 !== null) ? 'style="background: -webkit-linear-gradient(top,  ' + data[i].color1 + ' 0%, ' + data[i].color2 + ' 100%);"' : '') + '>' +
                 '<div class="caption" ' + ((!tiene_imagen) ? 'style="display: table;"' : '') + '>' +
                   ' ' + ((tiene_imagen) ? '<img src="' + base_url + 'application/images/familias/' + data[i].imagen + '" width="80" height="80">' : '') +

                   '<p ' + ((!tiene_imagen) ? 'style="vertical-align: middle; display: table-cell;"' : '') + '>' + data[i].nombre + '</p>' +
                 '</div>' +
               '</div>' +
             '</li>';
  }
  li_html += '</ul></div>';

  return li_html;
};

/**
 * Calcula el total de la venta
 */
var calcula_total = function() {
  var ttotal = 0;
  $('input#itotal').each(function(event) {
    ttotal += parseFloat($(this).val());
  });

  $('#ttotal').html(util.darFormatoNum(ttotal));
  $('#itotalv').val(ttotal);
};

/**
 * Construye los items del listado de productos
 * @param  obj [Objeto con los datos del producto/item]
 * @return void
 */
var buildItemList = function(obj) {
    var item = obj,
        fromCalculadora = calculadoraPv.fromCalculadora(); // Sirve para saber si existe un valor desde la calculadora

    if ($('#tr' + item.id).length === 0) { // Si el Producto no existe en el listado

      var cantidad = 1;
      if (fromCalculadora) { // Valida si existe una canti dad desde la calculadora
        cantidad = calculadoraPv.getValor(); // Obtiene la cantidad
      }

      var tr_html = '<tr id="tr' + item.id + '" data-pbf="'+item.pbf+'">' +
                      '<td id="td-nombre">' + item.nombre +
                          '<input type="hidden" id="iid" value="' + item.id + '">' +
                          '<input type="hidden" id="icantidad" value="' + cantidad + '">' +
                          '<input type="hidden" id="iprecio" value="' + item.precio + '">' +
                          '<input type="hidden" id="itotal" value="' + (cantidad * parseFloat(item.precio)) + '">' +
                          '<input type="hidden" id="inombre" value="' + item.nombre + '">' +
                      '</td>' +
                      '<td id="td-cantidad">' + cantidad + '</td>' +
                      '<td id="td-total">' + util.darFormatoNum(cantidad * parseFloat(item.precio)) + '</td>' +
                      '<td><input type="text" value="0" class="vpos-int" id="iiDesc' + item.id + '" style="width: 25px;"/></td>' +
                    '</tr>';

      $(tr_html).appendTo('#table-listado').find('tbody');

      $(".vpos-int").removeNumeric();
      $(".vpos-int").numeric({ decimal: false, negative: false });

    } else { // Si el producto existe en el listado

      var new_cantidad = 1;
      if (fromCalculadora) { // Valida si existe una cantidad desde la calculadora
        new_cantidad = calculadoraPv.getValor(); // Obtiene la cantidad
      }

      var tr = $('#tr' + item.id),
          cantidad = parseInt(tr.find('#icantidad').val(), 10) + new_cantidad;

      tr.find('#td-cantidad').html(cantidad);
      tr.find('#icantidad').val(cantidad);
      tr.find('#td-total').html(util.darFormatoNum(cantidad * parseFloat(item.precio)));
      tr.find('#itotal').val(cantidad * parseFloat(item.precio));


      calcDescItemList(tr.find('#iiDesc' + item.id));

    }

    calcula_total();
};


var calcDescItemList = function(obj) {
  var input = $(obj), // Obj del input del descuento
      desc = $(input).val(),

      inputCant   = input.parent().parent().find('#icantidad'), // Obj del input del total
      inputPrecio = input.parent().parent().find('#iprecio'), // Obj del input del total
      inputTotal  = input.parent().parent().find('#itotal'), // Obj del input del total

      total = parseFloat(inputCant.val()) * parseFloat(inputPrecio.val()),
      totalDesc = 0;

      inputTotal.val(total);

  if (desc === '') desc = 0;

  if (parseInt(desc, 10) < 0 || parseInt(desc, 10) > 100) {

    desc = desc.slice(0, desc.length - 1); // Elimina el ultimo caracter de la cadena
    input.val(desc);

    noty({"text": 'El descuento no puede ser mayor a 100', "layout":"topRight", "type": 'error'});
  }

  var total_sin_iva = parseFloat(inputTotal.val())/1.16;
  desc = total_sin_iva * (parseFloat(desc) / 100); // Calcula el descuento real
  total = parseFloat((total_sin_iva - desc)*1.16).toFixed(2);
  inputTotal.val(total);

  calcula_total();
};

var calculadoraPv  = (function($){

  var out = {}, // Objeto para almacenar los metodos que estaran publicos
      arrayCant = []; // array que contendra la cantidad de veces que se agregara un producto. ['5', '*']

  /**
   * Inicializa la Calculadora
   */
  var initialize = function() {
    $('#calcArea').find("button").on('click', function(event) {
      detectButton(this);
    });
    // keyboard();
  };

  /**
   * Asigan metodos segun el tipo de boton detectado
   */
  var detectButton = function(button) {
    var val = $(button).html();

    switch (val) {
      case '0':
      case '1':
      case '2':
      case '3':
      case '4':
      case '5':
      case '6':
      case '7':
      case '8':
      case '9':
                setCantidad(val);
                break;
      case '*':
                setSignoPor();
                break;
      case '-':
                delItem();
                break;
      case 'Supr':
                suprItem();
                break;
      case 'C':
                clearLista();
                break;
      case 'ENTER':
                terminarVenta();
                break;
      default:
                noty({"text": 'Boton no validado!', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Metodo para los botonos 1 al 9
   * Contruye el array que almacenara la cantidad de veces que se agregara un
   * producto al darle click (touch)
   *
   * @return void
   */
  var setCantidad = function(num) {
    var leng = arrayCant.length;
    if (leng === 2) {
      arrayCant = [num];
    } else if (leng === 1){
      arrayCant[0] = arrayCant[0] + '' + num;
    } else {
      arrayCant.push(num)
    }
  };

  /**
   * Metodo para el boton "*"
   *
   * @return void
   */
  var setSignoPor = function() {
    if (arrayCant.length !== 0) {
      if (arrayCant.length !== 2) {
        arrayCant.push("*");
      } else {
        noty({"text": 'Seleccione un Producto', "layout":"topRight", "type": 'error'});
      }
    } else {
      noty({"text": 'Seleccione una Cantidad', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Retorna el arrayCant
   * @return {Array}
   */
  var getArrayCant = function() {
    return arrayCant;
  };

  /**
   * Retorna la cantidad que se almaceno desde la calculadora
   *
   * @return Int
   */
  var getValor = function() {
    var val = arrayCant[0];
    arrayCant = [];
    return parseInt(val, 10);
  };

  /**
   * Valida si el arrayCant esta contruido correctamente
   *
   * @return boolean
   */
  var isCantidadValida = function() {
    if (arrayCant.length === 2) {
      return true;
    } else {
      return false;
    }
  };

  /**
   * Valida si se pasara un valor desde la calculadora
   *
   * @return boolean
   */
  var existValFromCalc = function() {
    if (arrayCant.length > 0) {
      return true;
    } else {
      return false;
    }
  };

  /**
   * Decrementa la cantidad de un Item en 1 y se recalcula el total, si el item
   * llega a ser 0 se elimina del listado.
   *
   * @return void
   */
  var suprItem = function() {
    var item = $('.clicked'),
        parent = item.parent();

    if (item.length !== 0) { // Si Existe un Item del listado seleccionado
      new_cantidad = parseInt(parent.find('#icantidad').val(), 10) - 1; // Obtiene la cantidad del listado y le resta 1

      if (new_cantidad === 0) { // Si la cantidad es 0 entonces lo elimina del listado
        parent.remove();
      } else {
       parent.find('#icantidad').val(new_cantidad);
       parent.find('#td-cantidad').html(new_cantidad);

       parent.find('#itotal').val(new_cantidad * parseFloat($(parent).find('#iprecio').val()));
       parent.find('#td-total').html(util.darFormatoNum(new_cantidad * parseFloat($(parent).find('#iprecio').val())));
      }
      calcula_total(); // Recalcula el total de la venta
    } else { // Si no existe ningun item del listado seleccionado muestra un msg
      noty({"text": 'Seleccione un item del listado para poder realizar la operación', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Incrementa la cantidad de un Item en 1 y se recalcula el total
   *
   * @return void
   */
  var incrementItem = function() {
    var item = $('.clicked'),
        parent = item.parent();

    if (item.length !== 0) { // Si Existe un Item del listado seleccionado
      new_cantidad = parseInt(parent.find('#icantidad').val(), 10) + 1; // Obtiene la cantidad del listado y le resta 1

       parent.find('#icantidad').val(new_cantidad);
       parent.find('#td-cantidad').html(new_cantidad);

       parent.find('#itotal').val(new_cantidad * parseFloat($(parent).find('#iprecio').val()));
       parent.find('#td-total').html(util.darFormatoNum(new_cantidad * parseFloat($(parent).find('#iprecio').val())));

      calcula_total(); // Recalcula el total de la venta
    } else { // Si no existe ningun item del listado seleccionado muestra un msg
      noty({"text": 'Seleccione un item del listado para poder realizar la operación', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Elimina un Item del listado completamente.
   *
   * @return void
   */
  var delItem = function() {
    var item = $('.clicked');

    if (item.length !== 0) {
      item.parent().remove();
      calcula_total();
    } else {
      noty({"text": 'Seleccione un item del listado para poder realizar la operación', "layout":"topRight", "type": 'error'});
    }
  }

  /**
   * Limpia el listado, elimina todo lo que contenga.
   *
   * @return void
   */
  var clearLista = function() {
    $('#table-listado').find('tbody').html('');
    calcula_total();
  };

  /**
   * Limpia el listado, elimina todo lo que contenga.
   *
   * @return void
   */
  var terminarVenta = function() {
    if ($('#table-listado').find('tbody tr').length > 0) {

      $('#myModal').modal('toggle');

      var iva = (parseFloat($('#itotalv').val()) - (parseFloat($('#itotalv').val())/1.16)).toFixed(2),
          suttotal_no_iva = (parseFloat($('#itotalv').val()) - iva).toFixed(2);

      $('#itvtvsubtotal_no_iva').val(suttotal_no_iva);
      $('#tvsubtotal_no_iva').html(util.darFormatoNum(suttotal_no_iva));

      $('#itviva').val(iva);
      $('#tviva').html(util.darFormatoNum(iva));

      $('#tvtotal').html(util.darFormatoNum($('#itotalv').val()));

      $('#itvtotal-modal').val($('#itotalv').val());

      $('#tvrecibido').html('$0.00');
      $('#itvrecibido').val(0);

      $('#tvcambio').html('$0.00');
      $('#itvcambio').val(0);

      calcTouch = false;

      calculadora.reset();

    } else {
      noty({"text": 'Agrege Items/Productos al listado', "layout":"topRight", "type": 'error'});
    }
  };

  /**
   * Lanza metodos segun la tecla presionada desde el Teclado
   *
   * @return void
   */
  var keyboard = function (e) {

    // if (calcTouch) {
      // $(window).keydown(function(e) {
        var key = e.which;

        // Tecla del 0 al 9
        if (key > 47 && key < 58) setCantidad(String.fromCharCode(e.keyCode));

        // flecha arriba
        if (key === 38) incrementItem();

        // Flecha abajo
        if (key === 40) suprItem();

        // Tecla Enter
        if (key === 13) terminarVenta();

        // Tecla Supr o Del
        if (key === 46) clearLista();

        // Tecla Borrar
        if (key === 8) delItem();

    //   });
    // }

  };

  // Declara los metodos que seran publicos
  out.init            = initialize;
  out.getArrayCant    = getArrayCant;
  out.fromCalculadora = existValFromCalc;
  out.isValido        = isCantidadValida;
  out.getValor        = getValor;

  out.teclado         = keyboard;

  return out;
})(jQuery);
