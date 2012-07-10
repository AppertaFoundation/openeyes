$(document).ready(function() {
	$('.multiSelectFieldSQLTable').live('change',function() {
		var m = $(this).attr('name').match(/^multiSelectFieldSQLTable([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == '') {
			$('#multiSelectFieldSQLTableFieldDiv'+element+'Field'+field).hide();
			$('select[name="multiSelectFieldSQLTableField'+element+'Field'+field+'"]').html('');
		} else {
			var table = $(this).val();
			$('.loader').show();

			$.ajax({
				'type': 'GET',
				'url': '/gii/EventTypeModule?ajax=table_fields&table='+table,
				'success': function(html) {
					$('select[name="multiSelectFieldSQLTableField'+element+'Field'+field+'"]').html(html);
					$('#multiSelectFieldSQLTableFieldDiv'+element+'Field'+field).show();
					$('.loader').hide();
				}
			});
		}
	});

	$('.multiSelectFieldValueTextInput').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^multiSelectFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="multiSelectFieldValuesAddValue'+element+'Field'+field+'"]').click();
			return false;
		}

		return true;
	});

	$('.multiSelectFieldSQLTableField').live('change',function() {
		var m = $(this).attr('name').match(/^multiSelectFieldSQLTableField([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == '') {
			$('#multiSelectFieldSQLTableDefaultValueDiv'+element+'Field'+field).hide();
			$('select[name="multiSelectFieldValueTextInputDefault'+element+'Field'+field+'"]').html('');
		} else {
			var table = $('select[name="multiSelectFieldSQLTable'+element+'Field'+field+'"]').val();
			var fieldval = $(this).val();
			$('.loader').show();

			$.ajax({
				'type': 'GET',
				'url': '/gii/EventTypeModule?ajax=dump_field_unique_values_multi&table='+table+'&field='+fieldval,
				'success': function(html) {
					$('select[name="multiSelectFieldValueDefault'+element+'Field'+field+'"]').html(html);
					$('#multiSelectFieldSQLTableDefaultValueDiv'+element+'Field'+field).show();
					$('.loader').hide();
				}
			});
		}
	});

	$('.multiSelectFieldValueDefault').live('change',function() {
		var m = $(this).attr('name').match(/^multiSelectFieldValueDefault([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() != '') {
			$('#multiSelectFieldValueDefaultsDiv'+element+'Field'+field).append('<div><input type="hidden" name="multiSelectFieldValueDefaults'+element+'Field'+field+'[]" value="'+$(this).children('option:selected').val()+'" /><span>'+$(this).children('option:selected').text()+'</span> <a href="#" class="multiSelectFieldValueDefaultsRemove">(remove)</a></div>');

			$(this).children('option:selected').remove();
			$(this).val('');
		}
	});

	$('.multiSelectFieldValueDefaultsRemove').live('click',function() {
		var val = $(this).parent().children('input').val();
		var text = $(this).parent().children('span').text();
		var m = $(this).parent().children('input').attr('name').match(/^multiSelectFieldValueDefaults([0-9]+)Field([0-9]+)/);
		var element = m[1];
		var field = m[2];

		$(this).parent().remove();

		var select = $('select[name="multiSelectFieldValueDefault'+element+'Field'+field+'"]');

		select.append('<option value="'+val+'">'+text+'</option>');
		sort_selectbox(select);

		return false;
	});

	$('.multiSelectMethodSelector').live('click',function() {
		var m = $(this).attr('name').match(/^multiSelectMethod([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == 0) {
			var view = 'extra_Multiselect_entervalues';
		} else {
			var view = 'extra_Multiselect_pointatsqltable';
		}

		$.ajax({
			'url': '/gii/EventTypeModule?ajax='+view+'&element_num='+element+'&field_num='+field,
			'type': 'GET',
			'success': function(html) {
				$('#multiSelectMethodFields'+element+'Field'+field).html(html);
				$('input[name="multiSelectFieldValue'+element+'Field'+field+'_1"]').select().focus();
			}
		});
	});

	$('.multiSelectFieldValuesAddValue').live('click',function() {
		var m = $(this).attr('name').match(/^multiSelectFieldValuesAddValue([0-9]+)Field([0-9]+)$/);
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

		$('#multiSelectFieldValues'+element+'Field'+field).append('<input type="checkbox" class="multiSelectFieldValueTextInputDefault" name="multiSelectFieldValueTextInputDefault'+element+'Field'+field+'_'+i+'" value="1" /> <input type="text" class="multiSelectFieldValueTextInput" name="multiSelectFieldValue'+element+'Field'+field+'_'+i+'" value="Enter value" /><input type="submit" class="multiSelectFieldValuesRemoveValue" value="remove"><br/>');
		$('input[name="multiSelectFieldValue'+element+'Field'+field+'_'+i+'"]').select().focus();

		return false;
	});
});
