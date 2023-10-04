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

function setDropDownWidth(id) {
    var $option_obj;
    var option_width;
    var arrow_width = 30;

    $option_obj = $("<span>").html($('#' + id + ' option:selected').text());
    $option_obj.appendTo('body');
    option_width = $option_obj.width();
    $option_obj.remove();

    $('#' + id).width(option_width + arrow_width);
}

function updateCorrespondence(macro_id) {
    var nickname = $('input[id="ElementLetter_use_nickname"][type="checkbox"]').is(':checked') ? '1' : '0';
    var obj = $(this);

    if (macro_id != '') {
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': baseUrl + '/OphCoCorrespondence/Default/getMacroData?patient_id=' + OE_patient_id + '&macro_id=' + macro_id + '&nickname=' + nickname,
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

function OphCoCorrespondence_attachmentStatusRequestError(row) {
    const status = row.querySelector('.attachment_status');
    const tooltip_content = 'Temporary error, please try again. If the error still occurs, please contact support.';

    status.innerHTML = '<i class="oe-i cross-red small pad-right"></i>Unable to attach' +
        '<i class="oe-i oe-i info small pad js-has-tooltip" data-tooltip-content="' + tooltip_content + '"></i>';
    row.querySelector('.reprocess_btn').style.display = '';
}

function OphCoCorrespondence_getAttachmentStatus(module_name, row, data_id) {
    let event_id = row.querySelector('.attachments_event_id').value;
    let title = row.querySelector('.attachments_display_title').value;

    const request = new XMLHttpRequest();
    request.open('GET', baseUrl + '/' + module_name + '/Default/savePDFprint/' + event_id + '?ajax=1&auto_print=0&pdf_documents=1&attachment_print_title=' + title, true);
    request.setRequestHeader('X-Requested-With', 'pdfprint');

    request.onload = function() {
        if (this.status >= 200 && this.status < 400) {
            const response = JSON.parse(this.response);

            if (response.success == 1) {
                const status = row.querySelector('.attachment_status');
                status.innerHTML = '<i class="oe-i tick-green small pad-right"></i>Attached';
                const hidden = OpenEyes.Util.htmlToElement('<input type="hidden" name="file_id[' + data_id + ']" value="' + response.file_id + '" />');

                row.appendChild(hidden);
                row.querySelector('.reprocess_btn').style.display = 'none';
            }
        } else {
            OphCoCorrespondence_attachmentStatusRequestError(row);
        }

        enableButtons();
    };

    request.onerror = function() {
        OphCoCorrespondence_attachmentStatusRequestError(row);
    };

    request.send();
}

function OphCoCorrespondence_addAttachment(event_id) {
    const request = new XMLHttpRequest();
    request.open('POST', baseUrl + '/OphCoCorrespondence/Default/getInitMethodDataById', true);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');

    request.onload = function() {
        if (this.status >= 200 && this.status < 400) {
            const response = JSON.parse(this.response);

            if (response.success == 1) {
                const table = document.getElementById('correspondence_attachments_table').querySelector('tbody');
                const data_id = table.children.length;
                const content = OpenEyes.Util.htmlToElement(response.content);

                table.appendChild(content);
                content.setAttribute('data-id', data_id);
                content.querySelector('.attachments_event_id').setAttribute('name', 'attachments_event_id[' + data_id + ']');
                content.querySelector('.attachments_display_title').setAttribute('name', 'attachments_display_title[' + data_id + ']');
                OphCoCorrespondence_getAttachmentStatus(response.module, content, data_id);
            }
        }
    };

    request.send("YII_CSRF_TOKEN=" + encodeURIComponent(YII_CSRF_TOKEN) +
        '&id=' + encodeURIComponent(event_id) +
        '&patient_id=' + encodeURIComponent(OE_patient_id));
}

function togglePrintDisabled(isSignedOff) {
    $('#et_save_print').prop('disabled', !isSignedOff);
}

/** Internal Referral **/
/**
 * Reset all Internal referral input fields
 */
function resetInternalReferralFields() {

    $('.internal-referral-section').find(':input').not(':button, :submit, :reset, :hidden').removeAttr('checked').removeAttr('selected').not(':checkbox, :radio, select').val('');

    $.each($('.internal-referral-section select'), function(i, input) {
        $(input).val('');
    });

    // set back the defaults
    $('#ElementLetter_is_same_condition_0').prop('checked', true);
    $('#ElementLetter_to_location_id').val(OE_to_location_id);
}

function setRecipientToInternalReferral() {
    $('#docman_recipient_0').attr('disabled', true);
    $('#DocumentTarget_0_attributes_contact_name').prop('readonly', true).val('Internal Referral');
    $('#Document_Target_Address_0').prop('readonly', true).val(internal_referral_booking_address);

    var $option = $('<option>', { value: 'INTERNALREFERRAL', text: 'Booking' });
    $('#DocumentTarget_0_attributes_contact_type').append($option);

    $('#DocumentTarget_0_attributes_contact_type').val('INTERNALREFERRAL');

    $('#dm_table tr:first-child td:last-child').html('<i class="oe-i info js-has-tooltip" data-tooltip-content="Change the letter type <br> to amend this recipient"></i>').css({ 'font-size': '11px' });

    if (!$('#yDocumentTarget_0_attributes_contact_type').length) {
        var $input = $('<input>', {
            'type': 'hidden',
            'id': 'yDocumentTarget_0_attributes_contact_type',
            'name': 'DocumentTarget[0][attributes][contact_type]'
        }).val('INTERNALREFERRAL');
        $('#DocumentTarget_0_attributes_contact_type').after($input);
    } else {
        $('#yDocumentTarget_0_attributes_contact_type').val('INTERNALREFERRAL');
    }

    $('#DocumentTarget_0_attributes_contact_id').val('');

    resetEmailField(0);

    // get the email address for internal referrals.
    getInternalReferralOutputType();
}

function resetRecipientFromInternalReferral() {
    $('#docman_recipient_0').attr('disabled', false).css({ 'background-color': 'white' });
    $('#DocumentTarget_0_attributes_contact_name').prop('readonly', false).val('');
    $('#Document_Target_Address_0').prop('readonly', false).val('');

    $('#DocumentTarget_0_attributes_contact_type').find('option[value="INTERNALREFERRAL"]').remove();
    $('#DocumentTarget_0_attributes_contact_type').val('');

    $('#dm_table tr:first-child td:last-child').html('');

    //find the GP row and remove then select GP as TO recipient
    $('.docman_contact_type').each(function(i, $element) {
        if ($($element).val() === "GP") {
            $element.closest('tr').remove();
        }
    });
    $('#docman_recipient_0 option:contains("GP")').val();
    $('#docman_recipient_0').val($('#docman_recipient_0 option:contains("GP")').val()).change();

}

function setConsultantDropdown(data) {
    var options = [];

    //remove old options
    $('#ElementLetter_to_firm_id option:gt(0)').remove();

    //create js array from obj to sort
    for (item in data) {
        options.push([item, data[item]]);
    }

    options.sort(function(a, b) {
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
}

function updateConsultantDropdown(site_id, subspecialty_id) {
    $.ajax({
        url: baseUrl + "/" + moduleName + "/Default/getConsultantsBySiteAndSubspecialty",
        data: { "site_id": site_id, "subspecialty_id": subspecialty_id, "check_service_firms_filter_setting": true },
        dataType: "json",
        beforeSend: function() {
            $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', true);
            $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', true);
        },
        success: setConsultantDropdown,
        complete: function() {
            $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', false);
            $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', false);
        }

    });
}

function updateSalutation(text) {
    $("#ElementLetter_introduction").val(text);
}

function updateEmailAndDeliveryMethod(text) {
    let shouldEmailImmediatelyDeliveryUpdate = false;
    // when the email admin parameter is set to on.
    if (send_email_immediately === 'on') {
        shouldEmailImmediatelyDeliveryUpdate = true;
    }
    // when only the email (delayed) admin parameter is set to on.
    if (send_email_immediately === 'off' && send_email_delayed === 'on') {
        shouldEmailImmediatelyDeliveryUpdate = false;
    }

    let $email = $('#DocumentTarget_0_attributes_email');
    // If the text is equals to the internal_referral_method_label js variable or the default text "Electronic"
    // then it means the email does not exist in the system, so the output_type is internalreferral.
    if (text === internal_referral_method_label || text === "Electronic") {
        // Internal Referral is always the first recipient.
        $('tr[data-rowindex=0]').find('input[value="Email (Delayed)"]').parents().eq(1).hide();
        $('input[name = "DocumentTarget_0_DocumentOutput_0_output_type"]').val('Internalreferral');
        $('input[name = "DocumentTarget[0][DocumentOutput][0][output_type]"]').val('Internalreferral');
        $("label.inline.highlight.electronic-label.internal-referral span").text(text);
        $email.val('');
    } else {
        $('tr[data-rowindex=0]').find('input[value="Email (Delayed)"]').parents().eq(1).show();
        if (shouldEmailImmediatelyDeliveryUpdate) {
            $("label.inline.highlight.electronic-label.internal-referral span").text('Email: ' + text);
            $('input[name = "DocumentTarget_0_DocumentOutput_0_output_type"]').val('Email');
            $('input[name = "DocumentTarget[0][DocumentOutput][0][output_type]"]').val('Email');
        }
        // Setting the value of the email element.
        $email.val(text);
    }
}

function getInternalReferralOutputType() {
    $.ajax({
        url: baseUrl + "/" + moduleName + "/Default/getInternalReferralOutputType",
        data: {
            subspecialty_id: $('#ElementLetter_to_subspecialty_id').val(),
            firm_id: $('#ElementLetter_to_firm_id').val()
        },
        dataType: "json",
        beforeSend: function() {
            $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', true);
            $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', true);
        },
        success: function(data) {
            if (data != null) {
                updateEmailAndDeliveryMethod(data.output_type);
            } else {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to fetch the email address for the internal referral letter type.\n\nPlease contact support for assistance."
                }).open();
            }
        },
        complete: function() {
            $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', false);
            $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', false);
        },
        error: function() {
            new OpenEyes.UI.Dialog.Alert({
                content: "Sorry, an internal error occurred and we were unable to fetch the email address for the internal referral letter type.\n\nPlease contact support for assistance."
            }).open();
        }
    });
}

$(document).ready(function() {
    $('#ElementLetter_to_subspecialty_id').on('change', function() {
        updateConsultantDropdown($('#ElementLetter_to_location_id').val(), $(this).val());
        updateSalutation("Dear " + $(this).find('option:selected').text() + ' service,');
        // Setting the firm_id to '' as on changing the subspecialty the first option i.e. None gets selected.
        $('#ElementLetter_to_firm_id').val('');
        // get the email address for internal referrals.
        getInternalReferralOutputType();
    });

    $('#ElementLetter_to_firm_id').on('change', function() {
        var reg_exp = /\(([^)]+)\)/;
        var subspecialty_name = reg_exp.exec($(this).find('option:selected').text());
        if (subspecialty_name) {
            var subspecialty_id;

            subspecialty_id = $('#ElementLetter_to_subspecialty_id').find('option:contains("' + subspecialty_name[1] + '")').val();
            $('#ElementLetter_to_subspecialty_id').val(subspecialty_id);
        }

        $.ajax({
            url: baseUrl + "/" + moduleName + "/Default/getSalutationByFirm",
            data: { firm_id: $('#ElementLetter_to_firm_id').val(), },
            dataType: "json",
            beforeSend: function() {
                $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', true);
                $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', true);
            },
            success: function(data) {
                if (data != null) {
                    updateSalutation(data);
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Sorry, an internal error occurred and we were unable to get the salutation.\n\nPlease contact support for assistance."
                    }).open();
                }
            },
            complete: function() {
                $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', false);
                $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', false);
            },
            error: function() {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to get the salutation.\n\nPlease contact support for assistance."
                }).open();
            }
        });
        // get the email address for internal referrals.
        getInternalReferralOutputType();
    });

    $('#ElementLetter_to_location_id').on('change', function() {
        if ($('#ElementLetter_to_location_id').val() !== '') {
            $.ajax({
                url: baseUrl + "/" + moduleName + "/Default/getSiteInfo",
                data: { to_location_id: $('#ElementLetter_to_location_id').val(), subspecialty_id: $('#ElementLetter_to_subspecialty_id').val(), check_service_firms_filter_setting: true },
                dataType: "json",
                beforeSend: function() {

                    // empty the value of the address textarea because if the ajax slow the user may save a wrong address
                    $('#Document_Target_Address_0').val('');
                    $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', true);
                    $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', true);

                    $('#ElementLetter_to_firm_id').children('option[value!=""]').remove();
                    $('#ElementLetter_to_firm_id').attr('disabled', true);
                },
                success: function(data) {
                    $('#Document_Target_Address_0').val(data.site.correspondence_name);
                    setConsultantDropdown(data.firms);
                },
                complete: function() {
                    $('button#et_saveprint, button#et_saveprint_footer').prop('disabled', false);
                    $('button#et_savedraft, button#et_savedraft_footer').prop('disabled', false);

                    $('#ElementLetter_to_firm_id').attr('disabled', false);
                }
            });
        } else {
            $('#Document_Target_Address_0').val('');

            $('#ElementLetter_to_firm_id').children('option[value!=""]').remove();
            $('#ElementLetter_to_firm_id').attr('disabled', true);
        }
    });

    $('#ElementLetter_to_firm_id').attr('disabled', $('#ElementLetter_to_location_id').val() === '');
});

/** End of Internal Referral **/


$(document).ready(function() {
    var $letterIsSignedOff = $('#ElementLetter_is_signed_off');
    // leave this for a while until the requirements gets clear
    // 	togglePrintDisabled($letterIsSignedOff.is(':checked'));
    //     $letterIsSignedOff.change(function() {
    //         togglePrintDisabled(this.checked);
    //     });

    let site_element = document.getElementById('ElementLetter_site_id');
    if (site_element) {
        site_element.addEventListener('change', function (e) {
            updateLineAndFax(site_element);
        });
        updateLineAndFax(site_element);
    }

    $('#et_save, #et_save_footer').click(function(e) {
        $('#' + event_form).submit();
    });

    $('#et_saveprint, #et_saveprint_footer').click(function(e) {
        e.preventDefault();

        var event_button = $(this);
        var event_form = event_button.attr('form');
        $('#ElementLetter_draft').val(0);
        $('#' + event_form).append($('<input>', { type: 'hidden', name: 'saveprint', value: '1' }));
        disableButtons();
        $('#' + event_form).submit();
    });

    $('#et_savedraft, #et_savedraft_footer').click(function(e) {
        e.preventDefault();

        var event_button = $(this);
        var event_form = event_button.attr('form');
        disableButtons();
        $('#ElementLetter_draft').val(1);
        $('#' + event_form).submit();
    });

    $(this).on('click', '#et_cancel', function() {
        $('#dialog-confirm-cancel').dialog({
            resizable: false,
            //height: 140,
            modal: true,
            buttons: {
                "Yes, cancel": function() {
                    $(this).dialog('close');

                    disableButtons();

                    if (m = window.location.href.match(/\/update\/[0-9]+/)) {
                        window.location.href = window.location.href.replace('/update/', '/view/');
                    } else {
                        window.location.href = baseUrl + '/patient/summary/' + OE_patient_id;
                    }
                },
                "No, go back": function() {
                    $(this).dialog('close');
                    return false;
                }
            }
        });
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
                'url': baseUrl + '/OphCoCorrespondence/Default/getString?patient_id=' + OE_patient_id + '&string_type=' + m[1] + '&string_id=' + m[2],
                'success': function(text) {
                    element_letter_controller.addAtCursor(text.replace(/\n(?!<)/g, '<br>'));
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
                    $.each($('#ElementLetter_cc').val().split("\n"), function(key, value) {
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
                    $.each($('#ElementLetter_cc').val().split("\n"), function(key, value) {
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
                'url': baseUrl + '/OphCoCorrespondence/Default/getCc?patient_id=' + OE_patient_id + '&contact=' + contact_id,
                'success': function(text) {
                    if (text.match(/DECEASED/)) {
                        new OpenEyes.UI.Dialog.Alert({
                            content: "The patient is deceased so cannot be cc'd."
                        }).open();
                        obj.val('');
                        return false;
                    } else if (!text.match(/NO ADDRESS/)) {
                        if ($('#ElementLetter_cc').val().length > 0) {
                            var cur = $('#ElementLetter_cc').val();

                            if (!$('#ElementLetter_cc').val().match(/[\n\r]$/)) {
                                cur += "\n";
                            }

                            $('#ElementLetter_cc').val(cur + text);
                        } else {
                            $('#ElementLetter_cc').val(text);
                        }

                        $('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="' + contact_id + '" />');
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

    $('#ElementLetter_body').unbind('keyup').bind('keyup', function() {
        if (m = $(this).val().match(/\[([a-z]{3})\]/i)) {

            var text = $(this).val();

            $.ajax({
                'type': 'POST',
                'url': baseUrl + '/OphCoCorrespondence/Default/expandStrings',
                'data': 'patient_id=' + OE_patient_id + '&text=' + text + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
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
            setTimeout("OphCoCorrespondence_do_print(true);", 1000);
        } else {
            setTimeout("OphCoCorrespondence_do_print(false);", 1000);
        }
    }

    this.addEventListener('click', function(e) {
        for (var target = e.target; target && target != this; target = target.parentNode) {
            if (target.matches('.reprocess_btn')) {
                const row = target.closest('tr');
                const status = row.querySelector('.attachment_status');
                const class_name = row.querySelector('.attachments_event_class_name').value;
                const data_id = row.getAttribute('data-id');

                status.innerHTML = '<i class="oe-i waiting small pad-right"></i>Pending...';
                OphCoCorrespondence_getAttachmentStatus(class_name, row, data_id);
            }
        }
    }, false);

    $(this).on('click', '#et_print', function(e) {
        if ($('#correspondence_out').hasClass('draft')) {
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/OphCoCorrespondence/default/doPrint/' + OE_event_id,
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
            OphCoCorrespondence_do_print(false,true);
            e.preventDefault();
        }
    });

    $(this).on('click', '#et_print_all', function(e) {
        if ($('#correspondence_out').hasClass('draft')) {
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/OphCoCorrespondence/default/doPrint/' + OE_event_id + '?all=1',
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

    $(this).on('click', '#et_export', function(e) {
        e.preventDefault();
        OphCoCorrespondence_do_export();
    });

    $(this).on('click', '#et_confirm_printed', function() {
        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/OphCoCorrespondence/Default/confirmPrinted/' + OE_event_id,
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

    $('button.addEnclosure').die('click').live('click', function() {
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
            '			<input type="text" class="cols-full" value="" autocomplete="' + window.OE_html_complete + '" name="EnclosureItems[enclosure' + id + ']">',
            '			<i class="oe-i trash removeEnclosure"></i>',
            '	</div>'
        ].join('');

        $('#enclosureItems').append(html).show();
        $('input[name="EnclosureItems[enclosure' + id + ']"]').select().focus();
    });

    $('i.removeEnclosure').die('click').live('click', function(e) {
        $(this).closest('.enclosureItem').remove();
        if (!$('#enclosureItems').children().length) {
            $('#enclosureItems').hide();
        }
        e.preventDefault();
    });

    $('div.enclosureItem input').die('keypress').live('keypress', function(e) {
        if (e.keyCode == 13) {
            $('button.addEnclosure').click();
            return false;
        }
        return true;
    });

    var selected_recipient = $('#address_target').val();

    if ($('#dm_table').length > 0) {
        // we have docman table here
        docman2 = docman;
        docman2.baseUrl = location.protocol + '//' + location.host + '/docman/'; // TODO add this to the config!
        docman2.setDOMid('docman_block', 'dm_');
        docman2.module_correspondence = 1;

        docman2.init();
    }

    $('#ElementLetter_letter_type_id').on('change', function() {
        if ($(this).find('option:selected').text() == 'Internal Referral') {
            $('.internal-referrer-wrapper').slideDown();
            setRecipientToInternalReferral();

            if (typeof docman !== "undefined" && !$('#macro_id').val()) {

                //add GP to recipients
                if (!docman.isContactTypeAdded("GP")) {
                    docman.createNewRecipientEntry('GP');
                }

                //add Patient to recipients
                if (!docman.isContactTypeAdded("PATIENT")) {
                    docman.createNewRecipientEntry('PATIENT');
                }
            }

            //call the setDeliveryMethods with row index 0 as the Internal referral will be the main recipient
            //we have to trigger to set it
            docman.setDeliveryMethods(0);
        } else if ($('.internal-referrer-wrapper').is(':visible')) {
            $('.internal-referrer-wrapper').slideUp();
            resetInternalReferralFields();
            resetRecipientFromInternalReferral();

            if (typeof docman !== "undefined") {

                if (typeof docman.setDeliveryMethods === 'function') {
                    docman.setDeliveryMethods(0);
                }
            }
        }
    });

    $('#attachments_content_container').on('click', 'i.trash', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
    });
});

function updateLineAndFax(site_element){
    let direct_line_element = document.getElementById('ElementLetter_direct_line');
    if (correspondence_directlines[site_element.value]) {
        direct_line_element.value = correspondence_directlines[site_element.value];
    } else {
        direct_line_element.value = '';
    }

    let fax_numbers_element = document.getElementById('ElementLetter_fax');
    if (correspondence_directlines[site_element.value]) {
        fax_numbers_element.value = correspondence_fax_numbers[site_element.value];
    } else {
        fax_numbers_element.value = '';
    }
}

function savePDFprint(module, event_id, $content, $data_id, title) {
    if (typeof title == 'undefined') {
        title = '';
    }

    disableButtons();
    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/' + module + '/Default/savePDFprint/' + event_id + '?ajax=1&auto_print=0&pdf_documents=1&attachment_print_title=' + title,
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-Requested-With', 'pdfprint');
        },
        'success': function(response) {
            if (response.success == 1) {
                $hidden = '<input type="hidden" name="file_id[' + $data_id + ']" value="' + response.file_id + '" />';
                $content.prepend($hidden);
            }
        },
        'complete': function() {
            enableButtons();
        }
    });
}

function updateReData(recipient, is_Cc) {
    let default_re = document.getElementById("default_re").value;
    let element_letter_re = document.getElementById('ElementLetter_re');
    if (recipient.match(/^Patient/) && !is_Cc) {
        element_letter_re.value = default_re.substring(default_re.indexOf('DOB'));
    } else {
        element_letter_re.value = default_re;
    }

    autosize(element_letter_re);
}

function correspondence_load_data(data) {
    for (var i in data) {
        if (m = i.match(/^text_(.*)$/)) {
            if (m[1] == 'ElementLetter_body') {
                element_letter_controller.setContent(data[i]);
            } else {
                $('#' + m[1]).val(data[i]);
            }
        } else if (m = i.match(/^sel_(.*)$/)) {
            if (m[1] == 'address_target') {
                updateReData(data[i], false);
            }
            $('#' + m[1]).val(data[i]);
        } else if (m = i.match(/^check_(.*)$/)) {
            $('input[id="' + m[1] + '"][type="checkbox"]').attr('checked', (parseInt(data[i]) == 1 ? true : false));
        } else if (m = i.match(/^textappend_(.*)$/)) {
            $('#' + m[1]).val($('#' + m[1]).val() + data[i]);
        } else if (m = i.match(/^hidden_(.*)$/)) {
            $('#' + m[1]).val(data[i]);
        } else if (m = i.match(/^elementappend_(.*)$/)) {
            $('#' + m[1]).append(data[i]);
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

function OphCoCorrespondence_do_print(all,only_print_the_to) {
    var data = {};

    data['all'] = all || false;
    data['only_print_the_to'] = only_print_the_to || false;

    if ($('#OphCoCorrespondence_print_checked').length && $('#OphCoCorrespondence_print_checked').val() == 1) {
        data['OphCoCorrespondence_print_checked'] = 1;

        // remove OphCoCorrespondence_print_checked, so Print and Print all will do what it says
        $('#OphCoCorrespondence_print_checked').remove();

    }

    $.ajax({
        'type': 'GET',
        'url': correspondence_markprinted_url,
        'success': function(html) {
            printEvent(data);
            enableButtons();
        }
    });
}

function OphCoCorrespondence_do_export() {
    $.ajax({
        'type': 'POST',
        'url': '/OphCoCorrespondence/default/export/' + OE_event_id,
        'data': {
            'YII_CSRF_TOKEN': YII_CSRF_TOKEN,
        },
        'success': function (response) {
            if (response.Success) {
                new OpenEyes.UI.Dialog.Alert({
                    content: 'Export to external document store completed successfully.',
                    closeCallback: enableButtons
                }).open();
            } else {
                new OpenEyes.UI.Dialog.Alert({
                    content: 'Export unable to be performed: Unknown error occurred.',
                    closeCallback: enableButtons
                }).open();
            }
        },
        'error': function() {
            new OpenEyes.UI.Dialog.Alert({
                content: 'An unknown error occurred.',
                closeCallback: enableButtons
            }).open();
        }
    });
}

/**
 * This function checks whether the email is present for the contact.
 * @param rowIndex
 * @param contactType
 * @param element
 */
function isEmailPresent(rowIndex, contactType, element) {
    const contactId = $(`#DocumentTarget_${rowIndex}_attributes_contact_id`).val();

    if ($(element).is(':checked')) {

        if ((contactType === 'GP' || contactType === 'PATIENT' || contactType === 'DRSS' || contactType === 'OPTOMETRIST') && contactId !== "") {
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/OphCoCorrespondence/Default/getContactEmailAddress',
                data: {
                    contact_id: contactId,
                    contact_type: contactType,
                },
                beforeSend: function() {
                    $(element).prop('disabled', true);
                    $(element).prop('disabled', true);
                },
                success: function(data) {
                    if (data !== "") {
                        $(`#DocumentTarget_${rowIndex}_attributes_email`).val(data);
                        $(`#DocumentTarget_${rowIndex}_attributes_email`).prop("readonly", true);
                        $(`#DocumentTarget_${rowIndex}_attributes_email`).show();
                    } else {
                        // show the email address text box if email does not exist, only even if param is set to on
                        if (manually_add_emails_correspondence === "on") {
                            $(`#DocumentTarget_${rowIndex}_attributes_email`).prop("readonly", false);
                            $(`#DocumentTarget_${rowIndex}_attributes_email`).show();
                        } else {
                            const spanElem = "<span id=email_not_found_" + rowIndex + ">No Email Address Stored</span>";
                            if (!$('#email_not_found_' + rowIndex).length) {
                                $(element).parents().eq(1).append(spanElem);
                            }
                            $(element).prop('checked', false);
                        }
                    }
                },
                complete: function() {
                    $(element).prop('disabled', false);
                    $(element).prop('disabled', false);
                },
            });
        }

        if (contactType === 'OTHER') {
            $(`#DocumentTarget_${rowIndex}_attributes_email`).prop("readonly", false);
            $(`#DocumentTarget_${rowIndex}_attributes_email`).show();
        }
    } else {
        //if both the email and email (delayed) checkboxes are un-checked then reset the email field.
        const tr = $('tr[data-rowindex="' + rowIndex + '"]');
        const emailInput = tr.find('input[value="Email"]').is(':checked');
        const emailDelayedInput = tr.find('input[value="Email (Delayed)"]').is(':checked');
        if (!emailInput && !emailDelayedInput) {
            resetEmailField(rowIndex);
        }
    }
}

function resetEmailField(rowIndex) {
    $(`#DocumentTarget_${rowIndex}_attributes_email`).val('');
    $(`#DocumentTarget_${rowIndex}_attributes_email`).prop("readonly", false);
    $(`#DocumentTarget_${rowIndex}_attributes_email`).hide();
}
