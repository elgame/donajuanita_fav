$(function(){
	var color1 = $("#dcolor_1"), color2 = $("#dcolor_2");
	color1.ColorPicker({  			//colorpicker del color de fondo
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
			color1.css("background-color", "#"+hex);
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			color1.val(hex).css('backgroundColor', '#' + hex);
		}
	}).on('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
		color1.css("background-color", "#"+this.value);
	});

	color2.ColorPicker({  			//colorpicker del color de fondo
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
			color2.css("background-color", "#"+hex);
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			color2.val(hex).css('backgroundColor', '#' + hex);
		}
	}).on('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
		color2.css("background-color", "#"+this.value);
	});

});