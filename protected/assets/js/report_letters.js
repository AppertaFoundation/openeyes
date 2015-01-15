
$(document).ready(function() {
	$('#add_letter_phrase').click(function(e) {
		e.preventDefault();

		$('.phraseList').append('<div><input type="text" name="phrases[]" value="" /> <a href="#" class="removePhrase">remove</a></div>');
		$('.phraseList').find('input[name="phrases[]"]:last').focus();
	});

	$('.removePhrase').die('click').live('click',function(e) {
		e.preventDefault();

		$(this).closest('div').remove();
	});
});
