$(function(){

  $("#ffecha1").datepicker({
       dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
       //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
       changeMonth: true, //permite modificar los meses (true o false)
       changeYear: true, //permite modificar los años (true o false)
       //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
       numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
     });

  $("#ffecha2").datepicker({
       dateFormat: 'yy-mm-dd', //formato de la fecha - dd,mm,yy=dia,mes,año numericos  DD,MM=dia,mes en texto
       //minDate: '-2Y', maxDate: '+1M +10D', //restringen a un rango el calendario - ej. +10D,-2M,+1Y,-3W(W=semanas) o alguna fecha
       changeMonth: true, //permite modificar los meses (true o false)
       changeYear: true, //permite modificar los años (true o false)
       //yearRange: (fecha_hoy.getFullYear()-70)+':'+fecha_hoy.getFullYear(),
       numberOfMonths: 1 //muestra mas de un mes en el calendario, depende del numero
     });

  // $(".btn-group .btn").on('click', function(){
  //   var btn_grp = $(this).parents('.btn-group');
    
  // });

  if($("#did_vendedor").length > 0){
    $("#dvendedor").autocomplete({
        source: base_url+'panel/usuarios/ajax_get_usuario/',
        minLength: 1,
        selectFirst: true,
        select: function( event, ui ) {
          $("#did_vendedor").val(ui.item.id);
          $("#dvendedor").css("background-color", "#B0FFB0");
        }
    }).on("keydown", function(event){
        if(event.which == 8 || event == 46){
          $("#dvendedor").css("background-color", "#FFD9B3");
          $("#did_vendedor").val("");
        }
    });
  }

  if($("#did_proveedor").length > 0){
    $("#dproveedor").autocomplete({
        source: base_url+'panel/proveedores/ajax_get_proveedor/',
        minLength: 1,
        selectFirst: true,
        select: function( event, ui ) {
          $("#did_proveedor").val(ui.item.id);
          $("#dproveedor").css("background-color", "#B0FFB0");
        }
    }).on("keydown", function(event){
        if(event.which == 8 || event == 46){
          $("#dproveedor").css("background-color", "#FFD9B3");
          $("#did_proveedor").val("");
        }
    });
  }
});