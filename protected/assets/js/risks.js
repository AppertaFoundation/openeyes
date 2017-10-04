/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$(document).ready(function () {
    $('#no_risks').bind('change', function () {
        if ($(this)[0].checked) {
            $('.risk_field').hide().find('select').attr('disabled', 'disabled');
        }
        else {
            $('.risk_field').show().find('select').removeAttr('disabled');
        }
    });

    $('#risk_id').change(function () {
        if ($(this).find(':selected').text() == 'Other') {
            $('#risk_other').slideDown('fast');
        } else {
            $('#risk_other').slideUp('fast');
        }
    });

    $('#btn-add_risk').click(function () {
        $('#add_risk').slideToggle('fast');
        $('#btn-add_risk').attr('disabled', true);
        $('#btn-add_risk').addClass('disabled');
    });
    $('button.btn_cancel_risk').click(function (e) {
        $('#add_risk').slideToggle('fast');
        $('#btn-add_risk').attr('disabled', false);
        $('#btn-add_risk').removeClass('disabled');
        OpenEyes.Form.reset($(e.target).closest('form'));
        return false;
    });
    $('button.btn_save_risk').click(function () {
        if ($('#risk_id').val() == '' && !$('#no_risks')[0].checked) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select a risk or confirm patient has no risks"
            }).open();
            return false;
        }
        if ($('#risk_id :selected').text() == 'Other' && $('#risk_other input').val().trim() == '') {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please enter a risk"
            }).open();
            return false;
        }
        $('img.add_risk_loader').show();
        return true;
    });


    $('.removeRisk').live('click', function () {
        $('#remove_risk_id').val($(this).attr('rel'));

        $('#confirm_remove_risk_dialog').dialog({
            resizable: false,
            modal: true,
            width: 560
        });

        return false;
    });

    $('button.btn_remove_risk').click(function () {
        $("#confirm_remove_risk_dialog").dialog("close");

        var aa_id = $('#remove_risk_id').val();

        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/patient/removeRisk?patient_id=' + OE_patient_id + '&assignment_id=' + aa_id,
            'success': function (html) {
                if (html == 'success') {
                    var row = $('#currentRisks tr[data-assignment-id="' + aa_id + '"]');
                    var risk_id = row.data('risk-id');
                    var risk_name = row.data('risk-name');
                    row.remove();
                    if ($('.removeRisk').length == 0) {
                        $('#currentRisks').hide();
                        $('.risk-status-unknown').show();
                        $('.risks_confirm_no').show();
                    }
                    if (risk_name != "Other") {
                        $('#risk_id').append('<option value="' + risk_id + '">' + risk_name + '</option>');
                        sort_selectbox($('#risk_id'));
                    }
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Sorry, an internal error occurred and we were unable to remove the risk.\n\nPlease contact support for assistance."
                    }).open();
                }
            },
            'error': function () {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to remove the risk.\n\nPlease contact support for assistance."
                }).open();
            }
        });

        return false;
    });

    $('button.btn_cancel_remove_risk').click(function () {
        $("#confirm_remove_risk_dialog").dialog("close");
        return false;
    });
});
