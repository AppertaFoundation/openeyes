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

	$(this).on('click','#et_save',function() {
		$('#Element_OphTrConsent_Type_draft').val(0);
	});

	const $bestInterestDecisionElement = $(".element.OEModule_OphTrConsent_models_Element_OphTrConsent_BestInterestDecision");
	if($bestInterestDecisionElement.length > 0) {
		new BestInterestDecisionController($bestInterestDecisionElement);
	}

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

	autosize($('.Element_OphTrConsent_BenefitsAndRisks textarea'));

	dialog = new OpenEyes.UI.Dialog();

	function openPopup(data)
        {
            dialog.content = data.html;
            dialog.open();
            document.getElementById("consent_delete_modal_no").addEventListener('click',() => {
                dialog.close();
            });
			document.getElementById("consent_delete_modal_cancel").addEventListener('click',() => {
                dialog.close();
            });
        }

	function openDeleteModalWindow(consentId){
		params = {id:consentId, YII_CSRF_TOKEN:YII_CSRF_TOKEN};
		const searchParams = Object.keys(params).map((key) => {
			return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
		}).join('&');

		fetch(baseUrl + "/" + moduleName + "/default/getDeleteConsentPopupContent",{
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
			},
			body: searchParams,
			method: 'POST'
		})
			.then(response => response.json())
			.then(data => {
				if(data) {
					openPopup(data);
				}
			});
	}

	$(document).on('click', '.delete-consent-button', function (e) {
		e.preventDefault();
		var id = this.getAttribute("data-id");
		openDeleteModalWindow(id);
	});

	$(document).on('click', '.withdraw-consent-button', function (e) {
		e.preventDefault();
		var id = this.getAttribute("data-id");
	});

    let consent_type = document.getElementById('Element_OphTrConsent_Type_type_id');
    if (consent_type !== null) {
        consent_type.onchange = function () {
            let url = window.location.href;
            if (url.match(/type_id/)) {
                url = url.replace(/&type_id=([0-9]+)/, '&type_id=' + this.value);
            } else {
                url = url + "&type_id=" + this.value;
            }
            window.formHasChanged = false;
            window.location.href = url;
        }
    }

    class AdditionalSignatures{
        constructor()
        {
            this.name_prefix = 'OEModule_OphTrConsent_models_Element_OphTrConsent_AdditionalSignatures';
            this.setWitnessPropreties();
            this.setInterpreterPropreties();
            this.setGuardianProperties();
        }

        setWitnessPropreties()
        {
            this.witness_required = document.getElementById('additional_signatures_witness_required');
            this.witness_required_hidden = document.getElementById(this.name_prefix + '_witness_required');
            this.witness_name = document.getElementById(this.name_prefix + '_witness_name');
            if (this.witness_required !== null ) {
                this.witness_required.addEventListener('change', evt => this.changeWitness(evt));
            }
        }

        setInterpreterPropreties()
        {
            this.interpreter_required = document.getElementById('additional_signatures_interpreter_required');
            this.interpreter_required_hidden = document.getElementById(this.name_prefix + '_interpreter_required');
            this.interpreter_name = document.getElementById(this.name_prefix + '_interpreter_name');
            if (this.interpreter_required !== null ) {
                this.interpreter_required.addEventListener('change', evt => this.changeInterpreter(evt));
            }
        }

        setGuardianProperties()
        {
            this.guardian_required = document.getElementById('additional_signatures_guardian_required');
            this.guardian_required_hidden = document.getElementById(this.name_prefix + '_guardian_required');
            this.guardian_name = document.getElementById(this.name_prefix + '_guardian_name');
            this.guardian_relationship = document.getElementById(this.name_prefix + '_guardian_relationship');
            if (this.guardian_required !== null ) {
                this.guardian_required.addEventListener('change', evt => this.changeGuardian(evt));
            }
        }

        changeWitness()
        {
            if (this.witness_required !== null ) {
                if (this.witness_required.checked) {
                    this.witness_name.disabled = false;
                    this.witness_required_hidden.value = 1;
                } else {
                    this.witness_name.value  = '';
                    this.witness_required_hidden.value = 0;
                    this.witness_name.disabled = true;
                }
            }
        }

        changeInterpreter()
        {
            if (this.interpreter_required !== null ) {
                if (this.interpreter_required.checked) {
                    this.interpreter_name.disabled = false;
                    this.interpreter_required_hidden.value = 1;
                } else {
                    this.interpreter_name.value = '';
                    this.interpreter_name.disabled = true;
                    this.interpreter_required_hidden.value = 0;
                }
            }
        }

        changeGuardian()
        {
            if (this.guardian_required !== null ) {
                if (this.guardian_required.checked) {
                    this.guardian_name.disabled = false;
                    this.guardian_relationship.disabled = false;
                    this.guardian_required_hidden.value = 1;
                } else {
                    this.guardian_name.disabled = true;
                    this.guardian_relationship.value = '';
                    this.guardian_relationship.disabled = true;
                    this.guardian_required_hidden.value = 0;
                }
            }
        }
    }

    const signatures = new AdditionalSignatures();
    signatures.changeWitness();
    signatures.changeInterpreter();
    signatures.changeGuardian();

	$("#clinical-create").submit(function(e) {
		if(proceed || !savenprint_clicked) {
			return true;
		} else {
			e.preventDefault();
			if(savenprint_clicked) {
				var missing_signatures = checkMissingSignatures();

				if(missing_signatures.length > 0) {
					var list = [];
					$.each(missing_signatures, function (i, str) {
							list.push("<li>" + str + "</li>");
					});
					var dlg = new OpenEyes.UI.Dialog.Confirm({
							content: "<p>One or more signatures are missing:</p><ul>" + list.join("\n") + "</ul><p>Do you wish to proceed?</p>"
					});
					dlg.on("ok", function (e) {
						proceed = true;
						var input = $("<input>")
								.attr("type", "hidden")
								.attr("name", "saveprint").val("");

						editors = $('textarea[data-richtext="true"]');
						if(editors.length > 0){
							for(i = 0; i < editors.length; i++){
								nic = nicEditors.findEditor( editors[i].getAttribute("id") );
								nic.saveContent();
								nic = null;
							}
						}

						$('#clinical-create').append(input).submit();
					});
					dlg.open();
					proceed = false;
				} else {
					proceed = true;
					var input = $("<input>")
							.attr("type", "hidden")
							.attr("name", "saveprint").val("");

					editors = $('textarea[data-richtext="true"]');
					if(editors.length > 0){
						for(i = 0; i < editors.length; i++){
							nic = nicEditors.findEditor( editors[i].getAttribute("id") );
							nic.saveContent();
							nic = null;
						}
					}

					$('#clinical-create').append(input).submit();
				}
			}
		}
	});

	$(this).on('click','.js-add-withdrawal',function(e) {
		window.location.href = '/OphTrConsent/default/withdraw?event_id='+OE_event_id;
	});
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

class BestInterestDecisionController {

	/**
	 * Object constructor
	 * @param {jQuery} $element
	 */
	constructor($element) {
        this.$element = $element;
		this.$uploadLabel = $element.find(".upload-label");
		this.$fileInput = $element.find(".js-file-input");
		this.$list = $element.find(".js-list");
		this.$list.attr("data-key", this.$list.children("tr").length);
		this.bindEvents();
    }

	/**
	 * Bind the required js events to DOM elements
	 */
	bindEvents() {
		let controller = this;
		controller.$uploadLabel
			.on("click", function () {
				controller.$fileInput.click();
			})
			.on("dragover", function (e) {
				e.preventDefault();
			})
			.on("drop", function (e) {
				e.preventDefault();
				const dt = e.originalEvent.dataTransfer;
				if (dt.items) {
					for (let i = 0; i < dt.items.length; i++) {
						if (dt.items[i].kind === 'file') {
							controller.addItem(dt.items[i].getAsFile());
						}
					}
				}
			});
		controller.$fileInput
			.on("change", function (e) {
				e.preventDefault();
				if (this.files) {
					for (let i = 0; i < this.files.length; i++) {
						controller.addItem(this.files[i]);
					}
				}
			});
		controller.$element
			.on("click", ".js-remove", function (e) {
				$(e.target).closest("tr").remove();
			})
			.on("click", ".js-retry", function (e) {
				let file = $(e.target).data("file");
				$(e.target).closest("tr").remove();
				controller.addItem(file);
			});
	}

	/**
	 * Add one file to the upload list and attempt uploading
	 * @param {File} file
	 */
	addItem(file) {
		let controller = this;
		const inputPrefix = "OEModule_OphTrConsent_models_Element_OphTrConsent_BestInterestDecision[attachments]";
		const key = this.getNextKey();
		disableButtons();
		let $newRow = $("<tr data-id='new'/>")
			.append('<input type="hidden" name="' + inputPrefix + '[' + key + '][tmp_name]" value="' + file.name + '" />')
			.append('<input type="hidden" name="' + inputPrefix + '[' + key + '][id]" value="new" />')
			.append('<input type="hidden" name="' + inputPrefix + '[' + key + '][protected_file_id]" value="" class="js-pf-id" />')
			.append("<td>" + file.name + "</td>")
			.append("<td>" + BestInterestDecisionController.getFileSizeString(file.size) + "</td>")
			.append("<td class='js-result-message'><i class=\"oe-i waiting small pad-right\"></i>In progress...</td>")
			.append("<td class='js-actions'><i class=\"oe-i trash js-remove\"></i></td>");
		$newRow.appendTo(controller.$list);
		let formData = new FormData();
		formData.append("file", file, file.name);
		formData.append("YII_CSRF_TOKEN", YII_CSRF_TOKEN);
		fetch("/OphTrConsent/Default/uploadFile", {
			method: 'POST',
			body: formData
		})
			.then(response => response.json())
			.then(data => {
				if(data.success) {
					$newRow.find("input.js-pf-id").val(data.protected_file_id);
					$newRow.find(".js-result-message").html("<i class=\"oe-i tick-green small pad-right\"></i>Attached");
				}
				else {
					let $btn = $("<button type='button' class='js-retry'>Try upload again</button>");
					$btn.data("file", file);
					$btn.prependTo($newRow.find(".js-actions"));
					$newRow.find(".js-result-message").html("<i class=\"oe-i cross-red small pad-right\"></i>Error: " + data.message);
				}
				enableButtons();
			})
			.catch(() => {
				$newRow.find(".js-result-message").html("<i class=\"oe-i cross-red small pad-right\"></i>Error: unable to attach");
				enableButtons();
			});
	}

	/**
	 * Returns the next row number in the list
	 * @return {int}
	 */
	getNextKey() {
		const key = parseInt(this.$list.attr("data-key"));
		this.$list.attr("data-key", key + 1);
		return key;
	}

	/**
	 * Returns the file size in human-readable form
	 * @param {int} sizeInBytes
	 * @return {string}
	 */
	static getFileSizeString(sizeInBytes) {
		const units = ['B','kB','MB','GB','TB'];
		const i = Math.floor(Math.log(sizeInBytes) / Math.log(1024));
		const unit = units[i];
		const precision = i <= 1 ? 0 : 2;
		return (sizeInBytes / Math.pow(1024, i)).toFixed(precision) + unit;
	}
}
