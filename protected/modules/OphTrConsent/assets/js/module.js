/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$(document).ready(function() {
	$(this).on('click','#et_save_draft, #et_save_draft_footer',function() {
		$('#Element_OphTrConsent_Type_draft').val(1);
	});

  $(this).on('click','#et_save_print, #et_save_print_footer',function() {
		$('#Element_OphTrConsent_Type_draft').val(0);
	});

  $(this).on('click','#et_cancel',function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/summary/'+OE_patient_id;
		}
		e.preventDefault();
	});

	$('select.populate_textarea').unbind('change').change(function() {
		if ($(this).val() != '') {
			var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
			var el = $('#'+cLass+'_'+$(this).attr('id'));
			var currentText = el.text();
			var newText = $(this).children('option:selected').text();

			if (currentText.length == 0) {
				el.text(ucfirst(newText));
			} else {
				el.text(currentText+', '+newText);
			}
		}
	});

	$('input[id="Element_OphTrConsent_Other_witness_required"]').unbind('click').click(function() {
		if ($(this).attr('checked') == 'checked') {
			$('#Element_OphTrConsent_Other_witness_name').show().closest('.data-group').show();
			$('#Element_OphTrConsent_Other_witness_name').val('').focus();
		} else {
			$('#Element_OphTrConsent_Other_witness_name').hide().closest('.data-group').hide();
		}
	});

	$('input[id="Element_OphTrConsent_Other_interpreter_required"]').unbind('click').click(function() {
		if ($(this).attr('checked') == 'checked') {
			$('#Element_OphTrConsent_Other_interpreter_name').show().closest('.data-group').show();
			$('#Element_OphTrConsent_Other_interpreter_name').val('').focus();
		} else {
			$('#Element_OphTrConsent_Other_interpreter_name').hide().closest('.data-group').hide();
		}
	});

	$('#et_print').unbind('click').click(function(e) {
		disableButtons();

		if ($('#OphTrConsent_draft').val() == 1) {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphTrConsent/default/doPrint/'+OE_event_id,
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to print the consent form, please try again or contact support for assistance."
						}).open();
					}
				}
			});
		} else {
			OphTrConsent_do_print(false);
			e.preventDefault();
		}
	});

	$('#et_print_va').unbind('click').click(function(e) {
		disableButtons();

		if ($('#OphTrConsent_draft').val() == 1) {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphTrConsent/default/doPrint/'+OE_event_id,
				'data': {
					vi: 1
				},
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to print the consent form, please try again or contact support for assistance."
						}).open();
					}
				}
			});
		} else {
			OphTrConsent_do_print(true);
			e.preventDefault();
		}
	});

	$('tr.clickable').disableSelection();

	$('tr.clickable').click(function() {
		$(this).children('td:first').children('input[type="radio"]').attr('checked',true);
	});

	// Normal print
	if ($('#OphTrConsent_print').val() == 1) {
		setTimeout(OphTrConsent_do_print, 1000);
	}
	// Print for visually impaired.
	else if ($('#OphTrConsent_print').val() == 2) {
		setTimeout(OphTrConsent_do_print.bind(null, true), 1000);
	}

	if(OpenEyes.UI.AutoCompleteSearch !== undefined){
		OpenEyes.UI.AutoCompleteSearch.init({
			input: $('#oe-autocompletesearch'),
			url: 'users',
			onSelect: function(){
				let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
				$('#Element_OphTrConsent_Other_consultant_id').val(AutoCompleteResponse.id);
				$('#Consultant').val(AutoCompleteResponse.fullname);
				return false;
			}
		});
	}

	autosize($('.Element_OphTrConsent_BenefitsAndRisks textarea'));
});

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		// handle event
	}
}

function OphTrConsent_inArray(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if (haystack[i].toLowerCase() == needle.toLowerCase()) return true;
	}
	return false;
}
function handleTinyMCEInput(target, data){
	// get the tinymce object
	const tinyMCE = tinymce.get(target);
	// get the tinymce current content, and convert into jquery 
	const tinyMCE_content = $(tinyMCE.getContent());
	const tinyMCE_content_items = tinyMCE_content.find('li');
	let existing_items = [];
	tinyMCE_content_items.each(function(i, ele){
		let existing_item = ele.innerText.trim();
		if(existing_item){
			existing_items.push(OphTrConsent_ucfirst(existing_item));
		}
	});
	data = data.map(function(item){
		return OphTrConsent_ucfirst(item);
	});
	let final_items = existing_items.concat(data);

	final_items = final_items.filter((item,index)=>{
		return (final_items.indexOf(item) == index)
	});

	final_items = final_items.map(function(item, i){
		return `<li>${item}</li>`;
	});
	tinyMCE.setContent(`<ul>${final_items.join('')}</ul>`);
}

function callbackAddProcedure(procedure_id, is_extra) {
	let benefits_url = is_extra ? '/OphTrConsent/default/benefits/'+procedure_id : baseUrl+'/procedure/benefits/'+procedure_id;
	let complications_url = is_extra ? '/OphTrConsent/default/complications/'+procedure_id : baseUrl+'/procedure/complications/'+procedure_id;
	$.ajax({
		'url': benefits_url,
		'type': 'GET',
		'dataType': 'json',
		'success': function(data) {
			handleTinyMCEInput('Element_OphTrConsent_BenefitsAndRisks_benefits', data);
		}
	});

	$.ajax({
		'url': complications_url,
		'type': 'GET',
		'dataType': 'json',
		'success': function(data) {
			handleTinyMCEInput('Element_OphTrConsent_BenefitsAndRisks_risks', data);
		}
	});
}

function OphTrConsent_ucfirst(str) {
	str += '';
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1);
}

function callbackRemoveProcedure(procedure_id) {
	const benefit_tinyMCE = tinymce.get('Element_OphTrConsent_BenefitsAndRisks_benefits');
	const risk_tinyMCE = tinymce.get('Element_OphTrConsent_BenefitsAndRisks_risks');
	benefit_tinyMCE.setContent('');
	risk_tinyMCE.setContent('');
	
	$.each($('.Element_OphTrConsent_ExtraProcedures input[name$="[proc_id]"]'),function() {
		callbackAddProcedure($(this).val(), true);
	});
	$.each($('.Element_OphTrConsent_Procedure input[name$="[proc_id]"]'),function() {
		callbackAddProcedure($(this).val(), false);
	});
}

function OphTrConsent_do_print(va) {
	if (va) {
		var va = {"vi":true};
	} else {
		var va = null;
	}

	$.ajax({
		'type': 'GET',
		'url': baseUrl+'/OphTrConsent/default/markPrinted/'+OE_event_id,
		'success': function(html) {
			printEvent(va);
			enableButtons();
		}
	});
}
