$(document).ready(function() {
	function selectField(e) {
		var side = e.data.side, id = $(this).val();

		var field = window["OphInVisualfields_available_fields_" + side][id];

		$('#Element_OphInVisualfields_Image_image_' + side + ' img').attr('src', field.url);
		$('#Element_OphInVisualfields_Image_image_' + side).data('image-id', field.image_id);
		$('#Element_OphInVisualfields_Image_strategy_' + side).text(field.strategy);
		$('#Element_OphInVisualfields_Image_pattern_' + side).text(field.pattern);
	}
	
	$('#Element_OphInVisualfields_Image_right_field_id').change({side: "right"}, selectField);
	$('#Element_OphInVisualfields_Image_left_field_id').change({side: "left"}, selectField);

	$('.OphInVisualfields_field_image').click(function (e) {
		e.preventDefault();

		var imgUrl = baseUrl + '/file/view/' + $(this).data('image-id') + '/img.gif';

		var dialog = new OpenEyes.UI.Dialog({
			content: '<img src="' + imgUrl + '" style="width: 100%;">',
			width: 1100,
			position: {my: "center top", at: "center top+10"},
			autoOpen: true,
			modal: false
		}).open();
	});

});
