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
				loadExtraFieldView('extraDropdownList',element,field);
				break;
			case 'Textarea with dropdown':
				loadExtraFieldView('extraTextAreaWithDropdown',element,field,function() {
					$('input[name="textAreaDropDownFieldValue'+element+'Field'+field+'_1"]').select().focus();
				});
				break;
			case 'EyeDraw':
				loadExtraFieldView('extraEyedraw',element,field);
				break;
			case 'Radio buttons':
				loadExtraFieldView('extraRadioButtons',element,field);
				break;
			case 'Multi select':
				loadExtraFieldView('extraMultiSelect',element,field);
				break;
			case 'Textbox':
			case 'Textarea':
			case 'Date picker':
			case 'Dropdown list':
			case 'Checkbox':
			case 'Boolean':
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

	$('.radioButtonFieldValueTextInput').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^radioButtonFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="radioButtonFieldValuesAddValue'+element+'Field'+field+'"]').click();
			return false;
		}

		return true;
	});

	$('.multiSelectFieldValueTextInput').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^multiSelectFieldValue([0-9]+)Field([0-9]+)_[0-9]+$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			$('input[name="multiSelectFieldValuesAddValue'+element+'Field'+field+'"]').click();
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

	$('.radioButtonMethodSelector').live('click',function() {
		var m = $(this).attr('name').match(/^radioButtonMethod([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == 0) {
			var view = 'extraRadioButtonEnterValues';
		} else {
			var view = 'extraRadioButtonPointAtSQLTable';
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

	$('.multiSelectMethodSelector').live('click',function() {
		var m = $(this).attr('name').match(/^multiSelectMethod([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($(this).val() == 0) {
			var view = 'extraMultiSelectEnterValues';
		} else {
			var view = 'extraMultiSelectPointAtSQLTable';
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

	$('.eyeDrawClassSelect').live('change',function() {
		var m = $(this).attr('name').match(/^eyedrawClass([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];
		var selected = $(this).children('option:selected').val();

		if (selected != '') {
			$.ajax({
				'url': '/gii/EventTypeModule?ajax=getEyedrawSize&class='+selected,
				'type': 'GET',
				'success': function(size) {
					$('input[name="eyedrawSize'+element+'Field'+field+'"]').val(size).select().focus();

					switch(selected) {
						case 'Buckle':
						case 'Cataract':
							if (!$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html().match(/eyedrawExtraReport/)) {
								$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html('<input type="checkbox" name="eyedrawExtraReport'+element+'Field'+field+'" value="1" /> Store eyedraw report data in hidden input<br/>');
							}
							break;
						default:
							$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html('');
							break;
					}
				}
			});
		} else {
			$('input[name="eyedrawSize'+element+'Field'+field+'"]').val('');
			$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html('');
		}
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

	$('#EventTypeModuleCode_moduleSuffix').focus();
});

function loadExtraFieldView(view_name,element,field,callback) {
	$.ajax({
		'url': '/gii/EventTypeModule?ajax='+view_name+'&element_num='+element+'&field_num='+field,
		'type': 'GET',
		'success': function(html) {
			$('#extraDataElement'+element+'Field'+field).html(html);
			if (callback) {
				callback();
			}
		}
	});
}

function selectSort(a, b) {
		if (a.innerHTML == rootItem) {
				return -1;
		}
		else if (b.innerHTML == rootItem) {
				return 1;
		}
		return (a.innerHTML > b.innerHTML) ? 1 : -1;
};

var rootItem = null;

function sort_selectbox(element) {
	rootItem = element.children('option:first').text();
	element.append(element.children('option').sort(selectSort));
}
