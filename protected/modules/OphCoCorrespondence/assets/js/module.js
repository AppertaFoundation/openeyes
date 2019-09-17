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

var correspondence_markprinted_url, correspondence_print_url;

function setDropDownWidth(id){
    var $option_obj;
    var option_width;
    var arrow_width = 30;
    
    $option_obj = $("<span>").html($('#' + id +' option:selected').text());
    $option_obj.appendTo('body');
    option_width = $option_obj.width();
    $option_obj.remove();

    $('#' + id).width(option_width + arrow_width);
}

function updateCorrespondence(macro_id)
{
    var nickname = $('input[id="ElementLetter_use_nickname"][type="checkbox"]').is(':checked') ? '1' : '0';
    var obj = $(this);

    if ( macro_id != '') {
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': baseUrl+'/OphCoCorrespondence/Default/getMacroData?patient_id=' + OE_patient_id + '&macro_id=' + macro_id + '&nickname=' + nickname,
            'success': function(data) {
                if (data['error'] == 'DECEASED') {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "The patient is deceased so this macro cannot be used."
                    }).open();
                    obj.val('');
                    return false;
                }
                $('#ElementLetter_cc').val('');
                $('#cc_targets').html('');
                correspondence_load_data(data);
                obj.val('');
                
                //set letter type
                $('.internal-referrer-wrapper').slideUp();
                $('#ElementLetter_letter_type_id').val(data.sel_letter_type_id).trigger('change');
                resetInternalReferralFields();

				$('#attachments_content_container').html(data.associated_content);

            }
        });
    }
	autosize($('.autosize'));
}

function togglePrintDisabled (isSignedOff) {
	$('#et_save_print').prop('disabled', !isSignedOff);
}

		/** Internal Referral **/
/**
 * Reset all Internal referral input fields
 */
function resetInternalReferralFields(){

    $('.internal-referral-section').find(':input').not(':button, :submit, :reset, :hidden').removeAttr('checked').removeAttr('selected').not(':checkbox, :radio, select').val('');

    $.each( $('.internal-referral-section select'), function(i, input){
        $(input).val('');
    });

    // set back the defaults
    $('#ElementLetter_is_same_condition_0').prop('checked', true);
    $('#ElementLetter_to_location_id').val(OE_to_location_id);
}

function setRecipientToInternalReferral(){
	$('#docman_recipient_0').attr('disabled', true);
	$('#DocumentTarget_0_attributes_contact_name').prop('readonly', true).val('Internal Referral');
	$('#Document_Target_Address_0').prop('readonly', true).val(internal_referral_booking_address);

    var $option = $('<option>',{value:'INTERNALREFERRAL',text:'Booking'});
    $('#DocumentTarget_0_attributes_contact_type').append($option);

	$('#DocumentTarget_0_attributes_contact_type').val('INTERNALREFERRAL');

    $('#dm_table tr:first-child td:last-child').html('Change the letter type <br> to amend this recipient').css({'font-size':'11px'});

    if( !$('#yDocumentTarget_0_attributes_contact_type').length ){
        var $input = $('<input>',{'type':'hidden','id':'yDocumentTarget_0_attributes_contact_type','name':'DocumentTarget[0][attributes][contact_type]'}).val('INTERNALREFERRAL');
        $('#DocumentTarget_0_attributes_contact_type').after($input);
    } else {
        $('#yDocumentTarget_0_attributes_contact_type').val('INTERNALREFERRAL');
    }
}

function resetRecipientFromInternalReferral(){
	$('#docman_recipient_0').attr('disabled', false).css({'background-color':'white'});
	$('#DocumentTarget_0_attributes_contact_name').prop('readonly', false).val('');
	$('#Document_Target_Address_0').prop('readonly', false).val('');

    $('#DocumentTarget_0_attributes_contact_type').find('option[value="INTERNALREFERRAL"]').remove();
	$('#DocumentTarget_0_attributes_contact_type').val('');

    $('#dm_table tr:first-child td:last-child').html('');

    //find the GP row and remove then select GP as TO recipient
    $('.docman_contact_type').each(function(i, $element){
        if( $($element).val() === "GP"){
            $element.closest('tr').remove();
        }
    });
    $('#docman_recipient_0 option:contains("GP")').val();
    $('#docman_recipient_0').val( $('#docman_recipient_0 option:contains("GP")').val() ).change();

}

function updateConsultantDropdown(subspecialty_id){


    jQuery.ajax({
        url: baseUrl + "/" + moduleName + "/Default/getConsultantsBySubspecialty",
        data: {"subspecialty_id": subspecialty_id },
        dataType: "json",
        beforeSend: function(){
            $('button#et_saveprint').prop('disabled', true);
            $('button#et_savedraft').prop('disabled', true);
        },
        success: function(data){
            var options = [];

            //remove old options
            $('#ElementLetter_to_firm_id option:gt(0)').remove();

            //create js array from obj to sort
            for(item in data){
                options.push([item,data[item]]);
            }

            options.sort(function(a,b){
                if (a[1] > b[1]) return -1;
                else if (a[1] < b[1]) return 1;
                else return 0;
            });
            options.reverse();

            //append new option to the dropdown
            $.each(options, function(key, value) {
                $('#ElementLetter_to_firm_id').append($("<option></option>")
                    .attr("value", value[0]).text(value[1]));
            });
        },
        complete: function(){
            $('button#et_saveprint').prop('disabled', false);
            $('button#et_savedraft').prop('disabled', false);
        }

    });
}

function updateSalutation(text){
    $("#ElementLetter_introduction").val(text);
}

$(document).ready(function() {
    $('#ElementLetter_to_subspecialty_id').on('change',function(){
        updateConsultantDropdown( $(this).val() );
        updateSalutation("Dear " + $(this).find('option:selected').text() + ' service,');
    });

    $('#ElementLetter_to_firm_id').on('change',function(){
        var reg_exp = /\(([^)]+)\)/;
        var subspecialty_name = reg_exp.exec( $(this).find('option:selected').text() )[1];
        var subspecialty_id;

        subspecialty_id = $('#ElementLetter_to_subspecialty_id').find('option:contains("' + subspecialty_name + '")').val();
        $('#ElementLetter_to_subspecialty_id').val(subspecialty_id);

        jQuery.ajax({
            url: baseUrl + "/" + moduleName + "/Default/getSalutationByFirm",
            data: { firm_id: $('#ElementLetter_to_firm_id').val(), },
            dataType: "json",
            beforeSend: function(){
                $('button#et_saveprint').prop('disabled', true);
                $('button#et_savedraft').prop('disabled', true);
            },
            success: function(data){
                updateSalutation(data);
            },
            complete: function(){
                $('button#et_saveprint').prop('disabled', false);
                $('button#et_savedraft').prop('disabled', false);
            }
        });

    });

    $('#ElementLetter_to_location_id').on('change', function(){

        jQuery.ajax({
            url: baseUrl + "/" + moduleName + "/Default/getSiteInfo",
            data: { to_location_id: $('#ElementLetter_to_location_id').val() },
            dataType: "json",
            beforeSend: function(){

                // empty the value of the address textarea because if the ajax slow the user may save a wrong address
                $('#Document_Target_Address_0').val('');
                $('button#et_saveprint').prop('disabled', true);
                $('button#et_savedraft').prop('disabled', true);
            },
            success: function(data){
                $('#Document_Target_Address_0').val(data.correspondence_name);
            },
            complete: function(){
                $('button#et_saveprint').prop('disabled', false);
                $('button#et_savedraft').prop('disabled', false);
            }
        });

    });


});

		/** End of Internal Referral **/



$(document).ready(function() {
	var $letterIsSignedOff = $('#ElementLetter_is_signed_off');
// leave this for a while until the requirements gets clear
// 	togglePrintDisabled($letterIsSignedOff.is(':checked'));
//     $letterIsSignedOff.change(function() {
//         togglePrintDisabled(this.checked);
//     });

	$(this).delegate('#ElementLetter_site_id', 'change', function() {
		if (correspondence_directlines) {
			$('#ElementLetter_direct_line').val(correspondence_directlines[$('#ElementLetter_site_id').val()]);
		}
	});

    $('#et_save').click(function(e){
		$('#'+event_form ).submit();
    });

    $('#et_saveprint').click(function(e){
        e.preventDefault();

        var event_button = $(this);
        var event_form = event_button.attr('form');
		$('#ElementLetter_draft').val(0);

        // ajax call to create php cookie
        $.get(baseUrl + '/OphCoCorrespondence/Default/savePrint?event_id='+OE_event_id, function() {
			// we need to know which button was clicked in ElementLetter.php, and the button doesn't get posted outside of the form
			$('#'+event_form ).append( $('<input>', {type: 'hidden', name: 'saveprint', value: '1'}) );
			disableButtons();
			$('#'+event_form ).submit();
        });
    });

    $('#et_savedraft').click(function(e){
        e.preventDefault();

        var event_button = $(this);
        var event_form = event_button.attr('form');
		disableButtons();
		$('#ElementLetter_draft').val(1);
		$('#'+event_form ).submit();
    });

    $(this).on('click','#et_cancel',function() {
		$('#dialog-confirm-cancel').dialog({
			resizable: false,
			//height: 140,
			modal: true,
			buttons: {
				"Yes, cancel": function() {
					$(this).dialog('close');

					disableButtons();

					if (m = window.location.href.match(/\/update\/[0-9]+/)) {
						window.location.href = window.location.href.replace('/update/','/view/');
					} else {
						window.location.href = baseUrl+'/patient/summary/'+OE_patient_id;
					}
				},
				"No, go back": function() {
					$(this).dialog('close');
					return false;
				}
			}
		});
	});


	$('#address_target').change(function() {
		var nickname = $('input[id="ElementLetter_use_nickname"][type="checkbox"]').is(':checked') ? '1' : '0';

		if ($(this).children('option:selected').val() != '') {
			if ($(this).children('option:selected').text().match(/NO ADDRESS/)) {

				new OpenEyes.UI.Dialog.Alert({
					content: "Sorry, this contact has no address so you can't send a letter to them."
				}).open();

				$(this).val(selected_recipient);
				return false;
			}

			var val = $(this).children('option:selected').val();

			if (re_field == null) {
				if ($('#re_default').length >0) {
					re_field = $('#re_default').val();
				} else {
					re_field = $('#ElementLetter_re').val();
				}
			}

			var target = $(this);

			$.ajax({
				'type': 'GET',
				'dataType': 'json',
				'url': baseUrl+'/OphCoCorrespondence/Default/getAddress?patient_id='+OE_patient_id+'&contact='+val+'&nickname='+nickname,
				'success': function(data) {

					$('#ElementLetter_address').attr('readonly', (data['contact_type'] == 'Gp'));

					if (data['error'] == 'DECEASED') {

						new OpenEyes.UI.Dialog.Alert({
							content: "This patient is deceased and cannot be written to."
						}).open();

						target.val(selected_recipient);
						return false;
					}

					if (val.match(/^Patient/)) {
						$('#ElementLetter_re').val('');
						$('#ElementLetter_re').parent().parent().hide();
					} else {
						if (re_field != null) {
							$('#ElementLetter_re').val(re_field);
							$('#ElementLetter_re').parent().parent().show();
						}
					}

					correspondence_load_data(data);
					selected_recipient = val;

					// try to remove the selected recipient's address from the cc field
					if ($('#ElementLetter_cc').val().length >0) {
						$.ajax({
							'type': 'GET',
							'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact='+val,
							'success': function(text) {
								if (text.match(/DECEASED/)) {
									new OpenEyes.UI.Dialog.Alert({
										content: "This patient is deceased and cannot be cc'd."
									}).open();
									target.val(selected_recipient);
									return false;
								} else if (!text.match(/NO ADDRESS/)) {
									if ($('#ElementLetter_cc').val().length >0) {
										var cur = $('#ElementLetter_cc').val();

										if (cur.indexOf(text) != -1) {
											var strings = cur.split("\n");
											var replace = '';

											for (var i in strings) {
												if (strings[i].length >0 && strings[i].indexOf(text) == -1) {
													if (replace.length >0) {
														replace += "\n";
													}
													replace += $.trim(strings[i]);
												}
											}

											$('#ElementLetter_cc').val(replace);
										}
									}

									var targets = '';

									$('#cc_targets').children().map(function() {
										if ($(this).val() != val) {
											targets += '<input type="hidden" name="CC_Targets[]" value="'+$(this).val()+'" />';
										}
									});
									$('#cc_targets').html(targets);
								}
							}
						});
					}

					// if the letter is to anyone but the GP we need to cc the GP
					if (!val.match(/^Gp|^Practice/)) {
						var contact;
						if (OE_gp_id) {
							contact = 'Gp' + OE_gp_id;
						}
						else if (OE_practice_id) {
							contact = 'Practice' + OE_practice_id;
						}
						if (contact) {
							$.ajax({
								'type': 'GET',
								'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact='+contact,
								'success': function(text) {
									if (!text.match(/NO ADDRESS/)) {
										if ($('#ElementLetter_cc').val().length >0) {
											var cur = $('#ElementLetter_cc').val();

											if (cur.indexOf(text) == -1) {
												if (!$('#ElementLetter_cc').val().match(/[\n\r]$/)) {
													cur += "\n";
												}

												$('#ElementLetter_cc').val(cur+text);
												$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="gp" />');
											}

										} else {
											$('#ElementLetter_cc').val(text);
											$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="gp" />');
										}
									} else {
										new OpenEyes.UI.Dialog.Alert({
											content: "Warning: letters should be cc'd to the patient's GP, but the current patient's GP has no valid address."
										}).open();
									}
								}
							});
						}
					} else {
						// if the letter is to the GP we need to cc the patient
						$.ajax({
							'type': 'GET',
							'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact=Patient'+OE_patient_id,
							'success': function(text) {
								if (text.match(/DECEASED/)) {
									new OpenEyes.UI.Dialog.Alert({
										content: "The patient is deceased so cannot be cc'd."
									}).open();
									target.val(selected_recipient);
									return false;
								} else if (!text.match(/NO ADDRESS/)) {
									if ($('#ElementLetter_cc').val().length >0) {
										var cur = $('#ElementLetter_cc').val();

										if (cur.indexOf(text) == -1) {
											if (!$('#ElementLetter_cc').val().match(/[\n\r]$/)) {
												cur += "\n";
											}

											$('#ElementLetter_cc').val(cur+text);
											$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="patient" />');
										}

									} else {
										$('#ElementLetter_cc').val(text);
										$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="patient" />');
									}
								} else {
									new OpenEyes.UI.Dialog.Alert({
										content: "Warning: letters to the GP should be cc'd to the patient's, but the patient has no valid address."
									}).open();
								}
							}
						});
					}
				}
			});
		}
	});

	$('input[id="ElementLetter_use_nickname"][type="checkbox"]').click(function() {
		$('#address_target').change();
	});

	$('select.stringgroup').change(function() {
		var obj = $(this);
		var selected_val = $(this).children('option:selected').val();

		if (selected_val != '') {
			var m = selected_val.match(/^([a-z]+)([0-9]+)$/);

			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/Default/getString?patient_id='+OE_patient_id+'&string_type='+m[1]+'&string_id='+m[2],
				'success': function(text) {
					element_letter_controller.addAtCursor(text.replace(/\n/g, "<br>"));
					obj.val('');
				}
			});
		}
	});

	$('#cc').change(function() {
		var contact_id = $(this).children('option:selected').val();
		var obj = $(this);

		if (contact_id != '') {
			var ok = true;

			$('#cc_targets').children('input').map(function() {
				if ($(this).val() == contact_id) {
					ok = false;
				}
			});

			if (!ok) {
				if (obj.val().match(/^Patient/)) {
					var found = false;
					$.each($('#ElementLetter_cc').val().split("\n"),function(key, value) {
						if (value.match(/^Patient: /)) {
							found = true;
						}
					});
					if (found) {
						obj.val('');
						return true;
					}
				} else if (obj.val().match(/^Gp/)) {
					var found = false;
					$.each($('#ElementLetter_cc').val().split("\n"),function(key, value) {
						if (value.match(/^GP: /)) {
							found = true;
						}
					});
					if (found) {
						obj.val('');
						return true;
					}
				} else {
					obj.val('');
					return true;
				}
			}

			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact='+contact_id,
				'success': function(text) {
					if (text.match(/DECEASED/)) {
						new OpenEyes.UI.Dialog.Alert({
							content: "The patient is deceased so cannot be cc'd."
						}).open();
						obj.val('');
						return false;
					} else if (!text.match(/NO ADDRESS/)) {
						if ($('#ElementLetter_cc').val().length >0) {
							var cur = $('#ElementLetter_cc').val();

							if (!$('#ElementLetter_cc').val().match(/[\n\r]$/)) {
								cur += "\n";
							}

							$('#ElementLetter_cc').val(cur+text);
						} else {
							$('#ElementLetter_cc').val(text);
						}

						$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="'+contact_id+'" />');
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Sorry, this contact has no address and so cannot be cc'd."
						}).open();
					}

					obj.val('');
				}
			});
		}
	});

	$('#ElementLetter_body').unbind('keyup').bind('keyup',function() {
		if (m = $(this).val().match(/\[([a-z]{3})\]/i)) {

			var text = $(this).val();

			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/OphCoCorrespondence/Default/expandStrings',
				'data': 'patient_id='+OE_patient_id+'&text='+text+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp) {
						element_letter_controller.setContent(resp);
					}
				}
			});
		}
	});

	if ($('#OphCoCorrespondence_printLetter').val() == 1) {
		if ($('#OphCoCorrespondence_printLetter_all').val() == 1) {
			setTimeout("OphCoCorrespondence_do_print(true);",1000);
		} else {
			setTimeout("OphCoCorrespondence_do_print(false);",1000);
		}
	}

	$(this).on('click','#et_print',function(e) {
		if ($('#correspondence_out').hasClass('draft')) {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/default/doPrint/'+OE_event_id,
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to print the letter, please try again or contact support for assistance."
						}).open();
					}
				}
			});
		} else {
			OphCoCorrespondence_do_print(false);
			e.preventDefault();
		}
	});

  $(this).on('click','#et_print_all',function(e) {
		if ($('#correspondence_out').hasClass('draft')) {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/default/doPrint/'+OE_event_id+'?all=1',
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to print the letter, please try again or contact support for assistance."
						}).open();
					}
				}
			});
		} else {
			OphCoCorrespondence_do_print(true);
			e.preventDefault();
		}
	});

	$(this).on('click','#et_confirm_printed',function() {
		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/OphCoCorrespondence/Default/confirmPrinted/'+OE_event_id,
			'success': function(html) {
				if (html != "1") {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, something went wrong. Please try again or contact support for assistance."
					}).open();
					enableButtons();
				} else {
					location.reload(true);
				}
			}
		});
	});

	$('button.addEnclosure').die('click').live('click',function() {
		var id = -1;
		$('#enclosureItems').find('.enclosureItem input').each(function() {
			var m = $(this).attr('name').match(/[0-9]+/);
			if (parseInt(m[0]) > id) {
				id = parseInt(m[0]);
			}
		});

		id += 1;

		var html = [
			'<div class="data-group collapse in enclosureItem flex-layout">',
			'			<input type="text" class="cols-full" value="" autocomplete="' + window.OE_html_complete + '" name="EnclosureItems[enclosure'+id+']">',
			'			<i class="oe-i trash removeEnclosure"></i>',
			'	</div>'
		].join('');

		$('#enclosureItems').append(html).show();
		$('input[name="EnclosureItems[enclosure'+id+']"]').select().focus();
	});

	$('i.removeEnclosure').die('click').live('click',function(e) {
		$(this).closest('.enclosureItem').remove();
		if (!$('#enclosureItems').children().length) {
			$('#enclosureItems').hide();
		}
		e.preventDefault();
	});

	$('div.enclosureItem input').die('keypress').live('keypress',function(e) {
		if (e.keyCode == 13) {
			$('button.addEnclosure').click();
			return false;
		}
		return true;
	});

	var selected_recipient = $('#address_target').val();
        
	if( $('#dm_table').length > 0 ){
        // we have docman table here
        docman2 = docman;
        docman2.baseUrl = location.protocol + '//' + location.host + '/docman/'; // TODO add this to the config!
        docman2.setDOMid('docman_block','dm_');
        docman2.module_correspondence = 1;

        docman2.init();
    }

    $('#ElementLetter_letter_type_id').on('change', function(){
		if( $(this).find('option:selected').text() == 'Internal Referral' ){
            $('.internal-referrer-wrapper').slideDown();
            setRecipientToInternalReferral();

            if( typeof docman !== "undefined" && !$('#macro_id').val()){

                //add GP to recipients
                if( !docman.isContactTypeAdded("GP") ){
                    docman.createNewRecipientEntry('GP');
                }

                //add Patient to recipients
                if( !docman.isContactTypeAdded("PATIENT") ){
                    docman.createNewRecipientEntry('PATIENT');
                }
            }

		} else if($('.internal-referrer-wrapper').is(':visible')) {
            $('.internal-referrer-wrapper').slideUp();
            resetInternalReferralFields();
            resetRecipientFromInternalReferral();

            if (typeof docman !== "undefined"){

                if( typeof docman.setDeliveryMethods === 'function') {
                    docman.setDeliveryMethods(0);
                }
		    }
        }


        //call the setDeliveryMethods with row index 0 as the Internal referral will be the main recipient
        //we have to trigger to set it
        docman.setDeliveryMethods(0);
	})

	$('#attachments_content_container').on('click', 'i.trash', function(e) {
		e.preventDefault();
        $(this).closest('tr').remove();
	});
});

function savePDFprint( module , event_id , $content, $data_id, title)
{
	if(typeof title == 'undefined'){
		title = '';
	}

    disableButtons();
    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/'+module+'/Default/savePDFprint/' + event_id + '?ajax=1&auto_print=0&pdf_documents=1&attachment_print_title='+title,
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-Requested-With', 'pdfprint');
        },
        'success': function(response) {
            if(response.success == 1){
                $hidden = '<input type="hidden" name="file_id[' + $data_id+ ']" value="'+response.file_id+'" />';
                $content.prepend($hidden);
            }
        },
		'complete': function(){
            enableButtons();
		}
    });
}

var re_field = null;

function correspondence_load_data(data) {
	for (var i in data) {
		if (m = i.match(/^text_(.*)$/)) {
			if (m[1] == 'ElementLetter_body'){
        element_letter_controller.setContent(data[i]);
			} else {
        $('#'+m[1]).val(data[i]);
      }
		} else if (m = i.match(/^sel_(.*)$/)) {
			if (m[1] == 'address_target') {
				if (data[i].match(/^Patient/)) {
					$('#ElementLetter_re').val('');
					$('#ElementLetter_re').parent().parent().hide();
				} else {
					if (re_field != null) {
						$('#ElementLetter_re').val(re_field);
						$('#ElementLetter_re').parent().parent().show();
					}
				}
			}
			$('#'+m[1]).val(data[i]);
		} else if (m = i.match(/^check_(.*)$/)) {
			$('input[id="'+m[1]+'"][type="checkbox"]').attr('checked',(parseInt(data[i]) == 1 ? true : false));
		} else if (m = i.match(/^textappend_(.*)$/)) {
			$('#'+m[1]).val($('#'+m[1]).val()+data[i]);
		} else if (m = i.match(/^hidden_(.*)$/)) {
			$('#'+m[1]).val(data[i]);
		} else if (m = i.match(/^elementappend_(.*)$/)) {
			$('#'+m[1]).append(data[i]);
		} else if (i == 'alert') {
			new OpenEyes.UI.Dialog.Alert({
				content: data[i]
			}).open();
		}
	}
}

function correspondence_append_body(text) {
  element_letter_controller.appendContent(text);
}

function ucfirst(str) {
	str += '';
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1);
}

function uclower(str) {
	str += '';
	var f = str.charAt(0).toLowerCase();
	return f + str.substr(1);
}

function inArray(needle, haystack) {
	var length = haystack.length;
	for (var i = 0; i < length; i++) {
		if (haystack[i] == needle) return true;
	}
	return false;
}

function OphCoCorrespondence_do_print(all) {

  var data = {};
  if (all) {
    data['all'] = 1;
  }

  if ($('#OphCoCorrespondence_print_checked').length && $('#OphCoCorrespondence_print_checked').val() == 1) {
    data['OphCoCorrespondence_print_checked'] = 1;

    // remove OphCoCorrespondence_print_checked, so Print and Print all will do what it says
    $('#OphCoCorrespondence_print_checked').remove();

  }

  $.ajax({
    'type': 'GET',
    'url': correspondence_markprinted_url,
    'success': function (html) {
      printEvent(data);
      enableButtons();
    }
  });
}

function OphCoCorrespondence_addAttachments(selectedItems){
	if(selectedItems.length) {
		disableButtons();
		for (let key in selectedItems) {
			$.ajax({
				'type': 'POST',
				'url': baseUrl + '/OphCoCorrespondence/Default/getInitMethodDataById',
				'data': { 'YII_CSRF_TOKEN': YII_CSRF_TOKEN, id: selectedItems[key].id, 'patient_id': OE_patient_id},
				'success': function (response) {
					if (response.success == 1) {
						let $table = $('#correspondence_attachments_table').find('tbody');

						let $data_id = parseInt($table.children().length);
						let $content = $(response.content);
						const title = $content.find('.attachments_display_title').val();

						$table.append($content);
						$content.attr('data-id', $data_id);
						$content.find('.attachments_event_id').attr('name', 'attachments_event_id[' + $data_id + ']');
						$content.find('.attachments_display_title').attr('name', 'attachments_display_title[' + $data_id + ']');

						$.ajax({
							'type': 'GET',
							'url': baseUrl + '/' + response.module + '/Default/savePDFprint/' + $content.find('.attachments_event_id').val() + '?ajax=1&auto_print=0&pdf_documents=1&attachment_print_title='+title,
							beforeSend: function(xhr) {
								xhr.setRequestHeader('X-Requested-With', 'pdfprint');
							},
							'success': function(response) {
								if(response.success == 1){
									$hidden = '<input type="hidden" name="file_id[' + $data_id+ ']" value="'+response.file_id+'" />';
									$content.append($hidden);
								}
							},
							'complete': function(){
								enableButtons();
							}
						});

						enableButtons();
					}
				}
			});
		}
	}
}
