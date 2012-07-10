$(document).ready(function() {
	$('input[name^="textBoxSize"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^textBoxSize([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#textBoxMaxLength'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="textBoxMaxLength"]').die('keypress').live('keypress',function(e) {
		return (e.keyCode != 13);
	});
});
