$(document).ready(function() {
	$('input[id^="textAreaDropDownCols"]').die('keypress').live('keypress',function(e) {
		if (e.keyCode == 13) {
			$('input[name="textAreaDropDownFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_1"]').select().focus();
			return false;
		}
		return true;
	});

	$('.textAreaDropDownFieldValuesAddValue').live('click',function() {
		var i = 1;

		$(this).prev().children('input[type="text"]').map(function() {
			var m = $(this).attr('name').match(/_([0-9]+)$/);
			if (parseInt(m[1]) > i) {
				i = parseInt(m[1]);
			}
		});

		i += 1;

		$('#textAreaDropDownFieldValues'+$(this).getElement()+'Field'+$(this).getField()).append('<input type="text" class="textAreaDropDownFieldValueTextInput" name="textAreaDropDownFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'" value="Enter value" /><input type="submit" class="textAreaDropDownFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="textAreaDropDownFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'"]').select().focus();

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
		if (e.keyCode == 13) {
			$('input[name="textAreaDropDownFieldValuesAddValue'+$(this).getElement()+'Field'+$(this).getField()+'"]').click();
			return false;
		}

		return true;
	});

	$('.textAreaDropDownFieldValuesAddValue').live('click',function() {
		var i = 1;

		$(this).prev().children('input[type="text"]').map(function() {
			var m = $(this).attr('name').match(/_([0-9]+)$/);
			if (parseInt(m[1]) > i) {
				i = parseInt(m[1]);
			}
		});

		i += 1;

		$('#textAreaDropDownFieldValues'+$(this).getElement()+'Field'+$(this).getField()).append('<input type="text" class="textAreaDropDownFieldValueTextInput" name="textAreaDropDownFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'" value="Enter value" /><input type="submit" class="textAreaDropDownFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="textAreaDropDownFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'"]').select().focus();

		return false;
	});
});
