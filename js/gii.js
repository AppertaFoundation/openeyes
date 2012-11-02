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
			'url': baseUrl+'/gii/EventTypeModule?ajax=element_field&element_num='+element_num+'&field_num='+field_num,
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
		
		// work out the highest element number (which will be the latest one)
		$('input[type="text"]').map(function() {
			if (m = $(this).attr('name').match(/^elementName([0-9]+)$/)) {
				if (parseInt(m[1]) > element_num) {
					element_num = parseInt(m[1]);
				}
			}
		});
		
		$('select.elementToAddFieldsTo').map(function() {
			if (m = $(this).attr('name').match(/^elementId([0-9]+)$/)) {
				if (parseInt(m[1]) > element_num) {
					element_num = parseInt(m[1]);
				}
			}
		});

		element_num += 1;

		if ($('#EventTypeModuleModeRadioGenerateNew').is(':checked')) {
			var target = 'elementsGenerateNew';
		} else {
			var target = 'elementsModifyExisting';
		}

		$.ajax({
			'url': baseUrl+'/gii/EventTypeModule?ajax=element&element_num='+element_num,
			'type': 'GET',
			'success': function(data) {
				$('#'+target).append(data);
				$('#elementName'+element_num).focus();
				return false;
			}
		});

		return false;
	});

	$('.add_field').live('click',function() {
		var element_num = 0;

		$('input[type="text"]').map(function() {
			if (m = $(this).attr('name').match(/^elementName([0-9]+)$/)) {
				if (parseInt(m[1]) > element_num) {
					element_num = parseInt(m[1]);
				}
			}
		});

		$('select.elementToAddFieldsTo').map(function() {
			if (m = $(this).attr('name').match(/^elementId([0-9]+)$/)) {
				if (parseInt(m[1]) > element_num) {
					element_num = parseInt(m[1]);
				}
			}
		});

		element_num += 1;

		if ($('#EventTypeModuleModeRadioGenerateNew').is(':checked')) {
			var target = 'elementsGenerateNew';
		} else {
			var target = 'elementsModifyExisting';
		}

		var event_type_id = $('select[name="EventTypeModuleEventType"]').val();

		$.ajax({
			'url': baseUrl+'/gii/EventTypeModule?ajax=elementfields&element_num='+element_num+'&event_type_id='+event_type_id,
			'type': 'GET',
			'success': function(data) {
				$('#'+target).append(data);
				$('#elementName'+element_num).focus();
				return false;
			}
		});

		return false;
	});

	$('.elementToAddFieldsTo').live('change',function() {
		var id = $(this).attr('name').replace(/^elementId/,'');

		if ($(this).val() == '') {
			$('input[name="addElementField'+id+'"]').hide();
		} else {
			$('input[name="addElementField'+id+'"]').show();
		}
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

		$.ajax({
			'url': baseUrl+'/gii/EventTypeModule?ajax=extra_'+$(this).children('option:selected').text().replace(/ /g,'')+'&element_num='+element+'&field_num='+field,
			'type': 'GET',
			'success': function(html) {
				if (html.length >0) {
					$('#extraDataElement'+element+'Field'+field).html(html);
					$('#extraDataElement'+element+'Field'+field+' input[type="text"]:first').select().focus();
				} else {
					$('#extraDataElement'+element+'Field'+field).html('');
				}
			}
		});

		return true;
	});

	$('.elementNameTextField').die('keypress').live('keypress',function(e) {
		var element = $(this).attr('name').match(/[0-9]+/);

		if (e.keyCode == 13) {
			$('input[name="addElementField'+element+'"]').click();
			return false;
		}

		return true;
	});
	
	// auto generate the shortname for the event from the event name
	$("#moduleSuffix").live('focusout', function () {
		if ($("#moduleShortSuffix").val().length == 0) {
			var sname = $.trim($(this).val()).toLowerCase().replace(/ /g, "").substring(0,20);
			$("#moduleShortSuffix").val(sname).select().focus();
		}
	});
	
	// auto generate the shortname field content when the element name is created
	$(".elementNameTextField").live('focusout',function () {
		var m = $(this).attr('name').match(/^elementName([0-9]+)$/);
		var element = m[1];
		
		if ($('#elementShortName'+element).val() == '') {
			var sname = $(this).val().toLowerCase().replace(/ /g, "").substring(0,11);
			$('#elementShortName'+element).val(sname);
		}

		if ($(this).val() != '') {
			$('#elementShortName'+element).select().focus();
		}
	});
	
	$('.fieldLabel').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^elementName([0-9]+)FieldLabel([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if (e.keyCode == 13) {
			if ($(this).val() != '') {
				$('input[name="elementName'+element+'FieldName'+field+'"]').select();
			}
			return false;
		}

		return true;
	});

	$(".fieldLabel").live('focusout',function () {
		var m = $(this).attr('name').match(/^elementName([0-9]+)FieldLabel([0-9]+)$/);
		var element = m[1];
		var field = m[2];

		if ($('#elementName'+element+'FieldName'+field).val() == '') {
			$('#elementName'+element+'FieldName'+field).val($.trim($(this).val()).toLowerCase().replace(/ /g, "_"));
		}

		if ($(this).val() != '') {
			$('#elementName'+element+'FieldName'+field).select().focus();
		}
	});

	$('.fieldName').die('keypress').live('keypress',function(e) {
		var m = $(this).attr('name').match(/^elementName([0-9]+)FieldName([0-9]+)$/);
		var element = m[1];
		var field = m[2];
		
		if (e.keyCode == 13) {
			$('input[name="addElementField'+element+'"]').click();
			return false;
		}
		
		return true;
	});

	// deduce and set various values on the specified decimal field attributes
	function manageDecimal(element, field) {
		var min_val = $('#decimalMinValue' + element + 'Field' + field).val().replace(/[^0-9\.\-\+]/g, "");
		var max_val = $('#decimalMaxValue' + element + 'Field' + field).val().replace(/[^0-9\.\-\+]/g, "");
		var dps = parseInt($('#decimalForceDP' + element + 'Field' + field).val());
		
		if (isNaN(dps)) {
			dps = 0;
		}
		var max_length = parseInt($('#decimalMaxLength' + element + 'Field' + field).val());
		if (isNaN(max_length)) {
			max_length = 0;
		}
		var fld_size = parseInt($('#decimalSize'+element+'Field'+field).val());
		if (isNaN(fld_size)) {
			fld_size = 0;
		}
		
		var min_flt = parseFloat(min_val);
		var max_flt = parseFloat(max_val);
		
		if (!isNaN(min_flt)) {
			// deduce required dps from min val
			var num_chk = min_val.match(/^([+-]?\d+)(\.(\d+))?$/);
			if (num_chk) {
				
				if (num_chk[3] != undefined && num_chk[3].length > dps) {
					dps = num_chk[1].length;
				}
				
				var length = num_chk[1].length;
				if (dps > 0) {
					length += dps + 1;
				}
				if (length > max_length) {
					max_length = length;
				}
			}
		}
		
		if (!isNaN(max_flt)) {
			// deduce required dps from min val
			var num_chk = max_val.match(/^[+-]?(\d+)(\.(\d+))?$/);
			
			if (num_chk) {
				if (num_chk[3] != undefined && num_chk[3].length > dps) {
					dps = num_chk[1].length;
				}
				var length = num_chk[1].length;
				
				if (dps > 0) {
					length += dps + 1;
				}
				if (length > max_length) {
					max_length = length;
				}
			}
		}
		if (dps) {
			if (dps >= max_length) {
				max_length += dps+2;
			}
			$('#decimalForceDP' + element + 'Field' + field).val(dps);
		}
		
		if (max_length) {
			$('#decimalMaxLength' + element + 'Field' +field).val(max_length);
		}
		if (max_length > fld_size) {
			$('#decimalSize' + element + 'Field' + field).val(max_length+1);
		}
	}
	
	$('.decimalMinValue').live('focusout', function() {
		var m = $(this).attr('name').match(/^decimalMinValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];
		
		if ($(this).val() != '') {
			manageDecimal(element, field);
		}
	})
	
	$('.decimalMaxValue').live('focusout', function() {
		var m = $(this).attr('name').match(/^decimalMaxValue([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];
		
		if ($(this).val() != '') {
			manageDecimal(element, field);
		}
	})
	
	$('.decimalForceDP').live('focusout', function() {
		var m = $(this).attr('name').match(/^decimalForceDP([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];
		
		if ($(this).val() != '') {
			manageDecimal(element, field);
		}
	})
	
	$('input.EventTypeModuleMode').click(function() {
		if ($(this).val() == 0) {
			var view = 'EventTypeModuleGenerate_GenerateNew';
		} else {
			var view = 'EventTypeModuleGenerate_ModifyExisting';
		}

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/gii/EventTypeModule?ajax='+view,
			'success': function(html) {
				$('#EventTypeModuleGenerateDiv').html(html);
				$('input[name="generate"]').remove();
				$('div.feedback').remove();
			}
		});
	});

	$('select.EventTypeModuleEventType').live('change',function() {
		if ($(this).val() != '') {
			$.ajax({
				'url': baseUrl+'/gii/EventTypeModule?ajax=event_type_properties&event_type_id='+$(this).val(),
				'type': 'GET',
				'success': function(html) {
					$('#EventTypeModuleEventTypeProperties').html(html);
					$('#EventTypeModuleEventTypeElementTypes').show();
					$('#EventType_name').select().focus();
				}
			});
		}
	});

	if ($('#target_event_class').length >0) {
		$('td.file').map(function() {
			var path = $(this).children('a').text();
			var s = path.split('/');
			var r = new RegExp(s[1],'g');
			var destination = path.replace(r,$('#target_event_class').val());
			$(this).parent().after('<tr class="new"><td class="file" style="color: #f00;"> - rename to: '+destination+'</td><td class="confirm">renamed</td></tr>');
		});
	}

	if ($('#EventTypeModuleModeRadioModifyExisting').is(':checked')) {
		$('tr.overwrite').map(function() {
			var file = $(this).children('td.file').children('a:first').text();

			$(this).children('td.confirm').map(function() {
				var s = $(this).children('label').attr('for').split('_');
				$(this).children('input').remove();
				$(this).append('<input type="checkbox" name="updatefile['+s[1]+']" value="1" checked="checked" />');
				$(this).append('<input type="hidden" name="filename['+s[1]+']" value="'+file+'" />');
				$(this).children('label').text('update');
			});
		});
	}

	if ($('#has_errors').val() == 1) {
		$('input[name="generate"]').hide();
		$('div.buttons').after('<span style="color: #f00;">Please fix the errors indicated in red above.</span>');
	}

	$('input.noreturn').die('keypress').live('keypress',function(e) {
		return (e.keyCode != 13);
	});

	$('input.returnnext').die('keypress').live('keypress',function(e) {
		if (e.keyCode == 13) {
			$(this).parent().parent().next().children('td:nth-child(2)').children('input').select().focus();
			return false;
		}

		return true;
	});

	$('#EventTypeModuleCode_moduleSuffix').focus();
});

$.fn.extend({
	ajaxCall: function(ajaxMethod, values, callback) {
		var m = this.getFieldProperties();

		if (m) {
			var giiElement = m[1];
			var giiField = m[2];

			var queryStr = baseUrl+'/gii/EventTypeModule?ajax='+ajaxMethod+'&element_num='+giiElement+'&field_num='+giiField;

			if (values) {
				for (var key in values) {
					queryStr += '&'+key+'='+values[key];
				}
			}

			$('.loader').show();

			$.ajax({
				'url': queryStr,
				'type': 'GET',
				'success': function(html) {
					if (callback) {
						callback(html,giiElement,giiField);
						$('.loader').hide();
					}
				}
			});
		}
	},
	getFieldProperties: function() {
		var m = $(this).attr('name').match(/^[a-zA-Z]+([0-9]+)Field([0-9]+)_([0-9]+)$/);
		if (!m) {
			var m = $(this).attr('name').match(/^[a-zA-Z]+([0-9]+)Field([0-9]+)$/);
		}
		return m;
	},
	getElement: function() {
		var m = this.getFieldProperties();
		if (m) {
			return m[1];
		}
	},
	getField: function() {
		var m = this.getFieldProperties();
		if (m) {
			return m[2];
		}
	}
});

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
