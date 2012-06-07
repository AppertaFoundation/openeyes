$(document).ready(function() {
	$(".fieldLabel").live('focusout',function () {
		// alert("hello");
		// prepopulate the field name if it is currently empty
		var myid = $(this).attr('id');
		var myval = $(this).val();
		var myvalfield = myval.toLowerCase().replace(/ /g, "_");
		var nameid = myid.replace("Label","Name");
		nameval = $('#'+nameid).val();
		if (nameval.length < 1) {
			$('#'+nameid).val(myvalfield);
		}
	});
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

		$('#dropDownFieldValues'+element+'Field'+field).append('<input type="text" name="dropDownFieldValue'+element+'Field'+field+'_'+i+'" value="Enter value" /><input type="submit" class="dropDownFieldValuesRemoveValue" value="remove"><br/>');
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
});




