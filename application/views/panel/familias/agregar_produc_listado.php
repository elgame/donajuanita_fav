<?php if(isset($productosr)){ ?>
<div style="height: 260px;overflow-y:auto;">
	<table id="tbl-pr" class="table table-condensed">
		<tbody>
<?php foreach($productosr['productos'] as $produc){ ?>
		<tr class="tr-producreg" data-id="<?php echo $produc->id; ?>">
			<td><?php echo $produc->nombre; ?></td>
		</tr>
<?php } ?>
		</tbody>
	</table>
</div>
<?php
	//Paginacion
	$this->pagination->initialize(array(
			'base_url' 			=> "",
			'javascript'		=> "javascript:buscarProductos({pag});",
			'total_rows'		=> $productosr['total_rows'],
			'per_page'			=> $productosr['items_per_page'],
			'cur_page'			=> $productosr['result_page']*$productosr['items_per_page'],
			'page_query_string'	=> TRUE,
			'num_links'			=> 1,
			'anchor_class'	=> 'pags corner-all',
			'num_tag_open' 	=> '<li>',
			'num_tag_close' => '</li>',
			'cur_tag_open'	=> '<li class="active"><a href="#">',
			'cur_tag_close' => '</a></li>'
	));
	$pagination = $this->pagination->create_links();
	if ($pagination != '')
		echo '<div class="pagination pagination-centered"><ul>'.$pagination.'</ul></div>';
}
?>