$(document).ready(function() {
	$('.eyeDrawDoodleSelect').live('change',function() {
		var doodle = $(this).children('option:selected').val();
		$('.selectedToolbarDoodles').append('<div><input type="hidden" name="eyedrawToolbarDoodle'+$(this).attr('data-attr-element')+'Field'+$(this).attr('data-attr-field')+'[]" value="'+doodle+'" />'+doodle+' <a href="#" class="removeDoodle">(remove)</a></div>');
		$(this).children('option:selected').remove();
	});

	$('.removeDoodle').live('click',function(e) {
		var value = $(this).parent().children('input').val();
		var select = $(this).parent().parent().prev('div').prev('br').prev('select');
		select.append('<option value="'+value+'">'+value+'</option>');
		sort_selectbox(select);
		$(this).parent().remove();
		e.preventDefault();
	});

	$('.eyeDrawDefaultDoodleSelect').live('change',function() {
		var doodle = $(this).children('option:selected').val();
		$('.selectedDefaultDoodles').append('<div><input type="hidden" name="eyedrawDefaultDoodle'+$(this).attr('data-attr-element')+'Field'+$(this).attr('data-attr-field')+'[]" value="'+doodle+'" />'+doodle+' <a href="#" class="removeDoodle">(remove)</a></div>');
		$(this).children('option:selected').remove();
	});
});
