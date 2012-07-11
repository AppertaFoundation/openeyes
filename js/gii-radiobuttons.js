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
		var m = $(this).attr('name').match(/^radioButtonFieldSQLTable([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == '') {
			$('#radioButtonFieldSQLTableFieldDiv'+element+'Field'+field).hide();
			$('select[name="radioButtonFieldSQLTableField'+element+'Field'+field+'"]').html('');
		} else {
			var table = $(this).val();
			$('.loader').show();

			$.ajax({
				'type': 'GET',
				'url': '/gii/EventTypeModule?ajax=table_fields&table='+table,
				'success': function(html) {
					$('select[name="radioButtonFieldSQLTableField'+element+'Field'+field+'"]').html(html);
					$('#radioButtonFieldSQLTableFieldDiv'+element+'Field'+field).show();
					$('.loader').hide();
				}
			});
		}
	});

	$('.radioButtonFieldValueTextInput').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^radioButtonFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="radioButtonFieldValuesAddValue'+element+'Field'+field+'"]').click();
			return false;
		}

		return true;
	});

	$('.radioButtonFieldValuesAddValue').live('click',function() {
		var m = $(this).attr('name').match(/^radioButtonFieldValuesAddValue([0-9]+)Field([0-9]+)$/);
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

		$('#radioButtonFieldValues'+element+'Field'+field).append('<input type="radio" class="radioButtonFieldValueTextInputDefault" name="radioButtonFieldValueTextInputDefault'+element+'Field'+field+'" value="1" /> <input type="text" class="radioButtonFieldValueTextInput" name="radioButtonFieldValue'+element+'Field'+field+'_'+i+'" value="Enter value" /><input type="submit" class="radioButtonFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="radioButtonFieldValue'+element+'Field'+field+'_'+i+'"]').select().focus();

		return false;
	});

	$('.radioButtonFieldSQLTableField').live('change',function() {
		var m = $(this).attr('name').match(/^radioButtonFieldSQLTableField([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == '') {
			$('#radioButtonFieldSQLTableDefaultValueDiv'+element+'Field'+field).hide();
			$('select[name="radioButtonFieldValueTextInputDefault'+element+'Field'+field+'"]').html('');
		} else {
			var table = $('select[name="radioButtonFieldSQLTable'+element+'Field'+field+'"]').val();
			var fieldval = $(this).val();
			$('.loader').show();

			$.ajax({
				'type': 'GET',
				'url': '/gii/EventTypeModule?ajax=field_unique_values&table='+table+'&field='+fieldval,
				'success': function(html) {
					$('select[name="radioButtonFieldValueTextInputDefault'+element+'Field'+field+'"]').html(html);
					$('#radioButtonFieldSQLTableDefaultValueDiv'+element+'Field'+field).show();
					$('.loader').hide();
				}
			});
		}
	});

	$('.radioButtonMethodSelector').live('click',function() {
		var m = $(this).attr('name').match(/^radioButtonMethod([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == 0) {
			var view = 'extra_Radiobuttons_entervalues';
		} else {
			var view = 'extra_Radiobuttons_pointatsqltable';
		}

		$.ajax({
			'url': '/gii/EventTypeModule?ajax='+view+'&element_num='+element+'&field_num='+field,
			'type': 'GET',
			'success': function(html) {
				$('#radioButtonMethodFields'+element+'Field'+field).html(html);
				$('input[name="radioButtonFieldValue'+element+'Field'+field+'_1"]').select().focus();
			}
		});
	});
});
