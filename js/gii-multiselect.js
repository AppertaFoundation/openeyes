$(document).ready(function() {
	$('.multiSelectFieldSQLTable').live('change',function() {
		if ($(this).val() == '') {
			$('#multiSelectFieldSQLTableFieldDiv'+$(this).getElement()+'Field'+$(this).getField()).hide();
			$('select[name="multiSelectFieldSQLTableField'+$(this).getElement()+'Field'+$(this).getField()+'"]').html('');
		} else {
			var table = $(this).val();

			$(this).ajaxCall('table_fields',{"table":table},function(html,element,field) {
				$('select[name="multiSelectFieldSQLTableField'+element+'Field'+field+'"]').html(html);
				$('#multiSelectFieldSQLTableFieldDiv'+element+'Field'+field).show();
			});
		}
	});

	$('.multiSelectFieldValueTextInput').die('keypress').live('keypress',function(e) {
		if (e.keyCode == 13) {
			$('input[name="multiSelectFieldValuesAddValue'+$(this).getElement()+'Field'+$(this).getField()+'"]').click();
			return false;
		}
		return true;
	});

	$('.multiSelectFieldSQLTableField').live('change',function() {
		if ($(this).val() == '') {
			$('#multiSelectFieldSQLTableDefaultValueDiv'+$(this).getElement()+'Field'+field).hide();
			$('select[name="multiSelectFieldValueTextInputDefault'+$(this).getElement()+'Field'+$(this).getField()+'"]').html('');
		} else {
			var table = $('select[name="multiSelectFieldSQLTable'+$(this).getElement()+'Field'+$(this).getField()+'"]').val();
			var fieldval = $(this).val();

			$(this).ajaxCall('dump_field_unique_values_multi',{"table":table,"field":fieldval},function(html,element,field) {
				$('select[name="multiSelectFieldValueDefault'+element+'Field'+field+'"]').html(html);
				$('#multiSelectFieldSQLTableDefaultValueDiv'+element+'Field'+field).show();
			});
		}
	});

	$('.multiSelectFieldValueDefault').live('change',function() {
		if ($(this).val() != '') {
			$('#multiSelectFieldValueDefaultsDiv'+$(this).getElement()+'Field'+$(this).getField()).append('<div><input type="hidden" name="multiSelectFieldValueDefaults'+$(this).getElement()+'Field'+$(this).getField()+'[]" value="'+$(this).children('option:selected').val()+'" /><span>'+$(this).children('option:selected').text()+'</span> <a href="#" class="multiSelectFieldValueDefaultsRemove">(remove)</a></div>');

			$(this).children('option:selected').remove();
			$(this).val('');
		}
	});

	$('.multiSelectFieldValueDefaultsRemove').live('click',function() {
		var val = $(this).parent().children('input').val();
		var text = $(this).parent().children('span').text();

		$(this).parent().remove();

		var select = $('select[name="multiSelectFieldValueDefault'+$(this).getElement()+'Field'+$(this).getField()+'"]');

		select.append('<option value="'+val+'">'+text+'</option>');
		sort_selectbox(select);

		return false;
	});

	$('.multiSelectMethodSelector').live('click',function() {
		if ($(this).val() == 0) {
			var view = 'extra_Multiselect_entervalues';
		} else {
			var view = 'extra_Multiselect_pointatsqltable';
		}

		$(this).ajaxCall(view,'',function(html,element,field) {
			$('#multiSelectMethodFields'+element+'Field'+field).html(html);
			$('input[name="multiSelectFieldValue'+element+'Field'+field+'_1"]').select().focus();
		});
	});

	$('.multiSelectFieldValuesAddValue').live('click',function() {
		var i = 1;

		$(this).prev().children('input[type="text"]').map(function() {
			var m = $(this).attr('name').match(/_([0-9]+)$/);
			if (parseInt(m[1]) > i) {
				i = parseInt(m[1]);
			}
		});

		i += 1;

		$('#multiSelectFieldValues'+$(this).getElement()+'Field'+$(this).getField()).append('<input type="checkbox" class="multiSelectFieldValueTextInputDefault" name="multiSelectFieldValueTextInputDefault'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'" value="1" /> <input type="text" class="multiSelectFieldValueTextInput" name="multiSelectFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'" value="Enter value" /><input type="submit" class="multiSelectFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="multiSelectFieldValue'+$(this).getElement()+'Field'+$(this).getField()+'_'+i+'"]').select().focus();

		return false;
	});
});
