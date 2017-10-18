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

$( document ).ready(function() {
    highlightBiometryElement();
    if(window.location.href.indexOf("update") == -1) {
        loadBiometryElementData();
    }
});

function callbackAddProcedure(procedure_id) {
    var eye = $('input[name="Element_OphTrOperationnote_ProcedureList\\[eye_id\\]"]:checked').val();

    $.ajax({
        'type': 'GET',
        'url': baseUrl + '/OphTrOperationnote/Default/loadElementByProcedure?procedure_id=' + procedure_id + '&eye=' + eye +'&patientId=' + OE_patient_id,
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
                            $('.Element_OphTrOperationnote_ProcedureList .sub-elements').append(html);
                            var $lastMatchedElement = $('.' + m[1] + ':last');
                            $lastMatchedElement.attr('style', 'display: none;');
                            $lastMatchedElement.removeClass('hidden');
                            $lastMatchedElement.slideToggle('fast');
                        }
                    }
                }
                highlightBiometryElement();
                if(window.location.href.indexOf("update") == -1) {
                    loadBiometryElementData();
                }
            }
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
    handleButton($('#et_save'), function () {
        if ($('#Element_OphTrOperationnote_Buckle_report').length > 0) {
            $('#Element_OphTrOperationnote_Buckle_report').val(ED.getInstance('ed_drawing_edit_Buckle').report());
        }
        if ($('#Element_OphTrOperationnote_Cataract_report2').length > 0) {
            $('#Element_OphTrOperationnote_Cataract_report2').val(ED.getInstance('ed_drawing_edit_Cataract').report());
        }
    });

    handleButton($('#et_cancel'), function (e) {
        if (m = window.location.href.match(/\/update\/[0-9]+/)) {
            window.location.href = window.location.href.replace('/update/', '/view/');
        } else {
            window.location.href = baseUrl + '/patient/episodes/' + OE_patient_id;
        }
        e.preventDefault();
    });

    handleButton($('#et_deleteevent'));

    handleButton($('#et_canceldelete'));

    handleButton($('#et_print'), function (e) {
        e.preventDefault();
        printEvent(null);
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

        var element = $(this).closest('.sub-element');

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
        var element = $(this).closest('.sub-element');
        var drawing_name = $('#Element_OphTrOperationnote_GlaucomaTube_eyedraw').prev('canvas').data('drawing-name');
        reportEyedraw(element, ED.getInstance(drawing_name), 'description');
    });

    $(this).delegate('#btn-trabeculectomy-report', 'click', function (e) {
        e.preventDefault();
        var element = $(this).closest('.sub-element');
        var drawing_name = $('#Element_OphTrOperationnote_Trabeculectomy_eyedraw').prev('canvas').data('drawing-name');
        reportEyedraw(element, ED.getInstance(drawing_name), 'report');
    });

    $(this).delegate('#btn-trabectome-report', 'click', function (e) {
        e.preventDefault();
        var element = $(this).closest('.sub-element');
        var drawing_name = $('#Element_OphTrOperationnote_Trabectome_eyedraw').prev('canvas').data('drawing-name');
        reportEyedraw(element, ED.getInstance(drawing_name), 'description');
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
        if (typeof(OphTrOperationnote_reports[element.data('element-type-id')]) != 'undefined' &&
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


           /* $('#Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id').slideToggle('fast');
            $('#div_Element_OphTrOperationnote_Anaesthetic_Agents').slideToggle('fast');
            $('#div_Element_OphTrOperationnote_Anaesthetic_Complications').slideToggle('fast');
            $('#div_Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment').slideToggle('fast', function () {
                anaestheticSlide.anaestheticTypeSliding = false;

            });
*/
    }
}

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
}

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
                if ($(this).text() == '- Please select -') {
                    $(this).attr('selected', true);
                } else {
                    $(this).attr('selected', false);
                    if ($(this).text() == 'None') {
                        $(this).attr('disabled','disabled');
                    }
                }
            });
        }
    }
    else {
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

}

function sidePortController(_drawing) {
    var phakoIncision;
    var sidePort1;
    var sidePort2;

    var iol_position;
    var site_id;
    var type_id;
    var length;
    var meridian;

    // Register controller for notifications
    _drawing.registerForNotifications(this, 'notificationHandler', ['ready', 'beforeReset', 'reset', 'resetEdit', 'parameterChanged', 'doodleAdded', 'doodleDeleted', 'doodlesLoaded']);

    this.addSidePorts = function() {
        sidePort1 = _drawing.addDoodle('SidePort', {rotation: 0});
        sidePort2 = _drawing.addDoodle('SidePort', {rotation: Math.PI});
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
                }
                // Else cancel sync for an updated drawing
                else {
                    if (typeof(phakoIncision) != 'undefined') {
                        phakoIncision.willSync = false;
                    }
                }

                site_id = $('#Element_OphTrOperationnote_Cataract_incision_site_id').val();
                type_id = $('#Element_OphTrOperationnote_Cataract_incision_type_id').val();
                length = $('#Element_OphTrOperationnote_Cataract_length').val();
                meridian = $('#Element_OphTrOperationnote_Cataract_meridian').val();

                break;

            case 'beforeReset':
                iol_position = $('#Element_OphTrOperationnote_Cataract_iol_position_id').val();
                break;

            case 'reset':
                this.addSidePorts();
                break;
            case 'resetEdit':
                if ($(_drawing.canvas).parents('.eyedraw-row.cataract').data('isNew')) {
                    // new eyedraws are loaded in the same way as editing, so might still be a new
                    // eyedraw that is being reset.
                    this.addSidePorts();
                }
                $('#Element_OphTrOperationnote_Cataract_iol_position_id').val(iol_position);
                $('#Element_OphTrOperationnote_Cataract_incision_site_id').val(site_id);
                $('#Element_OphTrOperationnote_Cataract_incision_type_id').val(type_id);
                $('#Element_OphTrOperationnote_Cataract_length').val(length);
                $('#Element_OphTrOperationnote_Cataract_meridian').val(meridian);
                break;

            // Parameter change notification
            case 'parameterChanged':
                // Only sync for new drawings
                if (_drawing.isNew) {
                    // Get rotation value of surgeon doodle
                    var surgeonDrawing = ED.getInstance('ed_drawing_edit_Position');
                    var surgeonRotation = surgeonDrawing.firstDoodleOfClass('Surgeon').rotation;

                    // Get doodle that has moved in opnote drawing
                    var masterDoodle = _messageArray['object'].doodle;

                    // Stop syncing if PhakoIncision or a SidePort is changed
                    if (masterDoodle.drawing.isActive && (masterDoodle.className == 'PhakoIncision' || masterDoodle.className == 'SidePort')) {
                        if (typeof(phakoIncision) != 'undefined') {
                            phakoIncision.willSync = false;
                        }
                    }

                    // Keep sideports in sync with PhakoIncision while surgeon is still syncing with it
                    if (masterDoodle.className == "PhakoIncision" && masterDoodle.willSync) {
                        if (typeof(sidePort1) != 'undefined') {
                            sidePort1.setSimpleParameter('rotation', (surgeonRotation + Math.PI / 2) % (2 * Math.PI));
                        }
                        if (typeof(sidePort2) != 'undefined') {
                            sidePort2.setSimpleParameter('rotation', (surgeonRotation - Math.PI / 2) % (2 * Math.PI));
                        }
                    }
                }
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

    if (typeof(drawingEdit1) != 'undefined') {
        if (drawingEdit1.eye == ED.eye.Right) drawingEdit1.eye = ED.eye.Left;
        else drawingEdit1.eye = ED.eye.Right;

        // Set surgeon position to temporal side
        var doodle = drawingEdit1.firstDoodleOfClass('Surgeon');
        doodle.setParameterWithAnimation('surgeonPosition', 'Temporal');
    }

    if (typeof(drawingEdit2) != 'undefined') {
        alert('The eye state loaded for the cataract operation may no longer be correct. Please remove and re-add the procedure.');
        if (drawingEdit2.eye == ED.eye.Right) drawingEdit2.eye = ED.eye.Left;
        else drawingEdit2.eye = ED.eye.Right;
    }

    if (typeof(drawingEdit3) != 'undefined') {
        if (drawingEdit3.eye == ED.eye.Right) drawingEdit3.eye = ED.eye.Left;
        else drawingEdit3.eye = ED.eye.Right;

        rotateTrabeculectomy();
    }

    if (typeof(drawingEdit4) != 'undefined') {
        if (drawingEdit4.eye == ED.eye.Right) drawingEdit4.eye = ED.eye.Left;
        else drawingEdit4.eye = ED.eye.Right;

        rotateVitrectomy();
    }

    if(typeof pcrCalculate === 'function'){
        if($("#Element_OphTrOperationnote_ProcedureList_eye_id_2").attr('checked') == "checked") {
            pcrCalculate($('#ophCiExaminationPCRRiskRightEye'), 'right');
        }

        if($("#Element_OphTrOperationnote_ProcedureList_eye_id_1").attr('checked') == "checked") {
            pcrCalculate($('#ophCiExaminationPCRRiskLeftEye'), 'left');
        }
    }

    // we remove the current values when the user change the eye
    $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val('');
    $('#Element_OphTrOperationnote_Cataract_iol_power').val('');
    highlightBiometryElement();
    if(window.location.href.indexOf("update") == -1) {
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


function loadBiometryElementData(){
    var $higlightedEye,
        predictedRefraction = '',
        iolPower = '';

    $higlightedEye = $('.highlighted-eye');
    predictedRefraction = $higlightedEye.find('.predictedRefraction').text();
    iolPower = $higlightedEye.find('.iolDisplay').text();
    selectedLens = $higlightedEye.find('.selected_lens').val();

    if(predictedRefraction &&  ($('#Element_OphTrOperationnote_Cataract_predicted_refraction').val() == "" ||  $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val() == 0)){
        if(predictedRefraction == "None"){
            $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val('');
        }else {
            $('#Element_OphTrOperationnote_Cataract_predicted_refraction').val(predictedRefraction);
        }
    }

    if(iolPower && ($('#Element_OphTrOperationnote_Cataract_iol_power').val() == "" || $('#Element_OphTrOperationnote_Cataract_iol_power').val() == 0)){
        $.isNumeric(iolPower)
        {
            $('#Element_OphTrOperationnote_Cataract_iol_power').val(iolPower);
        }
    }

    if(selectedLens){
        $('#Element_OphTrOperationnote_Cataract_iol_type_id').val(selectedLens);
    }
}

function highlightBiometryElement( ){

    // right: 2
    // left: 1
    $('#ophTrOperationnotePCRRiskDiv').attr('style', 'display: block;');
    if( $('#Element_OphTrOperationnote_ProcedureList_eye_id_2').is(':checked') ){
        $('.right-eye').removeClass('disabled-eye').addClass('highlighted-eye');
        $('.left-eye').removeClass('highlighted-eye').addClass('disabled-eye');
        $('#ophCiExaminationPCRRiskLeftEye').hide();
        $('#ophCiExaminationPCRRiskRightEye').show();
    }else if($('#Element_OphTrOperationnote_ProcedureList_eye_id_1').is(':checked') ){
        $('.left-eye').removeClass('disabled-eye').addClass('highlighted-eye');
        $('.right-eye').removeClass('highlighted-eye').addClass('disabled-eye');
        $('#ophCiExaminationPCRRiskRightEye').hide();
        $('#ophCiExaminationPCRRiskLeftEye').show();
    }

}
