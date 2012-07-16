$(document).ready(function() {
	$('.radioButtonFieldValuesRemoveValue').live('click',function() {
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

	$('.radioButtonFieldSQLTable').live('change',function() {
		if ($(this).val() == '') {
			$('#radioButtonFieldSQLTableFieldDiv'+$(this).getElement()+'Field'+$(this).getField()).hide();
			$('select[name="radioButtonFieldSQLTableField'+$(this).getElement()+'Field'+$(this).getField()+'"]').html('');
		} else {
			var table = $(this).val();

			$(this).ajaxCall('table_fields',{"table":table},function(html,element,field) {
				$('select[name="radioButtonFieldSQLTableField'+element+'Field'+field+'"]').html(html);
				$('#radioButtonFieldSQLTableFieldDiv'+element+'Field'+field).show();
			});
		}
	});

	$('.radioButtonFieldValueTextInput').die('keypress').live('keypress',function(e) {
		if (e.keyCode == 13) {
			$('input[name="radioButtonFieldValuesAddValue'+$(this).getElement()+'Field'+$(this).getField()+'"]').click();
			return false;
		}
		return true;
	});

	$('.radioButtonFieldValuesAddValue').live('click',function() {
		var i = 1;

		$(this).prev().children('input[type="text"]').map(function() {
			var m = $(this).attr('name').match(/_([0-9]+)$/);
			if (parseInt(m[1]) > i) {
				i = parseInt(m[1]);
			}
		});

		i += 1;

		$('#radioButtonFieldValues'+$(this).getElement()+'Field'+$(this).getField()).append('<input type="radio" class="radioButtonFieldValueTextInputDefault" name="radioButtonFieldValueTextInputDefault'+$(this).getElement()+'Field'+$(this).getField()+'" value="1" /> <input type="text" class="radioButtonFieldValueTextInput" name="radioButtonFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'" value="Enter value" /><input type="submit" class="radioButtonFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="radioButtonFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'"]').select().focus();

		return false;
	});

	$('.radioButtonFieldSQLTableField').live('change',function() {
		if ($(this).val() == '') {
			$('#radioButtonFieldSQLTableDefaultValueDiv'+$(this).getElement()+'Field'+$(this).getField()).hide();
			$('select[name="radioButtonFieldValueTextInputDefault'+$(this).getElement()+'Field'+$(this).getField()+'"]').html('');
		} else {
			var table = $('select[name="radioButtonFieldSQLTable'+$(this).getElement()+'Field'+$(this).getField()+'"]').val();
			var fieldval = $(this).val();

			$(this).ajaxCall('field_unique_values',{"table":table,"field":fieldval},function(html,element,field) {
				$('select[name="radioButtonFieldValueTextInputDefault'+element+'Field'+field+'"]').html(html);
				$('#radioButtonFieldSQLTableDefaultValueDiv'+element+'Field'+field).show();
			});
		}
	});

	$('.radioButtonMethodSelector').live('click',function() {
		if ($(this).val() == 0) {
			var view = 'extra_Radiobuttons_entervalues';
		} else {
			var view = 'extra_Radiobuttons_pointatsqltable';
		}

		$(this).ajaxCall(view,'',function(html,element,field) {
			$('#radioButtonMethodFields'+element+'Field'+field).html(html);
			$('input[name="radioButtonFieldValue'+element+'Field'+field+'_1"]').select().focus();
		});
	});
});
