$(document).ready(function() {
	$('.add_element_field').live('click',function() {
		var div = $(this).parent().children('div.element_fields');

		var element_num = $(this).attr('name').match(/[0-9]+/);

		var field_num = 0;
		div.children('div.element_field').each(function() {
			$(this).children('input[type="submit"]').each(function() {
				var m = $(this).attr('name').match(/^removeElementField([0-9]+)_([0-9]+)$/);
				if (parseInt(m[2]) > field_num) {
					field_num = parseInt(m[2]);
				}
			});
		});

		field_num += 1;

		$.ajax({
			'url': '/gii/EventTypeModule?ajax=element_field&element_num='+element_num+'&field_num='+field_num,
			'type': 'GET',
			'success': function(data) {
				div.append(data);
				$('#elementName'+element_num+'FieldLabel'+field_num).focus();
				return false;
			}
		});

		return false;
	});

	$('.add_element').live('click',function() {
		var element_num = 0;

		$('input[type="text"]').map(function() {
			if (m = $(this).attr('name').match(/^elementName([0-9]+)$/)) {
				if (parseInt(m[1]) > element_num) {
					element_num = parseInt(m[1]);
				}
			}
		});

		element_num += 1;

		$.ajax({
			'url': '/gii/EventTypeModule?ajax=element&element_num='+element_num,
			'type': 'GET',
			'success': function(data) {
				$('#elements').append(data);
				$('#elementName'+element_num).focus();
				return false;
			}
		});

		return false;
	});

	$('.remove_element_field').live('click',function() {
		$(this).parent().remove();
		return false;
	});

	$('.remove_element').live('click',function() {
		$(this).parent().parent().parent().remove();
		return false;
	});

	$('.selectFieldType').live('change',function() {
		var m = $(this).attr('name').match(/^elementType([0-9]+)FieldType([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		var selected = $(this).children('option:selected').text();

		switch (selected) {
			case 'Dropdown list':
				$.ajax({
					'url': '/gii/EventTypeModule?ajax=extraDropdownList&element_num='+element+'&field_num='+field,
					'type': 'GET',
					'success': function(html) {
						$('#extraDataElement'+element+'Field'+field).html(html);
					}
				});
				break;
			case 'Textarea with dropdown':
				$.ajax({
					'url': '/gii/EventTypeModule?ajax=extraTextAreaWithDropdown&element_num='+element+'&field_num='+field,
					'type': 'GET',
					'success': function(html) {
						$('#extraDataElement'+element+'Field'+field).html(html);
						$('input[name="textAreaDropDownFieldValue'+element+'Field'+field+'_1"]').select().focus();
					}
				});
				break;
			case 'Textbox':
			case 'Textarea':
			case 'Date picker':
			case 'Dropdown list':
			case 'Checkbox':
			case 'Radio buttons':
			case 'Boolean':
			case 'EyeDraw':
				$('#extraDataElement'+element+'Field'+field).html('');
				break;
		}

		return true;
	});

	$('.dropDownMethodSelector').live('click',function() {
		var m = $(this).attr('name').match(/^dropDownMethod([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == 0) {
			var view = 'extraDropdownListEnterValues';
		} else {
			var view = 'extraDropdownListPointAtSQLTable';
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

	$('.dropDownFieldValueTextInput').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^dropDownFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="dropDownFieldValuesAddValue'+element+'Field'+field+'"]').click();
			return false;
		}

		return true;
	});

	$('.elementNameTextField').live('keypress',function(e) {
		var element = $(this).attr('name').match(/[0-9]+/);

		if (e.keyCode == 13) {
			$('input[name="addElementField'+element+'"]').click();
			return false;
		}

		return true;
	});

	$('.fieldLabel').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^elementName([0-9]+)FieldLabel([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="elementName'+element+'FieldName'+field+'"]').select();
			return false;
		}

		return true;
	});

	$(".fieldLabel").live('focusout',function () {
		var m = $(this).attr('name').match(/^elementName([0-9]+)FieldLabel([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($('#elementName'+element+'FieldName'+field).val() == '') {
			$('#elementName'+element+'FieldName'+field).val($(this).val().toLowerCase().replace(/ /g, "_"));
		}

		$('#elementName'+element+'FieldName'+field).select().focus();
	});

	$('.fieldName').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^elementName([0-9]+)FieldName([0-9]+)$/);
		var element = m[1];
		var field = m[2];
		
		if (e.keyCode == 13) {
			$('input[name="addElementField'+element+'"]').click();
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

	$('.textAreaDropDownFieldValueTextInput').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^textAreaDropDownFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="textAreaDropDownFieldValuesAddValue'+element+'Field'+field+'"]').click();
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

	$('#EventTypeModuleCode_moduleSuffix').focus();
});
