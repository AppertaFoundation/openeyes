$(document).ready(function() {
	$('select.MultiSelectList').unbind('change').bind('change',function() {
		var selected = $(this).children('option:selected');

		$(this).parent().children('div').children('ul').append('<li>'+selected.text()+' (<a href="#" class="MultiSelectRemove '+selected.val()+'">remove</a>)</li>');

		$(this).parent().children('input').map(function() {
			if ($(this).attr('name').match(new RegExp('^Element[a-zA-Z]+\\['+selected.val()+'\\]$'))) {
				$(this).val(1);
			}
		});

		selected.remove();

		$(this).val('');

		return false;
	});

	$('a.MultiSelectRemove').die('click').live('click',function(e) {
		e.preventDefault();

		var value = $(this).attr('class').replace(/^MultiSelectRemove /,'');
		var text = $(this).parent().text().replace(/ \(.*$/,'');

		$(this).parent().parent().parent().parent().children('input').map(function() {
			if ($(this).attr('name').match(new RegExp('^Element[a-zA-Z]+\\['+value+'\\]$'))) {
				$(this).val(0);
			}
		});

		var select = $(this).parent().parent().parent().parent().children('select');

		select.append('<option value="'+value+'">'+text+'</option>');

		sort_selectbox(select);

		$(this).parent().remove();

		return false;
	});
});
