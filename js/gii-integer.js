$(document).ready(function() {
	$('input[name^="integerMinValue"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^integerMinValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#integerMaxValue'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="integerMaxValue"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^integerMaxValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#integerDefaultValue'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="integerDefaultValue"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^integerDefaultValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#integerSize'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="integerSize"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^integerSize([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#integerMaxLength'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="integerMaxLength"]').die('keypress').live('keypress',function(e) {
		return (e.keyCode != 13);
	});
});
