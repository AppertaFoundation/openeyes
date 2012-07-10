$(document).ready(function() {
	$('input[id^="textAreaDropDownRows"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('id').match(/^textAreaDropDownRows([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('#textAreaDropDownCols'+element+'Field'+field).select().focus();
			return false;
		}

		return true;
	});

	$('input[id^="textAreaDropDownCols"]').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('id').match(/^textAreaDropDownCols([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="textAreaDropDownFieldValue'+element+'Field'+field+'_1"]').select().focus();
			return false;
		}

		return true;
	});

	$('.textAreaDropDownFieldValuesAddValue').live('click',function() {
		var m = $(this).attr('name').match(/^textAreaDropDownFieldValuesAddValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		var i = 1;

		$(this).prev().children('input[type="text"]').map(function() {
			var m = $(this).attr('name').match(/_([0-9]+)$/);
			if (parseInt(m[1]) > i) {
				i = parseInt(m[1]);
			}
		});

		i += 1;

		$('#textAreaDropDownFieldValues'+element+'Field'+field).append('<input type="text" class="textAreaDropDownFieldValueTextInput" name="textAreaDropDownFieldValue'+element+'Field'+field+'_'+i+'" value="Enter value" /><input type="submit" class="textAreaDropDownFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="textAreaDropDownFieldValue'+element+'Field'+field+'_'+i+'"]').select().focus();

		return false;
	});

	$('.textAreaDropDownFieldValuesRemoveValue').live('click',function() {
		var prevText = $(this).prev().prev().prev();
		if (prevText.attr('type') == 'submit') {
			prevText = prevText.prev();
		}
		$(this).prev().remove();
		$(this).next().remove();
		$(this).remove();
		prevText.select().focus();
		return false;
	});

	$('.textAreaDropDownFieldValueTextInput').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^textAreaDropDownFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="textAreaDropDownFieldValuesAddValue'+element+'Field'+field+'"]').click();
			return false;
		}

		return true;
	});

	$('.textAreaDropDownFieldValuesAddValue').live('click',function() {
		var m = $(this).attr('name').match(/^textAreaDropDownFieldValuesAddValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		var i = 1;

		$(this).prev().children('input[type="text"]').map(function() {
			var m = $(this).attr('name').match(/_([0-9]+)$/);
			if (parseInt(m[1]) > i) {
				i = parseInt(m[1]);
			}
		});

		i += 1;

		$('#textAreaDropDownFieldValues'+element+'Field'+field).append('<input type="text" class="textAreaDropDownFieldValueTextInput" name="textAreaDropDownFieldValue'+element+'Field'+field+'_'+i+'" value="Enter value" /><input type="submit" class="textAreaDropDownFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="textAreaDropDownFieldValue'+element+'Field'+field+'_'+i+'"]').select().focus();

		return false;
	});
});
