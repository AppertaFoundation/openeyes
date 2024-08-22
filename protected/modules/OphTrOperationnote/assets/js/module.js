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

$(document).ready(function () {
    window.event_has_errors = false;
    const $event_content = document.getElementById('event-content');
    if ($event_content) {
        window.event_has_errors = $event_content.dataset.hasErrors === 'true' ? true : false;
    }

    highlightBiometryElement();
    if (window.location.href.indexOf("update") == -1) {
        loadBiometryElementData();
    }

    $(document).on('eyedrawAfterReset', loadBiometryElementData);
    autosize($('textarea'));

    $('#js-display-whiteboard, #js-display-whiteboard_footer').click(function(e) {
        e.preventDefault();
        oeWindow = window.open('/OphTrOperationbooking/whiteboard/view/' + $(this).data('id'), 'whiteboard', 'fullscreen=yes');
    });

    $('#js-close-whiteboard, #js-close-whiteboard_footer').click(function(e) {
        e.preventDefault();
        let oeWindow = window.open('', 'whiteboard', 'fullscreen=yes');
        oeWindow.close();
    });
})

function applyPrefillClassesTo(container)
{
    $(container).find('input, textarea, select').each(function() {
        let prefilled_value = String($(this).data('prefilled-value'));
        if (prefilled_value
            && (
                (prefilled_value === 'true' && $(this).prop('type') === 'checkbox' && $(this).is(':checked'))
                || prefilled_value === $(this).val() && ($(this).prop('type') !== 'radio' || $(this).is(':checked'))
            )
        ) {
            $(this).addClass('prefilled');
        }
    });
}

async function callbackAddProcedure(procedure_id) {
    var eye = $('input[name="Element_OphTrOperationnote_ProcedureList\\[eye_id\\]"]:checked').val();
    let surgeon_id = $('#Element_OphTrOperationnote_Surgeon_surgeon_id').val();

    const event_id = $('.js-procedures-event-id').val();
    const event_param = event_id ? `&event_id=${event_id}` : '';

    const template_id = (new URL(window.location)).searchParams.get('template_id');
    const template_param = template_id ? `&template_id=${template_id}` : '';

    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphTrOperationnote/Default/loadElementByProcedure?procedure_id=' + procedure_id + '&eye=' + eye +'&patient_id=' + OE_patient_id + "&surgeon_id=" + surgeon_id + event_param + template_param,
        'success': function (html) {
            if (html.length > 0) {
                if (html.match(/must-select-eye/)) {
                    var $procedureItem = $('.procedureItem');
                    $procedureItem.map(function (e) {
                        var r = new RegExp('<input type="hidden" value="' + procedure_id + '" name="Procedures');
                        if ($(this).html().match(r)) {
                            $(this).remove();
                        }
                    });
                    if ($procedureItem.length === 0) {
                        $('#procedureList').hide();
                    }
                    new OpenEyes.UI.Dialog.Alert({
                        content: "You must select either the right or the left eye to add this procedure."
                    }).open();
                } else {
                    var m = html.match(/data-element-type-class="(Element.*?)"/);
                    if (m) {
                        m[1] = m[1].replace(/ .*$/, '');

                        if (m[1] === 'Element_OphTrOperationnote_GenericProcedure' || $('.' + m[1]).length < 1) {
                            const new_element = $(html);
                            new_element.insertBefore($('.Element_OphTrOperationnote_ProcedureList').nextAll('.element.required').first());
                            applyPrefillClassesTo(new_element);

                            var $lastMatchedElement = $('section.' + m[1] + ':last');
                            var $customHintBlock = $('.alert-box.' + m[1] + ':last');
                            $lastMatchedElement.attr('style', 'display: none;');
                            $lastMatchedElement.removeClass('hidden');
                            $customHintBlock.attr('style', 'display: none;');
                            $customHintBlock.removeClass('hidden');
                            $lastMatchedElement.slideToggle('fast');
                            $customHintBlock.slideToggle('fast');
                        }
                    }
                }
                highlightBiometryElement();
                if (window.location.href.indexOf("update") == -1) {
                    loadBiometryElementData();
                }
            }
            autosize($('textarea'));
        }
    });
}

/*
 * Post the removed operation_id and an array of ElementType class names currently in the DOM
 * This should return any ElementType classes that we should remove.
 */
function callbackRemoveProcedure(procedure_id) {
    var procedures = '';

    var hpid = $('input[type="hidden"][name="Element_OphTrOperationnote_GenericProcedure[' + procedure_id + '][proc_id]"][value="' + procedure_id + '"]');

    if (hpid.length > 0) {
        hpid.parent().slideToggle('fast', function () {
            hpid.parent().remove();
        });

        return;
    }

    $('input[name="Procedures_procs[]"]').map(function () {
        if (procedures.length > 0) {
            procedures += ',';
        }
        procedures += $(this).val();
    });

    $.ajax({
        'type': 'POST',
        'url': baseUrl + '/OphTrOperationnote/Default/getElementsToDelete',
        'data': "remaining_procedures=" + procedures + "&procedure_id=" + procedure_id + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
        'dataType': 'json',
        'success': function (data) {
            $.each(data, function (key, val) {
                $('.' + val).slideToggle('fast', function () {
                    $('.' + val).remove();
                });

                if (val === 'Element_OphTrOperationnote_Cataract') {
                    $('#ophTrOperationnotePCRRiskDiv').slideToggle('fast', function () {
                        $(this).remove();
                    })
                }
            });
        }
    });
}

function setCataractSelectInput(key, value) {
    $('#Element_OphTrOperationnote_Cataract_' + key + '_id').children('option').map(function () {
        if ($(this).text() == value) {
            $('#Element_OphTrOperationnote_Cataract_' + key + '_id').val($(this).val());
        }
    });
}

function setCataractInput(key, value) {
    $('#Element_OphTrOperationnote_Cataract_' + key).val(value);
}

$(document).ready(function () {
    $(this).on('click', '#et_save', function () {
        if ($('#Element_OphTrOperationnote_Buckle_report').length > 0) {
            $('#Element_OphTrOperationnote_Buckle_report').val(ED.getInstance('ed_drawing_edit_Buckle').report());
        }
        if ($('#Element_OphTrOperationnote_Cataract_report2').length > 0) {
            $('#Element_OphTrOperationnote_Cataract_report2').val(ED.getInstance('ed_drawing_edit_Cataract').report());
        }
    });

    $(this).on('click', '#et_cancel', function (e) {
        if (m = window.location.href.match(/\/update\/[0-9]+/)) {
            window.location.href = window.location.href.replace('/update/', '/view/');
        } else {
            window.location.href = baseUrl + '/patient/summary/' + OE_patient_id;
        }
        e.preventDefault();
    });

    applyPrefillClassesTo(this);

    $(this).on('click', '#et_print', function (e) {
        e.preventDefault();
        printEvent(null);
    });

    $('body').on('keyup', 'input, textarea', function() {
        if ($(this).hasClass('prefilled') && $(this).val() != $(this).data('prefilled-value')) {
            $(this).removeClass('prefilled');
        }
    });

    $('body').on('change', 'input[type="checkbox"]', function() {
        // Remove 'prefilled' class from only the selected checkbox.
        if ($(this).hasClass('prefilled') && !$(this).prop('checked') && $(this).data('prefilled-value') === 'true') {
            $(this).removeClass('prefilled');
        }
    });

    $('body').on('change', 'input[type="radio"]', function() {
        // Remove 'prefilled' class from all radio buttons that form part of the same group.
        $('input[type="radio"][name="' + $(this).prop('name') + '"]').each(function () {
            if ($(this).hasClass('prefilled')) {
                $(this).removeClass('prefilled');
            }
        });
    });

    $('body').on('change', 'select', function() {
        if ($(this).hasClass('prefilled') && $(this).val() != $(this).data('prefilled-value')) {
            $(this).removeClass('prefilled');
        }
    });

    var last_Element_OphTrOperationnote_ProcedureList_eye_id = null;

    $('[data-element-type-class="Element_OphTrOperationnote_ProcedureList"]').undelegate('input[name="Element_OphTrOperationnote_ProcedureList\[eye_id\]"]', 'change').delegate('input[name="Element_OphTrOperationnote_ProcedureList\[eye_id\]"]', 'change', function () {
        var element = $(this);

        if ($(this).val() == 3) {
            var i = 0;
            var procs = '';
            $('input[name="Procedures[]"]').map(function () {
                if (procs.length > 0) {
                    procs += '&';
                }
                procs += 'proc' + i + '=' + $(this).val();
                i += 1;
            });

            if (procs.length > 0) {
                $.ajax({
                    'type': 'GET',
                    'url': baseUrl + '/OphTrOperationnote/default/verifyprocedure',
                    'data': procs,
                    'success': function (result) {
                        if (result != 'yes') {
                            $('#Element_OphTrOperationnote_ProcedureList_eye_id_' + last_Element_OphTrOperationnote_ProcedureList_eye_id).attr('checked', 'checked');
                            if (parseInt(result.split("\n").length) == 1) {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "The following procedure requires a specific eye selection and cannot be entered for both eyes at once:\n\n" + result
                                }).open();
                            } else {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "The following procedures require a specific eye selection and cannot be entered for both eyes at once:\n\n" + result
                                }).open();
                            }
                            return false;
                        } else {
                            if ($('#typeProcedure').is(':hidden')) {
                                $('#typeProcedure').slideToggle('fast');
                            }

                            changeEye();
                            last_Element_OphTrOperationnote_ProcedureList_eye_id = element.val();

                            return true;
                        }
                    }
                });
            } else {
                if ($('#typeProcedure').is(':hidden')) {
                    $('#typeProcedure').slideToggle('fast');
                }

                changeEye();

                last_Element_OphTrOperationnote_ProcedureList_eye_id = $(this).val();

                return true;
            }

            return false;
        } else {
            if ($('#typeProcedure').is(':hidden')) {
                $('#typeProcedure').slideToggle('fast');
            }

            changeEye();
            last_Element_OphTrOperationnote_ProcedureList_eye_id = $(this).val();

            return true;
        }
    });

    $('[data-element-type-class="Element_OphTrOperationnote_Anaesthetic"]').undelegate('input[name="Element_OphTrOperationnote_Anaesthetic\[anaesthetic_type_id\]"]', 'click').delegate('input[name="Element_OphTrOperationnote_Anaesthetic\[anaesthetic_type_id\]"]', 'click', function (e) {
        anaestheticSlide.handleEvent($(this));
    });

    $('[data-element-type-class="Element_OphTrOperationnote_Cataract"]').undelegate('input[name="Element_OphTrOperationnote_Anaesthetic\[anaesthetist_id\]"]', 'click').delegate('input[name="Element_OphTrOperationnote_Anaesthetic\[anaesthetist_id\]"]', 'click', function (e) {
        anaestheticGivenBySlide.handleEvent($(this));
    });

    $('#Element_OphTrOperationnote_Cataract_iol_type_id').die('change').live('change', function () {
        if ($(this).children('optgroup').children('option:selected').text() == 'MTA3UO' || $(this).children('option:selected').text() == 'MTA4UO') {
            $('#Element_OphTrOperationnote_Cataract_iol_position_id').val(4);
        }
    });

    $('#Element_OphTrOperationnote_Cataract_iol_power').die('keypress').live('keypress', function (e) {
        if (e.keyCode == 13) {
            return false;
        }
        return true;
    });

    $('tr.clickable').disableSelection();

    $('tr.clickable').click(function () {
        $(this).children('td:first').children('input[type="radio"]').attr('checked', true);
    });

    $(this).delegate('.ed_clear', 'click', function (e) {
        e.preventDefault();

        var element = $(this).closest('.element');

        var description = 'description';
        var report = 'report';

        var textarea = element.find([
            'textarea[name$="[' + description + ']"]',
            'textarea[name$="[' + report + ']"]'
        ].join(',')).first();

        textarea.val('');
        textarea.trigger('autosize');
    });

    $(this).delegate('#btn-glaucomatube-report', 'click', function (e) {
        e.preventDefault();
        var element = $(this).closest('.element');
        var drawing_name = $('#Element_OphTrOperationnote_GlaucomaTube_eyedraw').prev('canvas').data('drawing-name');
        reportEyedraw(element, ED.getInstance(drawing_name), 'description');
    });

    $(this).delegate('#btn-trabeculectomy-report', 'click', function (e) {
        e.preventDefault();
        var element = $(this).closest('.element');
        var drawing_name = $('#Element_OphTrOperationnote_Trabeculectomy_eyedraw').prev('canvas').data('drawing-name');
        reportEyedraw(element, ED.getInstance(drawing_name), 'report');
    });

    $(this).delegate('#btn-trabectome-report', 'click', function (e) {
        e.preventDefault();
        var element = $(this).closest('.element');
        var drawing_name = $('#Element_OphTrOperationnote_Trabectome_eyedraw').prev('canvas').data('drawing-name');
        reportEyedraw(element, ED.getInstance(drawing_name), 'description');
    });

    let $op_note_surgeon = $('#Element_OphTrOperationnote_Surgeon_surgeon_id');

    $op_note_surgeon.on('input', function () {
        let selected_surgeon_id = $(this).val();
        let surgeon_drawing = window.ED ? ED.getInstance('ed_drawing_edit_Position') : undefined;
        let cataract_drawing = window.ED ? ED.getInstance('ed_drawing_edit_Cataract') : undefined;

        $.ajax({
            type: 'GET',
            url: baseUrl + '/OphTrOperationnote/Default/getUserSettingsValues/',
            dataType: "json",
            data: {
                surgeon_id: selected_surgeon_id
            },
            success: function (settings) {
                if ((typeof cataract_drawing !== 'undefined') && settings.length != 0) {

                    let eye_side = cataract_drawing.eye === 0 ? 'right' : 'left';
                    let surgeon_doodle = surgeon_drawing.firstDoodleOfClass('Surgeon');
                    let phako_incision_doodles = cataract_drawing.allDoodlesOfClass('PhakoIncision');

                    cataract_drawing.deleteDoodlesOfClass('SidePort');
                    cataract_drawing.addDoodle('SidePort', {rotation: 0});
                    if (+settings['number_of_ports'] === 2) {
                        cataract_drawing.addDoodle('SidePort', {rotation: Math.PI});
                    }

                    surgeon_doodle.setParameterWithAnimation('surgeonPosition', settings['surgeon_position_' + eye_side + '_eye']);

                    setTimeout(() => {
                        phako_incision_doodles[0].setParameterFromString('incisionMeridian', settings['incision_centre_position_' + eye_side + '_eye'], true);
                    }, 500);

                    setTimeout(() => {
                        phako_incision_doodles[0].setParameterFromString('incisionMeridian', settings['incision_centre_position_' + eye_side + '_eye'], true);

                        const side_ports = cataract_drawing.allDoodlesOfClass('SidePort');
                        if (typeof (side_ports[0]) !== 'undefined') {
                            side_ports[0].setSimpleParameter('rotation', (phako_incision_doodles[0].rotation + Math.PI / 2) % (2 * Math.PI));
                        }

                        if (typeof (side_ports[1]) !== 'undefined') {
                            side_ports[1].setSimpleParameter('rotation', (phako_incision_doodles[0].rotation - Math.PI / 2) % (2 * Math.PI));
                        }

                        cataract_drawing.deselectDoodles();

                    }, 700);


                }
            }
        });
    });
});

var OphTrOperationnote_reports = {};

function reportEyedraw(element, eyedraw, fieldName) {
    var text = eyedraw.report();
    text = text.replace(/, +$/, '');

    var field = $('textarea[name$="[' + fieldName + ']"]', element).first();

    if (field.val() == '') {
        OphTrOperationnote_reports[element.data('element-type-id')] = text;
        field.val(text);
    } else {
        if (typeof (OphTrOperationnote_reports[element.data('element-type-id')]) != 'undefined' &&
            field.val().indexOf(OphTrOperationnote_reports[element.data('element-type-id')]) != -1) {
            field.val(field.val().replace(new RegExp(OphTrOperationnote_reports[element.data('element-type-id')]), text));
            OphTrOperationnote_reports[element.data('element-type-id')] = text;
        } else {
            field.val(text);
        }
    }

    field.trigger('autosize');
}


function callbackVerifyAddProcedure(proc_name, durations, callback) {
    var eye = $('input[name="Element_OphTrOperationnote_ProcedureList\[eye_id\]"]:checked').val();

    if (eye != 3) {
        callback(true);
        return;
    }

    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphTrOperationnote/Default/verifyprocedure?name=' + proc_name + '&durations=' + durations,
        'success': function (result) {
            if (result == 'yes') {
                callback(true);
            } else {
                new OpenEyes.UI.Dialog.Alert({
                    content: "You must select either the right or the left eye before adding this procedure."
                }).open();
                callback(false);
            }
        }
    });
}

function AnaestheticSlide() {
    if (this.init) this.init.apply(this, arguments);
}

AnaestheticSlide.prototype = {
    init: function (params) {
        this.anaestheticTypeSliding = false;
    },
    handleEvent: function (e) {
        var slide = false;

        /* if (!this.anaestheticTypeSliding) {
             if (e.val() == 5 && !$('#Element_OphTrOperationnote_Anaesthetic_anaesthetist_id').is(':hidden')) {
                 this.slide(true);
             } else if (e.val() != 5 && $('#Element_OphTrOperationnote_Anaesthetic_anaesthetist_id').is(':hidden')) {
                 this.slide(false);
             }
         }*/

        // If topical anaesthetic type is selected, select topical delivery
        if (e.val() == 1) {
            $('#Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_5').click();
        }
    },
    slide: function (hide) {
        this.anaestheticTypeSliding = true;
        /*$('#Element_OphTrOperationnote_Anaesthetic_anaesthetist_id').slideToggle('fast');*/
        if (hide) {
            if (!$('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id').is(':hidden')) {
                $('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id').slideToggle('fast');
            }
        } else {
            if ($('#Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_3').is(':checked') && $('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id').is(':hidden')) {
                $('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id').slideToggle('fast');
            }
        }
    }
};

function AnaestheticGivenBySlide() {
    if (this.init) this.init.apply(this, arguments);
}

AnaestheticGivenBySlide.prototype = {
    init: function (params) {
        this.anaestheticTypeWitnessSliding = false;
    },
    handleEvent: function (e) {
        var slide = false;

        // if Fife mode is enabled
        if ($('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id')) {
            // If nurse is selected, show the witness field
            if (!this.anaestheticTypeWitnessSliding) {
                if ((e.val() == 3 && $('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id').is(':hidden')) ||
                    (e.val() != 3 && !$('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id').is(':hidden'))) {
                    this.slide();
                }
            }
        }
    },
    slide: function () {
        this.anaestheticTypeWitnessSliding = true;
        $('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_witness_id').slideToggle('fast', function () {
            anaestheticGivenBySlide.anaestheticTypeWitnessSliding = false;
        });
    }
};

var anaestheticSlide = new AnaestheticSlide;
var anaestheticGivenBySlide = new AnaestheticGivenBySlide;
var iol_position;

/**
 * simply checks if any IOL related doodles are in the eyedraw, and hides or shows the related fields
 * accordingly
 *
 * @param _drawing
 */
function showHideIOLFields(_drawing, resetPosition) {
    var iolPresent = false;

    for (var i in _drawing.doodleArray) {
        if ($.inArray(_drawing.doodleArray[i].className, eyedraw_iol_classes) > -1) {
            iolPresent = true;
            break;
        }
    }
    if (iolPresent) {
        $('#div_Element_OphTrOperationnote_Cataract_iol_type_id').show();
        $('#div_Element_OphTrOperationnote_Cataract_iol_power').show();
        $('#div_Element_OphTrOperationnote_Cataract_iol_position_id').show();
        if (resetPosition && $('#Element_OphTrOperationnote_Cataract_iol_position_id').children('option:selected').text() == 'None') {
            $('#Element_OphTrOperationnote_Cataract_iol_position_id').children('option').map(function () {
                if ($(this).text() == 'Select') {
                    $(this).attr('selected', true);
                } else {
                    $(this).attr('selected', false);
                    if ($(this).text() == 'None') {
                        $(this).attr('disabled', 'disabled');
                    }
                }
            });
        }
    } else {
        $('#div_Element_OphTrOperationnote_Cataract_iol_type_id').hide();
        $('#div_Element_OphTrOperationnote_Cataract_iol_power').hide();
        $('#div_Element_OphTrOperationnote_Cataract_iol_position_id').hide();

        $('#Element_OphTrOperationnote_Cataract_iol_position_id').children('option').map(function () {
            if ($(this).text() == 'None') {
                $(this).removeAttr('disabled');
                $(this).attr('selected', true);
            } else {
                $(this).attr('selected', false);
            }
        });


    }

    let posteriorChamberIOLPresent = false;
    let anteriorChamberIOLPresent = false;
    const eyedrawPosteriorChamberIOLClass = 'PCIOL';
    const eyedrawAnteriorChamberIOLClass = 'ACIOL';

    for (let doodleArrayIndex in _drawing.doodleArray) {
        if (_drawing.doodleArray[doodleArrayIndex].className === eyedrawPosteriorChamberIOLClass) {
            posteriorChamberIOLPresent = true;
        }
        if (_drawing.doodleArray[doodleArrayIndex].className === eyedrawAnteriorChamberIOLClass) {
            anteriorChamberIOLPresent = true;
        }
    }

    if (posteriorChamberIOLPresent || anteriorChamberIOLPresent) {
        $('#tr_Element_OphTrOperationnote_Cataract_iol_type').show();
        if ($("#Element_OphTrOperationnote_Cataract_iol_type_id option:selected").text() === '-') {
            $('#Element_OphTrOperationnote_Cataract_iol_type_id').val('');
        }
    } else {
        $('#tr_Element_OphTrOperationnote_Cataract_iol_type').hide();
        $("#Element_OphTrOperationnote_Cataract_iol_type_id option").each(function () {
            if ($(this).text() === '-') {
                $(this).attr('selected', 'selected');
            }
        });
    }
}

function AngleMarksController(_drawing) {
    var angleMarks,
        $biometry_element = $(".Element_OphTrOperationnote_Biometry"),
        has_biometry = $biometry_element.find(".element-fields").data('has-biometry'),
        data;

    // Register controller for notifications
    _drawing.registerForNotifications(this, 'notificationHandler', ['ready', 'afterReset']);

    // Method called for notification
    this.notificationHandler = function (_messageArray) {

        switch (_messageArray['eventName']) {
            case 'afterReset':
            case 'ready':
                this.initAntSegAngleMarks();
                break;
        }
    };

    this.initAntSegAngleMarks = function () {

        data = $(".Element_OphTrOperationnote_Biometry").find('.' + (_drawing.eye === 0 ? 'right' : 'left') + '-eye .element-data').data("biometry-data");
        angleMarks = _drawing.firstDoodleOfClass('AntSegAngleMarks');

        if (!has_biometry && angleMarks) {
            _drawing.deleteDoodle(angleMarks, true);
        } else if (has_biometry && !data && angleMarks) {
            //Eg. only one side has biometry data but we creating Op note for the other eye
            _drawing.deleteDoodle(angleMarks, true);
        } else if (has_biometry && data) {
            if (!_drawing.hasDoodleOfClass("AntSegAngleMarks")) {
                let biometry_data = this.calculateBiometryData(data);
                _drawing.addDoodle("AntSegAngleMarks", biometry_data);
            } else {
                this.setBiometryData();
            }

        }
    };

    this.calculateBiometryData = function (data) {
        var return_obj = {};
        if (data && data.k1 && data.k2 && data.axis_k1 && data.axis_k2) {
            let steepK = data.k1 > data.k2 ? data.k1 : data.k2;
            let flatK = data.k1 < data.k2 ? data.k1 : data.k2;
            let axis = data.k1 > data.k2 ? data.axis_k1 : data.axis_k2;

            return_obj = {'axis': axis, 'flatK': flatK, 'steepK': steepK};
        }

        return return_obj;
    }
    this.setBiometryData = function () {

        //Refresh data
        data = $(".Element_OphTrOperationnote_Biometry").find('.' + (_drawing.eye === 0 ? 'right' : 'left') + '-eye .element-data').data("biometry-data");
        let biometry_data = this.calculateBiometryData(data);

        if (has_biometry && data && angleMarks) {
            angleMarks.setParameterFromString('axis', biometry_data.axis);
            angleMarks.setParameterFromString('flatK', biometry_data.flatK);
            angleMarks.setParameterFromString('steepK', biometry_data.steepK);
        } else {
            _drawing.deleteDoodle(angleMarks, true);
        }
    };
}

function PCIOLController(_drawing) {

    // Register controller for notifications
    _drawing.registerForNotifications(this, 'notificationHandler', ['ready', 'doodleAdded']);

    // Method called for notification
    this.notificationHandler = function (_messageArray) {
        let $iol_position;
        switch (_messageArray['eventName']) {
            // Ready notification
            case 'doodleAdded':
                $iol_position = $('#Element_OphTrOperationnote_Cataract_iol_position_id');

                $('#eyedrawwidget_Cataract').on('change', '#ed_canvas_edit_Cataract_fixation_control', function () {
                    let text = $(this).find('option:selected').text();
                    switch (text) {
                        case 'In-the-bag':
                            $iol_position.val(1);
                            break;
                        case 'Partly in the bag':
                            $iol_position.val(2);
                            break;
                        case 'Ciliary sulcus':
                            $iol_position.val(3);
                            break;
                    }
                });

                $iol_position.on('change', function () {

                    if (_drawing.hasDoodleOfClass('PCIOL')) {
                        let value = $(this).val();
                        let fixation_value;
                        var PCIOL = _drawing.allDoodlesOfClass('PCIOL')[0];

                        if (value === "1") {
                            fixation_value = 'In-the-bag';
                        }
                        if (value === "2") {
                            fixation_value = 'Partly in the bag';
                        }
                        if (value === "3") {
                            fixation_value = 'Ciliary sulcus';
                        }

                        if (fixation_value) {
                            PCIOL.setSimpleParameter('fixation', fixation_value);
                        }

                    }
                });
                break;
        }
    };
}

function getSurgeonPosition(_value, isRE = true) {
    returnArray = [];
    switch (_value) {
        case 'Superior':
            returnArray['rotation'] = 0;
            break;
        case 'Supero-temporal':
            returnArray['rotation'] = isRE ? 7 * Math.PI / 4 : 1 * Math.PI / 4;
            break;
        case 'Temporal':
            returnArray['rotation'] = isRE ? 6 * Math.PI / 4 : 2 * Math.PI / 4;
            break;
        case 'Infero-temporal':
            returnArray['rotation'] = isRE ? 5 * Math.PI / 4 : 3 * Math.PI / 4;
            break;
        case 'Inferior':
            returnArray['rotation'] = Math.PI;
            break;
        case 'Infero-nasal':
            returnArray['rotation'] = isRE ? 3 * Math.PI / 4 : 5 * Math.PI / 4;
            break;
        case 'Nasal':
            returnArray['rotation'] = isRE ? 2 * Math.PI / 4 : 6 * Math.PI / 4;
            break;
        case 'Supero-nasal':
            returnArray['rotation'] = isRE ? 1 * Math.PI / 4 : 7 * Math.PI / 4;
            break;
    }

    return returnArray;
}
function surgeonController(_drawing) {
    // Register controller for notifications
    _drawing.registerForNotifications(this, 'notificationHandler', ['ready', 'parameterChanged']);

    // Method called for notification
    this.notificationHandler = function (_messageArray) {
        const isRE = _drawing.eye == ED.eye.Right;
        const surgeon = _drawing.firstDoodleOfClass('Surgeon');
        switch (_messageArray['eventName']) {
            case 'ready':
                _drawing.addDoodle('OperatingTable');

                if (!surgeon) {
                    _drawing.addDoodle('Surgeon');
                }

                _drawing.deselectDoodles();
                break;
            case 'parameterChanged':
                const _cataractDrawing = ED.getInstance('ed_drawing_edit_Cataract');
                const doodle = _messageArray.object.doodle;
                if (doodle.className === 'Surgeon' && _messageArray.object.parameter === 'surgeonPosition') {

                    const phakoIncision = _cataractDrawing.firstDoodleOfClass('PhakoIncision');
                    if (phakoIncision && surgeon && _messageArray.object.value !==_messageArray.object.oldValue) {
                        const oldRotation = getSurgeonPosition(_messageArray.object.oldValue, isRE)['rotation']*180/Math.PI;
                        const newRotation = getSurgeonPosition(_messageArray.object.value, isRE)['rotation']*180/Math.PI;

                        let mod = (oldRotation < newRotation ? 1 : -1);
                        if (newRotation === 0 && oldRotation > 300) {
                            mod = 1;
                        }
                        if (newRotation === 0 && oldRotation < 50) {
                            mod = -1;
                        }
                        if (oldRotation === 0 && newRotation > 300) {
                            mod = -1;
                        }
                        phakoIncision.setParameterFromString('rotation', '' + (phakoIncision.rotation + ((45*Math.PI/180)* mod) ));

                        // rotate sidePorts as well
                        const sidePorts = _cataractDrawing.allDoodlesOfClass('SidePort');
                        sidePorts.forEach(sidePort => {
                            sidePort.setParameterFromString('rotation', '' + (sidePort.rotation + ((45*Math.PI/180)* mod) ));
                        });
                    }
                }
                break;
        }
    };
}

function sidePortController(_drawing) {
    let phakoIncision;
    let sidePort1;
    let sidePort2;

    let iol_position;
    let site_id;
    let type_id;
    let length;
    let meridian;
    let meridian_element = 'Element_OphTrOperationnote_Cataract_meridian';
    let eye_id = ED.getInstance('ed_drawing_edit_Cataract').eye;

    // Register controller for notifications
    _drawing.registerForNotifications(this, 'notificationHandler', ['ready', 'beforeReset', 'afterReset', 'resetEdit', 'parameterChanged', 'doodleAdded', 'doodleDeleted', 'doodlesLoaded']);

    this.addSidePorts = function () {
        var has_sideport = _drawing.hasDoodleOfClass('SidePort');
        var doodles = _drawing.allDoodlesOfClass('SidePort');

        sidePort1 = has_sideport ? doodles[0] : _drawing.addDoodle('SidePort', {rotation: 0});

        if(typeof number_of_ports === 'undefined' || number_of_ports === 2){
            sidePort2 = has_sideport ? doodles[1] : _drawing.addDoodle('SidePort', {rotation: Math.PI});
        }
        _drawing.deselectDoodles();
    };

    // Method called for notification
    this.notificationHandler = function (_messageArray) {

        switch (_messageArray['eventName']) {
            // Ready notification
            case 'ready':
                // Get reference to the phakoIncision
                phakoIncision = _drawing.firstDoodleOfClass('PhakoIncision');

                // If this is a newly created drawing, add two sideports
                if ($(_drawing.canvas).parents('.eyedraw-row.cataract').data('isNew')) {
                    this.addSidePorts();

                    if (typeof (phakoIncision) !== 'undefined') {
                        let incision_meridian = eye_id === 0 ? '180' : '0';

                        if (typeof incision_centre_position !== 'undefined' && incision_centre_position[eye_id] !== 'undefined') {
                            incision_meridian = ''+incision_centre_position[eye_id];
                        }

                        if (window.event_has_errors !== true) {
                            setTimeout(() => {
                                phakoIncision.setParameterFromString('incisionMeridian', incision_meridian, true);

                                const side_ports = _drawing.allDoodlesOfClass('SidePort');
                                if (typeof (side_ports[0]) !== 'undefined') {
                                    side_ports[0].setSimpleParameter('rotation', (phakoIncision.rotation + Math.PI / 2) % (2 * Math.PI));
                                }

                                if (typeof (side_ports[1]) !== 'undefined') {
                                    side_ports[1].setSimpleParameter('rotation', (phakoIncision.rotation - Math.PI / 2) % (2 * Math.PI));
                                }

                                _drawing.deselectDoodles();
                            }, 1000);
                        }
                    }
                }

                // this attribute is used by cypress testing to indicate that eyedraw is ready
                // ideally we would actually verify that all the fields have been synced before
                // setting this state.
                $('#eyedrawwidget_Cataract').attr('data-cy-ed-ready', 'true');

                break;
            case 'beforeReset': {
                iol_position = $('#Element_OphTrOperationnote_Cataract_iol_position_id').val();
                let surgeonDrawing = ED.getInstance('ed_drawing_edit_Position');
                if (surgeonDrawing) {
                    surgeonDrawing.resetEyedraw();
                }
            }
                break;
            case 'resetEdit':
                $('#Element_OphTrOperationnote_Cataract_iol_position_id').val(iol_position);
                $('#Element_OphTrOperationnote_Cataract_incision_site_id').val(site_id);
                $('#Element_OphTrOperationnote_Cataract_incision_type_id').val(type_id);
                $('#Element_OphTrOperationnote_Cataract_length').val(length);
                $('#Element_OphTrOperationnote_Cataract_meridian').val(meridian);
                break;
            case 'afterReset':
                // Get reference to the phakoIncision
                phakoIncision = _drawing.firstDoodleOfClass('PhakoIncision');
                if (this.resetDoodleSet === false || // resetDoodleSet === false when the eyedraw is new
                    // but new eyedraws are loaded in the same way as editing, so might still be a new
                    // eyedraw that is being reset.
                    $(_drawing.canvas).parents('.eyedraw-row.cataract').data('isNew')) {
                    this.addSidePorts();
                }
                break;
            // Parameter change notification
            case 'parameterChanged':

                // Get rotation value of surgeon doodle
                var surgeonDrawing = ED.getInstance('ed_drawing_edit_Position');
                var surgeonRotation = surgeonDrawing.firstDoodleOfClass('Surgeon').rotation;

                // Get doodle that has moved in opnote drawing
                var masterDoodle = _messageArray['object'].doodle;
                break;
            case 'doodleDeleted':
                showHideIOLFields(_drawing, true);
                break;
            case 'doodleAdded':
                if (_drawing.isReady)
                    showHideIOLFields(_drawing, true);
                break;
            case 'doodlesLoaded':
                showHideIOLFields(_drawing, false);
                break;
        }
    }
}

function trabeculectomyController(_drawing) {
    _drawing.registerForNotifications(this, 'notificationHandler', ['ready', 'reset', 'resetEdit', 'parameterChanged']);

    var conjunctival_flap_type_id;
    var site_id;
    var size_id;
    var sclerostomy_type_id;

    this.notificationHandler = function (_messageArray) {

        var sutureArray = Array();
        switch (_messageArray['eventName']) {
            case 'ready':
                this.conjFlap = _drawing.firstDoodleOfClass('ConjunctivalFlap');
                this.trabFlap = _drawing.firstDoodleOfClass('TrabyFlap');
                this.gsf = _drawing.globalScaleFactor;
                _drawing.deselectDoodles();

                conjunctival_flap_type_id = $('#Element_OphTrOperationnote_Trabeculectomy_conjunctival_flap_type_id').val();
                site_id = $('#Element_OphTrOperationnote_Trabeculectomy_site_id').val();
                size_id = $('#Element_OphTrOperationnote_Trabeculectomy_size_id').val();
                sclerostomy_type_id = $('#Element_OphTrOperationnote_Trabeculectomy_sclerostomy_type_id').val();
                break;

            case 'reset':
                $('#Element_OphTrOperationnote_Trabeculectomy_conjunctival_flap_type_id').val('');
                $('#Element_OphTrOperationnote_Trabeculectomy_site_id').val('');
                $('#Element_OphTrOperationnote_Trabeculectomy_size_id').val('');
                $('#Element_OphTrOperationnote_Trabeculectomy_sclerostomy_type_id').val('');
                break;

            case 'resetEdit':
                $('#Element_OphTrOperationnote_Trabeculectomy_conjunctival_flap_type_id').val(conjunctival_flap_type_id);
                $('#Element_OphTrOperationnote_Trabeculectomy_site_id').val(site_id);
                $('#Element_OphTrOperationnote_Trabeculectomy_size_id').val(size_id);
                $('#Element_OphTrOperationnote_Trabeculectomy_sclerostomy_type_id').val(sclerostomy_type_id);
                break;

            case 'parameterChanged':
                if (_drawing.isNew) {
                    var doodle = _messageArray['object'].doodle;

                    if (doodle.isSelected && (doodle.className == 'TrabySuture' || doodle.className == 'ConjunctivalFlap')) {
                        if (this.trabFlap) {
                            this.trabFlap.willSync = false;
                        }
                    }

                    if (doodle.className == 'TrabyFlap') {
                        if (_messageArray['object']['parameter'] == 'rotation') {
                            if (doodle.willSync) {
                                var sutures = _drawing.allDoodlesOfClass('TrabySuture');

                                for (var i = 0; i < sutures.length; i++) {
                                    var np = new ED.Point(sutures[i].originX, sutures[i].originY);

                                    var delta = _messageArray['object'].value - _messageArray['object'].oldValue;

                                    np.setWithPolars(np.length(), np.direction() + delta);

                                    sutures[i].originX = np.x;
                                    sutures[i].originY = np.y;

                                    sutures[i].rotation += delta;
                                }

                                if (this.conjFlap) {
                                    this.conjFlap.rotation = doodle.rotation;
                                }
                            }
                        }
                    }
                }
                break;
        }
    }.bind(this)
}

function changeEye() {
    // Swap side of each drawing
    var drawingEdit1 = window.ED ? ED.getInstance('ed_drawing_edit_Position') : undefined;
    var drawingEdit2 = window.ED ? ED.getInstance('ed_drawing_edit_Cataract') : undefined;
    var drawingEdit3 = window.ED ? ED.getInstance('ed_drawing_edit_Trabeculectomy') : undefined;
    var drawingEdit4 = window.ED ? ED.getInstance('ed_drawing_edit_Vitrectomy') : undefined;

    if (typeof (drawingEdit1) != 'undefined') {
        if (drawingEdit1.eye == ED.eye.Right) drawingEdit1.eye = ED.eye.Left;
        else drawingEdit1.eye = ED.eye.Right;

        // Set surgeon position to temporal side
        var doodle = drawingEdit1.firstDoodleOfClass('Surgeon');
        doodle.setParameterWithAnimation('surgeonPosition', (typeof surgeon_position !== 'undefined' && surgeon_position !== false ? surgeon_position[drawingEdit1.eye] : 'Temporal'));
    }

    if (typeof (drawingEdit2) != 'undefined') {
        alert('The eye state loaded for the cataract operation may no longer be correct. Please remove and re-add the procedure.');
        if (drawingEdit2.eye == ED.eye.Right) drawingEdit2.eye = ED.eye.Left;
        else drawingEdit2.eye = ED.eye.Right;

        for (let i in drawingEdit2.notificationArray) {
            let obj = drawingEdit2.notificationArray[i].object;
            if (obj.constructor.name === 'AngleMarksController') {
                obj.initAntSegAngleMarks();
            }
        }
    }

    if (typeof (drawingEdit3) != 'undefined') {
        if (drawingEdit3.eye == ED.eye.Right) drawingEdit3.eye = ED.eye.Left;
        else drawingEdit3.eye = ED.eye.Right;

        rotateTrabeculectomy();
    }

    if (typeof (drawingEdit4) != 'undefined') {
        if (drawingEdit4.eye == ED.eye.Right) drawingEdit4.eye = ED.eye.Left;
        else drawingEdit4.eye = ED.eye.Right;

        rotateVitrectomy();
    }

    if (typeof pcrCalculate === 'function') {
        if ($("#Element_OphTrOperationnote_ProcedureList_eye_id_2").attr('checked') == "checked") {
            pcrCalculate($('#ophCiExaminationPCRRiskRightEye'), 'right');
        }

        if ($("#Element_OphTrOperationnote_ProcedureList_eye_id_1").attr('checked') == "checked") {
            pcrCalculate($('#ophCiExaminationPCRRiskLeftEye'), 'left');
        }
    }

    // we remove the current values when the user change the eye
    $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val('');
    $('#Element_OphTrOperationnote_Cataract_iol_power').val('');
    highlightBiometryElement();
    if (window.location.href.indexOf("update") == -1) {
        loadBiometryElementData();
    }
}

function rotateTrabeculectomy() {
    var _drawing = ED.getInstance('ed_drawing_edit_Trabeculectomy');

    if (_drawing.isNew) {
        var sidePort = _drawing.firstDoodleOfClass('SidePort');
        var trabFlap = _drawing.firstDoodleOfClass('TrabyFlap');

        if (_drawing.eye == ED.eye.Right) {
            sidePort.setParameterWithAnimation('rotation', 225 * (Math.PI / 180));
            trabFlap.setParameterWithAnimation('site', $('#Element_OphTrOperationnote_Trabeculectomy_site_id').children('option:selected').text());
        } else {
            sidePort.setParameterWithAnimation('rotation', 135 * (Math.PI / 180));
            trabFlap.setParameterWithAnimation('site', $('#Element_OphTrOperationnote_Trabeculectomy_site_id').children('option:selected').text());
        }
    }
}

function rotateVitrectomy() {
    var _drawing = ED.getInstance('ed_drawing_edit_Vitrectomy');

    if (_drawing.isNew) {
        _drawing.firstDoodleOfClass('Fundus').setParameterWithAnimation('rotation', 0);
    }
}

function glaucomaController(_drawing) {
    _drawing.registerForNotifications(this, 'notificationHandler', ['reset', 'resetEdit']);

    var position_id;

    this.notificationHandler = function (_messageArray) {
        switch (_messageArray['eventName']) {
            case 'ready':
                position_id = $('#Element_OphTrOperationnote_GlaucomaTube_plate_position_id').val();
                break;
            case 'reset':
                $('#Element_OphTrOperationnote_GlaucomaTube_plate_position_id').val('');
                break;
            case 'resetEdit':
                $('#Element_OphTrOperationnote_GlaucomaTube_plate_position_id').val(position_id);
                break;
        }
    }
}


function loadBiometryElementData() {
    var $higlightedEye,
        predictedRefraction = '',
        iolPower = '';

    $higlightedEye = $('.highlighted-eye');
    predictedRefraction = $higlightedEye.find('.js-predicted-refraction').text().trim();
    iolPower = $higlightedEye.find('.js-iol-display').text();
    selectedLens = $higlightedEye.find('.js-selected_lens').val();

    is_templated = (new URL(window.location)).searchParams.get('template_id') !== null;

    let nonzero_predicted_refraction = $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val() == ""
        || $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val() == 0;

    if (predictedRefraction && (nonzero_predicted_refraction || is_templated)) {
        if (predictedRefraction == "None") {
            $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val('');
        } else {
            $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val(predictedRefraction);
        }
    }

    let nonzero_iol_power = $('#Element_OphTrOperationnote_Cataract_iol_power').val() == "" || $('#Element_OphTrOperationnote_Cataract_iol_power').val() == 0

    if (iolPower && (nonzero_iol_power || is_templated)) {
        $.isNumeric(iolPower)
        {
            $('#Element_OphTrOperationnote_Cataract_iol_power').val(iolPower);
        }
    }

    if (selectedLens) {
        $('#Element_OphTrOperationnote_Cataract_iol_type_id').val(selectedLens);
    }
}

function highlightBiometryElement() {

    // right: 2
    // left: 1
    $('#ophTrOperationnotePCRRiskDiv').attr('style', 'display: block;');
    if ($('#Element_OphTrOperationnote_ProcedureList_eye_id_2').is(':checked')) {
        $('.right-eye').removeClass('deactivate').addClass('highlighted-eye');
        $('.left-eye').addClass('deactivate').removeClass('highlighted-eye');
        $('#ophCiExaminationPCRRiskLeftEye').hide();
        $('#ophCiExaminationPCRRiskRightEye').show();
    } else if ($('#Element_OphTrOperationnote_ProcedureList_eye_id_1').is(':checked')) {
        $('.left-eye').removeClass('deactivate').addClass('highlighted-eye');
        $('.right-eye').addClass('deactivate').removeClass('highlighted-eye');
        $('#ophCiExaminationPCRRiskRightEye').hide();
        $('#ophCiExaminationPCRRiskLeftEye').show();
    }

}

function loadOrClearTemplate(newTemplateId, eye) {
    const url = new URL(window.location);
    const currentTemplateId = url.searchParams.get('template_id') ?? '';

    if (currentTemplateId !== newTemplateId && newTemplateId != '') {
      url.searchParams.set('template_id', newTemplateId);
      url.searchParams.delete('template_clear');

      if (eye) {
        url.searchParams.set('eye', eye);
      }
    } else {
      url.searchParams.delete('template_id');
      url.searchParams.set('template_clear', '1');
      url.searchParams.delete('eye');
    }

    window.onbeforeunload = null;
    window.location = url;
}
