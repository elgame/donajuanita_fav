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

	<div class="container-fluid">
		<div class="row-fluid">
			<!--[if lt IE 7]>
        <div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<p>Usted está usando un navegador desactualizado. <a href="http://browsehappy.com/">Actualice su navegador</a> o <a href="http://www.google.com/chromeframe/?redirect=true">instale Google Chrome Frame</a> para experimentar mejor este sitio.</p>
				</div>
      <![endif]-->


		<div id="content" class="span12">
			<!-- content starts -->

			<div class="row-fluid">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> Agregar Inventario</h2>
						<div class="box-icon">
							<a href="#" class="btn" title="Cerrar ventana" onclick="parent.supermodal.close();"><i class="icon-remove"></i></a>
						</div>
					</div>
					<div class="box-content">
						<form action="<?php echo base_url('panel/productos/agregar_inventario/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="form-horizontal" id="form">
						  <fieldset style="padding: 0 5px;">
								<legend></legend>

								<div class="span11">

									<div class="control-group">
	                  <label class="control-label">Producto </label>
	                  <div class="controls">
	                    <strong><?php echo (isset($producto->nombre)? $producto->nombre: ''); ?></strong>
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="dcantidad">*Cantidad </label>
	                  <div class="controls">
	                    <input type="text" name="dcantidad" id="dcantidad" value="<?php echo set_value('dcantidad'); ?>" 
	                    	class="input-xlarge vnumeric" placeholder="5, 1, 10 (unidades)">
	                  </div>
	                </div>

	                <div class="control-group">
	                  <label class="control-label" for="dprecio_compra">*Precio compra </label>
	                  <div class="controls">
	                    <input type="text" name="dprecio_compra" id="dprecio_compra" value="<?php echo set_value('dprecio_compra'); ?>"
	                    	class="input-large vnumeric" placeholder="30, 20, 3 (pesos)">
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


		
	</div><!--/fluid-row-->

</div><!--/.fluid-container-->

	<div class="clear"></div>

</body>
</html>