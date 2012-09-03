$(document).ready(function() {
	$('select.MultiSelectList').unbind('change').bind('change',function() {
		var selected = $(this).children('option:selected');

		if (selected.val().length >0) {
			$(this).parent().children('div').children('ul').append('<li>'+selected.text()+' (<a href="#" class="MultiSelectRemove '+selected.val()+'">remove</a>)</li>');

			var element_class = $(this).attr('name').replace(/\[.*$/,'');

			var m = $(this).parent().parent().prev('input').attr('name').match(/\[MultiSelectList_(.*?)\]/);
			var multiSelectField = m[1];

			$(this).parent().children('div').children('ul').append('<input type="hidden" name="'+multiSelectField+'[]" value="'+selected.val()+'" />');

			selected.remove();

			$(this).val('');
		}

		return false;
	});

	$(this).undelegate('a.MultiSelectRemove','click').delegate('a.MultiSelectRemove','click',function(e) {
		e.preventDefault();

		var value = $(this).parent().next().val();
		var text = $(this).parent().text().trim().replace(/ \(.*$/,'');

		var select = $(this).parent().parent().parent().parent().children('select');

		select.append('<option value="'+value+'">'+text+'</option>');

		sort_selectbox(select);

		$(this).parent().next().remove();
		$(this).parent().remove();

		return false;
	});
});
