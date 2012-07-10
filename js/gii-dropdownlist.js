$(document).ready(function() {
	$('.dropDownMethodSelector').live('click',function() {
		var m = $(this).attr('name').match(/^dropDownMethod([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == 0) {
			var view = 'extra_Dropdownlist_entervalues';
		} else {
			var view = 'extra_Dropdownlist_pointatsqltable';
		}

		$.ajax({
			'url': '/gii/EventTypeModule?ajax='+view+'&element_num='+element+'&field_num='+field,
			'type': 'GET',
			'success': function(html) {
				$('#dropDownMethodFields'+element+'Field'+field).html(html);
				$('input[name="dropDownFieldValue'+element+'Field'+field+'_1"]').select().focus();
			}
		});
	});

	$('.dropDownFieldValuesAddValue').live('click',function() {
		var m = $(this).attr('name').match(/^dropDownFieldValuesAddValue([0-9]+)Field([0-9]+)$/);
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

		$('#dropDownFieldValues'+element+'Field'+field).append('<input type="radio" class="dropDownFieldValueTextInputDefault" name="dropDownFieldValueTextInputDefault'+element+'Field'+field+'" value="'+i+'" /> <input type="text" class="dropDownFieldValueTextInput" name="dropDownFieldValue'+element+'Field'+field+'_'+i+'" value="Enter value" /><input type="submit" class="dropDownFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="dropDownFieldValue'+element+'Field'+field+'_'+i+'"]').select().focus();

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
		var m = $(this).attr('name').match(/^dropDownFieldSQLTable([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == '') {
			$('#dropDownFieldSQLTableFieldDiv'+element+'Field'+field).hide();
			$('select[name="dropDownFieldSQLTableField'+element+'Field'+field+'"]').html('');
		} else {
			var table = $(this).val();
			$('.loader').show();

			$.ajax({
				'type': 'GET',
				'url': '/gii/EventTypeModule?ajax=table_fields&table='+table,
				'success': function(html) {
					$('select[name="dropDownFieldSQLTableField'+element+'Field'+field+'"]').html(html);
					$('#dropDownFieldSQLTableFieldDiv'+element+'Field'+field).show();
					$('.loader').hide();
				}
			});
		}
	});

	$('.dropDownFieldValueTextInput').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^dropDownFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="dropDownFieldValuesAddValue'+element+'Field'+field+'"]').click();
			return false;
		}

		return true;
	});

	$('.dropDownFieldSQLTableField').live('change',function() {
		var m = $(this).attr('name').match(/^dropDownFieldSQLTableField([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == '') {
			$('#dropDownFieldSQLTableDefaultValueDiv'+element+'Field'+field).hide();
			$('select[name="dropDownFieldValueTextInputDefault'+element+'Field'+field+'"]').html('');
		} else {
			var table = $('select[name="dropDownFieldSQLTable'+element+'Field'+field+'"]').val();
			var fieldval = $(this).val();
			$('.loader').show();

			$.ajax({
				'type': 'GET',
				'url': '/gii/EventTypeModule?ajax=field_unique_values&table='+table+'&field='+fieldval,
				'success': function(html) {
					$('select[name="dropDownFieldValueTextInputDefault'+element+'Field'+field+'"]').html(html);
					$('#dropDownFieldSQLTableDefaultValueDiv'+element+'Field'+field).show();
					$('.loader').hide();
				}
			});
		}
	});
});
