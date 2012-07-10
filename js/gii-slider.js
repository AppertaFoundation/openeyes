$(document).ready(function() {
	$('input[name^="sliderMinValue"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^sliderMinValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#sliderMaxValue'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="sliderMaxValue"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^sliderMaxValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#sliderDefaultValue'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="sliderDefaultValue"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^sliderDefaultValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#sliderStepping'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="sliderStepping"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^sliderStepping([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#sliderForceDP'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[name^="sliderForceDP"]').die('keypress').live('keypress',function(e) {
		return (e.keyCode != 13);
	});
});
