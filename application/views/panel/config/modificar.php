		<div id="content" class="span10">
			<!-- content starts -->
			

			<div>
				<ul class="breadcrumb">
					<li>
						<a href="<?php echo base_url('panel'); ?>">Inicio</a> <span class="divider">/</span>
					</li>
					<li>Configuración</li>
				</ul>
			</div>

			<form action="<?php echo base_url('panel/config/?'.String::getVarsLink(array('msg'))); ?>" method="post" class="form-horizontal" 
				enctype="multipart/form-data">
				<div class="row-fluid">
					<div class="box span12">
						<div class="box-header well" data-original-title>
							<h2><i class="icon-list-alt"></i> Configuración</h2>
							<div class="box-icon">
								<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							</div>
						</div>
						<div class="box-content">
							  <fieldset>
									<legend></legend>

									<div class="span6 mquit">
										<div class="control-group">
											<label class="control-label" for="dnombre">Nombre:</label>
											<div class="controls">
												<input type="text" name="dnombre" id="dnombre" class="span12" 
													value="<?php echo (isset($info->nombre)? $info->nombre: ''); ?>" maxlength="150" autofocus>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="drazon_social">Nombre Fiscal:</label>
											<div class="controls">
												<input type="text" name="drazon_social" id="drazon_social" class="span12" 
													value="<?php echo (isset($info->razon_social)? $info->razon_social: ''); ?>" maxlength="150">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="drfc">RFC:</label>
											<div class="controls">
												<input type="text" name="drfc" id="drfc" class="span12" 
													value="<?php echo (isset($info->rfc)? $info->rfc: ''); ?>" maxlength="15">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcalle">Calle:</label>
											<div class="controls">
												<input type="text" name="dcalle" id="dcalle" class="span12" 
													value="<?php echo (isset($info->calle)? $info->calle: ''); ?>" maxlength="100">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dno_exterior">No exterior:</label>
											<div class="controls">
												<input type="text" name="dno_exterior" id="dno_exterior" class="span12" 
													value="<?php echo (isset($info->num_ext)? $info->num_ext: ''); ?>" maxlength="12">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dno_interior">No interior:</label>
											<div class="controls">
												<input type="text" name="dno_interior" id="dno_interior" class="span12" 
													value="<?php echo (isset($info->num_int)? $info->num_int: ''); ?>" maxlength="12">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcolonia">Colonia:</label>
											<div class="controls">
												<input type="text" name="dcolonia" id="dcolonia" class="span12" 
													value="<?php echo (isset($info->colonia)? $info->colonia: ''); ?>" maxlength="100">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dmunicipio">Municipio / Delegación:</label>
											<div class="controls">
												<input type="text" name="dmunicipio" id="dmunicipio" class="span12" 
													value="<?php echo (isset($info->municipio)? $info->municipio: ''); ?>" maxlength="100">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="destado">Estado:</label>
											<div class="controls">
												<input type="text" name="destado" id="destado" class="span12" 
													value="<?php echo (isset($info->estado)? $info->estado: ''); ?>" maxlength="100">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcp">CP:</label>
											<div class="controls">
												<input type="text" name="dcp" id="dcp" class="span12" 
													value="<?php echo (isset($info->cp)? $info->cp: ''); ?>" maxlength="20">
											</div>
										</div>

									</div> <!--/span-->

									<div class="span6 mquit">
										<div class="control-group">
											<label class="control-label" for="dtelefono">Teléfono:</label>
											<div class="controls">
												<input type="text" name="dtelefono" id="dtelefono" class="span12" 
													value="<?php echo (isset($info->telefono)? $info->telefono: ''); ?>" maxlength="50">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dpag_web">Pag Web:</label>
											<div class="controls">
												<input type="text" name="dpag_web" id="dpag_web" class="span12" 
													value="<?php echo (isset($info->pag_web)? $info->pag_web: ''); ?>" maxlength="130">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="demail">Email:</label>
											<div class="controls">
												<input type="text" name="demail" id="demail" class="span12" 
													value="<?php echo (isset($info->email)? $info->email: ''); ?>" maxlength="100">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dfooter">Texto al final del Ticket:</label>
											<div class="controls">
												<textarea name="dfooter" id="dfooter" class="span12" rows="5"><?php echo (isset($info->footer)? $info->footer: ''); ?></textarea>
											</div>
										</div>

										<div class="control-group">
											<label for="dlogo" class="control-label">Logo</label>
											<div class="controls">
												<input type="file" name="dlogo" id="dlogo" size="30">
												<img style="float: right; height: 50px;" src="<?php echo (isset($info->url_logo)? base_url($info->url_logo): ''); ?>">
											</div>
										</div>

										<div class="control-group">
											<label for="durl_logop" class="control-label">Imp. Logo en Ticket</label>
											<div class="controls">
												<?php $logp = (isset($info->url_logop)? $info->url_logop: 'false') ?>
												<input type="checkbox" name="durl_logop" id="durl_logop" value="true" <?php echo set_checkbox('durl_logop', 'true', ($logp=='true'? true: false)) ?>>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcolor_1">Color 1:</label>
											<div class="controls">
												<input type="text" name="dcolor_1" id="dcolor_1" class="span6" 
													value="<?php echo (isset($info->color_1)? $info->color_1: ''); ?>" maxlength="15">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dcolor_2">Color 2:</label>
											<div class="controls">
												<input type="text" name="dcolor_2" id="dcolor_2" class="span6" 
													value="<?php echo (isset($info->color_2)? $info->color_2: ''); ?>" maxlength="15">
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="dfuente_pv">Tamaño de fuente en punto de venta:</label>
											<div class="controls">
												<input type="number" name="dfuente_pv" id="dfuente_pv" class="span6" 
													value="<?php echo (isset($info->fuente_pv)? $info->fuente_pv: ''); ?>" max="26" min="10">
											</div>
										</div>

		              </div> <!--/span-->

							  </fieldset>

						</div>
					</div><!--/box span-->

				</div><!--/row-->
				
				<div class="form-actions">
				  <button type="submit" class="btn btn-primary">Guardar</button>
				  <a href="<?php echo base_url('panel/clientes/'); ?>" class="btn">Cancelar</a>
				</div>

			</form>
				  
       
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



