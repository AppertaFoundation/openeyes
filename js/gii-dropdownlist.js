$(document).ready(function() {
	$('.dropDownMethodSelector').live('click',function() {
		if ($(this).val() == 0) {
			var view = 'extra_Dropdownlist_entervalues';
		} else {
			var view = 'extra_Dropdownlist_pointatsqltable';
		}

		$(this).ajaxCall(view,'',function(html, element, field) {
			$('#dropDownMethodFields'+element+'Field'+field).html(html);
			$('input[name="dropDownFieldValue'+element+'Field'+field+'_1"]').select().focus();
		});
	});

	$('.dropDownFieldValuesAddValue').live('click',function() {
		var i = 1;

		$(this).prev().children('input[type="text"]').map(function() {
			var m = $(this).attr('name').match(/_([0-9]+)$/);
			if (parseInt(m[1]) > i) {
				i = parseInt(m[1]);
			}
		});

		i += 1;

		$('#dropDownFieldValues'+$(this).getElement()+'Field'+$(this).getField()).append('<input type="radio" class="dropDownFieldValueTextInputDefault" name="dropDownFieldValueTextInputDefault'+$(this).getElement()+'Field'+$(this).getField()+'" value="'+i+'" /> <input type="text" class="dropDownFieldValueTextInput" name="dropDownFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'" value="Enter value" /><input type="submit" class="dropDownFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="dropDownFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'"]').select().focus();

		return false;
	});

	$('.dropDownFieldValuesRemoveValue').live('click',function() {
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

	$('.dropDownFieldSQLTable').live('change',function() {
		if ($(this).val() == '') {
			$('#dropDownFieldSQLTableFieldDiv'+$(this).getElement()+'Field'+$(this).getField()).hide();
			$('select[name="dropDownFieldSQLTableField'+$(this).getElement()+'Field'+$(this).getField()+'"]').html('');
		} else {
			var table = $(this).val();

			$(this).ajaxCall('table_fields',{"table":table},function(html, element, field) {
				$('select[name="dropDownFieldSQLTableField'+element+'Field'+field+'"]').html(html);
				$('#dropDownFieldSQLTableFieldDiv'+element+'Field'+field).show();
			});
		}
	});

	$('.dropDownFieldValueTextInput').die('keypress').live('keypress',function(e) {
		if (e.keyCode == 13) {
			$('input[name="dropDownFieldValuesAddValue'+$(this).getElement()+'Field'+$(this).getField()+'"]').click();
			return false;
		}
		return true;
	});

	$('.dropDownFieldSQLTableField').live('change',function() {
		if ($(this).val() == '') {
			$('#dropDownFieldSQLTableDefaultValueDiv'+$(this).getElement()+'Field'+$(this).getField()).hide();
			$('select[name="dropDownFieldValueTextInputDefault'+$(this).getElement()+'Field'+$(this).getField()+'"]').html('');
		} else {
			var table = $('select[name="dropDownFieldSQLTable'+$(this).getElement()+'Field'+$(this).getField()+'"]').val();
			var fieldval = $(this).val();

			$(this).ajaxCall('field_unique_values',{"table":table,"field":fieldval},function(html, element, field) {
				$('select[name="dropDownFieldValueTextInputDefault'+element+'Field'+field+'"]').html(html);
				$('#dropDownFieldSQLTableDefaultValueDiv'+element+'Field'+field).show();
			});
		}
	});
});
