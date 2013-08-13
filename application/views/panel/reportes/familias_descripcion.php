<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="es" class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title><?php echo $seo['titulo'];?></title>
  <meta name="description" content="<?php echo $seo['titulo'];?>">
  <meta name="viewport" content="width=device-width">

<?php
  if(isset($this->carabiner)){
    $this->carabiner->display('css');
    $this->carabiner->display('base_panel');
    $this->carabiner->display('js');
  }
?>

  <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

<script type="text/javascript" charset="UTF-8">
  var base_url = "<?php echo base_url();?>";
</script>
</head>
<body>

  <div class="container-fluid" style="padding-left: 0px;">
    <div class="row-fluid">
      <!--[if lt IE 7]>
        <div class="alert alert-info">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <p>Usted está usando un navegador desactualizado. <a href="http://browsehappy.com/">Actualice su navegador</a> o <a href="http://www.google.com/chromeframe/?redirect=true">instale Google Chrome Frame</a> para experimentar mejor este sitio.</p>
        </div>
      <![endif]-->

    <div class="span3">
      <form action="<?php echo base_url('panel/reportes/familias_descripcion_pdf'); ?>" method="GET" target="rvcReporte">
        <fieldset>
          <legend>Filtros</legend>

          <div class="input-prepend">
            <span class="add-on" style="margin-right: -4px;">Descripción</span>
            <input type="text" name="ddescripcion" class="span9" id="ddescripcion" value="<?php echo set_value('ddescripcion'); ?>" placeholder="descripcion">
          </div>

          <div class="form-actions" style="margin-bottom: 0px;padding-bottom: 0px;">
            <input type="submit" name="enviar" value="Enviar" class="btn">
          </div>
        </fieldset>
      </form>
    </div> 

    <div id="content" class="span9">
      <!-- content starts -->

      <div class="row-fluid">
        <div class="box span12">
          <div class="box-header well" data-original-title>
            <h2><i class="icon-file"></i> <?php echo $seo['titulo'];?></h2>
            <div class="box-icon">
              <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
            </div>
          </div>
          <div class="box-content">
            <div class="row-fluid">
              <iframe name="rvcReporte" id="iframe-reporte" class="span12" 
                src="<?php echo base_url('panel/reportes/familias_descripcion_pdf')?>" style="height:600px;">dd</iframe>
            </div>

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

    
  </div><!--/fluid-row-->

</div><!--/.fluid-container-->

  <div class="clear"></div>

</body>
</html>