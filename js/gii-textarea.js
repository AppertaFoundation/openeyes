$(document).ready(function() {
	$('input[id^="textAreaRows"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('id').match(/^textAreaRows([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#textAreaCols'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[id^="textAreaCols"]').die('keypress').live('keypress',function(e) {
		return (e.keyCode != 13);
	});
});
