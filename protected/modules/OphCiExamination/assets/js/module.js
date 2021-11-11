/**
 * OpenEyes
 *
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var dr_grade_et_class = 'Element_OphCiExamination_DRGrading';
var module_css_path;
var OphCiExamination_reports = {};

function gradeCalculator(_drawing) {
    var doodleArray = _drawing.doodleArray;

    var side = 'right';
    if (_drawing.eye) {
        side = 'left';
    }

    // Array to store counts of doodles of relevant classes
    var countArray = new Array();
    countArray['Microaneurysm'] = 0;
    countArray['HardExudate'] = 0;
    countArray['Circinate'] = 0;
    countArray['BlotHaemorrhage'] = 0;
    countArray['PreRetinalHaemorrhage'] = 0;
    countArray['CottonWoolSpot'] = 0;
    countArray['DiabeticNV'] = 0;
    countArray['FibrousProliferation'] = 0;
    countArray['LaserSpot'] = 0;
    countArray['FocalLaser'] = 0;
    countArray['MacularGrid'] = 0;
    countArray['SectorPRPPostPole'] = 0;
    countArray['PRPPostPole'] = 0;
    countArray['IRMA'] = 0;
    countArray['TractionRetinalDetachment'] = 0;

    var retinopathy = "NO";
    var maculopathy = "NO";
    var retinopathy_photocoagulation = false;
    var maculopathy_photocoagulation = false;
    var clinicalret = "NR";
    var clinicalmac = "NM";
    var dnv_within = false;

    // Get reference to PostPole doodle
    var postPole = _drawing.lastDoodleOfClass('PostPole');

    if (postPole) {
        // Iterate through doodles counting, and checking location
        for (var i = 0; i < doodleArray.length; i++) {
            var doodle = doodleArray[i];
            countArray[doodle.className]++;

            // Exudates within one disk diameter of fovea
            if (doodle.className == 'HardExudate' || doodle.className == 'Circinate') {
                if (postPole.isWithinDiscDiametersOfFovea(doodle, 1)) maculopathy = 'MA';
            }
            //TODO: needs to check against optic disc, not Fovea
            /*
            if (doodle.className == 'DiabeticNV') {
                if (postPole.isWithinDiscDiametersOfFovea(doodle,1)) dnv_within = true;
            }
            */
            if (doodle.className == 'LaserSpot' || doodle.className == 'FocalLaser') {
                if (postPole.isWithinArcades(doodle)) {
                    retinopathy_photocoagulation = true;
                } else {
                    maculopathy_photocoagulation = true;
                }
            }
        }

        if (countArray['Microaneurysm'] > 0 || countArray['HardExudate'] > 0) {
            clinicalret = 'MN';
        }

        if (countArray['BlotHaemorrhage'] > 0 || countArray['IRMA'] > 0 || countArray['PreRetinalHaemorrhage']) {
            clinicalret = 'MO';
        }

        if ((countArray['PreRetinalHaemorrhage'] || countArray['BlotHaemorrhage'] > 0) && countArray['IRMA'] > 0) {
            clinicalret = 'SR';
        }

        if (countArray['DiabeticNV'] > 0) {
            clinicalret = 'EP';
            if (dnv_within || countArray['PreRetinalHaemorrhage']) {
                clinicalret = 'HR';
            }
        }

        if (countArray['BlotHaemorrhage'] > 0 || countArray['Microaneurysm'] > 0) {
            var bestVa = OphCiExamination_VisualAcuity_bestForSide(side);

            if (bestVa !== null && bestVa <= 95) {
                maculopathy = 'MA';
            }
        }

        // R1 (Background)
        if (countArray['Microaneurysm'] > 0 || countArray['BlotHaemorrhage'] > 0 || countArray['HardExudate'] > 0 || countArray['CottonWoolSpot'] > 0 || countArray['Circinate'] > 0) {
            retinopathy = "BA";
        }

        // R2
        if (countArray['BlotHaemorrhage'] >= 2 || countArray['IRMA'] > 0) {
            retinopathy = "PP";
        }

        // R3
        if (countArray['PRPPostPole'] > 0) {
            retinopathy = "PE";
            retinopathy_photocoagulation = true;
        }
        if (countArray['DiabeticNV'] > 0 || countArray['PreRetinalHaemorrhage'] > 0 || countArray['FibrousProliferation'] > 0 || countArray['TractionRetinalDetachment'] > 0) {
            retinopathy = "PR";
        }

        if (countArray['SectorPRPPostPole'] > 0 || countArray['MacularGrid'] > 0) {
            maculopathy_photocoagulation = true;
        }

        // basic default setting for clincal maculopathy at the moment:
        if (maculopathy == 'MA') clinicalmac = 'DS';

        return [retinopathy, maculopathy, retinopathy_photocoagulation, maculopathy_photocoagulation, clinicalret, clinicalmac];
    }
    return false;
}

function addDRFeature($container, selectedItems, template, side) {
    // Selected item/s are features.
    let feature_id_list = [];
    let ma_count = null;
    // If the feature from the itemSet is '(MA)', set the ma_count. Otherwise, add the feature ID as normal to the list.
    // The assumption made here is that MA has been selected from the R1 list before an MA value was selected.
    $.each(selectedItems, function(index, item) {
        if (item.itemSet.options.header === '(MA)') {
            ma_count = item;
        } else if (item.itemSet.options.header !== '(MA)' && item.label !== 'DR') {
            feature_id_list.push(item.id);
        }
    });

    // Obtain the main data points for the selected features and add rows for each one.
    $.ajax({
        url: '/OphCiExamination/default/getDrFeatures',
        data: {
            feature_list: feature_id_list
        },
        success: function(response) {
            $($container).find("tbody").empty();
            if (typeof response !== 'object') {
                response = JSON.parse(response);
            }
            $.each(response, function(index, item) {
                var data = {
                    grade: item.grade,
                    id: item.id,
                    name: ((ma_count !== null && item.name === 'MA') ? ma_count.id + ' ' + item.name : item.name),
                    index: index,
                    feature_count: (ma_count !== null && item.name === 'MA') ? ma_count.id : null,
                    side: side
                };
                var row = Mustache.render(template, data);
                $($container).find("tbody").append(row);
            });
        },
        error: function(request, status, error) {
            let content = "Unable to add DR features due to ";
            if (status === 'error' || status === null) {
                content = content + 'unknown error.';
            } else {
                content = content + status + '.';
            }
            console.log(error);
            new OpenEyes.UI.Dialog.Alert({
                content: content,
            });
        }
    });
}

//returns the number of weeks booking recommendation from the DR grades (based on nsc retinopathy value for the given side)
function getDRBookingVal(side) {
    var dr_grade = $('.' + dr_grade_et_class);
    var booking = null;

    var val = dr_grade.find('select#' + dr_grade_et_class + '_' + side + '_nscretinopathy_id').val();
    $('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_id').find('option').each(function() {
        if ($(this).val() == val) {
            var b = parseInt($(this).attr("data-booking"));
            if (b && (booking == null || b < booking)) {
                booking = b;
                return false;
            }
        }
    });

    return booking;
}

// sets the booking hint text based on the DR grade
function updateBookingWeeks(side) {
    var weeks = getDRBookingVal(side);
    if (weeks) {
        $('.Element_OphCiExamination_LaserManagement').find('#' + side + '_laser_booking_hint').text('Laser treatment needs to be booked within ' + weeks.toString() + ' weeks').show();
    } else {
        $('.Element_OphCiExamination_LaserManagement').find('#' + side + '_laser_booking_hint').text('').hide();
    }
}

function updateDRGrades(_drawing, retinopathy, maculopathy, ret_photo, mac_photo, clinicalret, clinicalmac) {
    if (_drawing.eye) {
        var side = 'left';
    } else {
        var side = 'right';
    }

    var dr_grade = $('.js-active-elements .' + OE_MODEL_PREFIX + dr_grade_et_class);
    // clinical retinopathy
    var crSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalret_id');
    crSel.find('option').each(function() {
        if ($(this).attr('data-code') == clinicalret) {
            crSel.val($(this).val());
            crSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            return false;
        }
    });

    // description
    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalret_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalret_desc_' + clinicalret.replace(/\s+/g, '')).show();

    // clinical maculopathy
    var cmSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalmac_id');
    cmSel.find('option').each(function() {
        if ($(this).attr('data-code') == clinicalmac) {
            cmSel.val($(this).val());
            cmSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            return false;
        }
    });

    // description
    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalmac_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalmac_desc_' + clinicalmac.replace(/\s+/g, '')).show();

    // Retinopathy
    var retSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_id');
    retSel.find('option').each(function() {
        if ($(this).attr('data-code') == retinopathy) {
            retSel.val($(this).val());
            retSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            return false;
        }
    });

    ret_photo_id = OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_photocoagulation_';
    if (ret_photo) {
        dr_grade.find('input#' + ret_photo_id + '1').attr('checked', 'checked');
    } else {
        dr_grade.find('input#' + ret_photo_id + '0').attr('checked', 'checked');
    }

    // display description
    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_desc_' + retinopathy).show();

    // Maculopathy
    var macSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscmaculopathy_id');
    macSel.find('option').each(function() {
        if ($(this).attr('data-code') == maculopathy) {
            macSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            macSel.val($(this).val());
            return false;
        }
    });

    mac_photo_id = OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscmaculopathy_photocoagulation_';
    if (mac_photo) {
        dr_grade.find('input#' + mac_photo_id + '1').attr('checked', 'checked');
    } else {
        dr_grade.find('input#' + mac_photo_id + '0').attr('checked', 'checked');
    }

    // display description
    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscmaculopathy_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscmaculopathy_desc_' + maculopathy).show();

    updateBookingWeeks(side);
}

/**
 * Listener function for Anterior Segment to make the Traby Flap doodle deletable
 *
 * @param _drawing
 */
function anteriorListener(_drawing) {
    this.drawing = _drawing;

    this.drawing.registerForNotifications(this, 'callBack', ['doodleAdded']);

    this.callBack = function(_messageArray) {
        var obj = _messageArray.object;
        if (obj.className == 'TrabyFlap' || obj.className == 'Tube') {
            obj.isDeletable = true;
            this.drawing.selectDoodle(obj);
        }
    }
}

$(document).ready(function() {
    if (!$('#OphCiExamination_allergy').find('tr').length) {
        $('.allergies_confirm_no').show();
    }
    $(this).delegate('#js-search-in-event', 'click', function(e) {
        showSearch();
        $('#js-search-in-event').addClass('selected');
        $('.main-event').addClass('examination-search-active');
    });

    // popup
    function showSearch() {
        $('#js-search-in-event-popup').show();

        $('.close-icon-btn').click(function() {
            $('.main-event').removeClass('examination-search-active');
            $('#js-search-in-event-popup').hide();
            $('#js-search-in-event').removeClass('selected');
            $('#js-search-event-input-right').val('');
            $("#js-search-event-input-left").val('');
            $('#js-search-event-results').hide();
        });

    }

    $(this).on('click', '#et_print', function(e) {
        e.preventDefault();
        printEvent(null);
    });

    $('#pcr-risk-info').tooltip({
        show: {
            effect: "slideDown",
            delay: 250
        }
    });

    // because these elements will be loaded later with AJAX we need to use live()
    $('.btn_save_allergy').live('click', OphCiExamination_AddAllergy);
    $('#allergy_id').live('change', function() {
        if ($(this).find(':selected').text() == 'Other') {
            $('#allergy_other').slideDown('fast');
        } else {
            $('#allergy_other').slideUp('fast');
        }
    });

    $('.removeAllergy').live('click', function() {
        var row = $(this).parent().parent();
        var allergy_id = row.data('allergy-id');
        var allergy_name = row.data('allergy-name');
        if (allergy_name != 'Other') {
            $('#allergy_id').append('<option value="' + allergy_id + '">' + allergy_name + '</option>');
        }
        if (row.data('assignment-id') !== undefined) {
            $('#OphCiExamination_allergy').append('<input type="hidden" name="deleted_allergies[]" value="' + row.data('assignment-id') + '">');
        }
        row.remove(); // we remove the <tr>
        if ($('#OphCiExamination_allergy tr').length == 0) {
            $('.allergies_confirm_no').slideDown('fast');
        }
    });

    $(this).delegate('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_GlaucomaRisk_risk_id', 'change', function() {
        // Update Clinic Outcome follow up
        var clinic_outcome_element = $('.js-active-elements .' + OE_MODEL_PREFIX + 'Element_OphCiExamination_ClinicOutcome');
        if (clinic_outcome_element.length) {
            var template_id = $('option:selected', this).attr('data-clinicoutcome-template-id');
            OphCiExamination_ClinicOutcome_LoadTemplate(template_id);
        }

    });
    $(this).delegate('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_GlaucomaRisk a.descriptions_link', 'click', function() {
        var glaucoma_dialog = new OpenEyes.UI.Dialog({
            title: 'Glaucoma Risk Stratifications',
            content: $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_GlaucomaRisk_descriptions').clone(),
            width: "50%"
        });
        glaucoma_dialog.open();
        $(glaucoma_dialog.content).find('.glaucoma-risk-descriptions').show();
        $(glaucoma_dialog.content).on('click', '.status-box a', function(e) {
            var value = $(this).attr('data-risk-id');
            $('.oe-popup-wrap').not('#js-overlay').remove();
            $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_GlaucomaRisk_risk_id').val(value).trigger('change');
        });
    });

    /**
     * Populate description from eyedraw
     */
    $(this).delegate('.ed_report', 'click', function(e) {

        e.preventDefault();

        var element = $(this).closest('.element');

        // Get side (if set)
        var side = null;
        if ($(this).closest('[data-side]').length) {
            side = $(this).closest('[data-side]').attr('data-side');
        }

        // Get eyedraw js object
        var eyedraw = element.attr('data-element-type-id');
        if (side) {
            eyedraw = side + '_' + eyedraw;
        }
        eyedraw = ED.getInstance('ed_drawing_edit_' + eyedraw);

        // Get report text and strip trailing comma
        var text = eyedraw.report();
        text = text.replace(/, +$/, '');

        // Update description
        var description = 'description';
        if (side) {
            description = side + '_' + description;
        }
        description = $('textarea[name$="[' + description + ']"]', element).first();

        if (description.val() == '') {
            OphCiExamination_reports[element.data('element-type-id')] = text;
            description.val(text);
        } else {
            if (typeof(OphCiExamination_reports[element.data('element-type-id')]) != 'undefined' &&
                description.val().indexOf(OphCiExamination_reports[element.data('element-type-id')]) != -1) {
                description.val(description.val().replace(new RegExp(OphCiExamination_reports[element.data('element-type-id')]), text));
                OphCiExamination_reports[element.data('element-type-id')] = text;
            } else {
                description.val(text);
            }
        }

        description.trigger('autosize');

        // Update diagnoses
        var code = eyedraw.diagnosis();

        for (var i in code) {
            var max_id = -1;
            var already_in_list = false;
            var list_eye_id = null;
            var existing_id = null;

            $('#OphCiExamination_diagnoses').children('tr').map(function() {
                var id = parseInt($(this).find('.eye input:first').attr('name').match(/[0-9]+/));
                if (id >= max_id) {
                    max_id = id;
                }

                if ($(this).children('td:nth-child(4)').children('a:first').attr('rel') == code[i]) {
                    already_in_list = true;
                    list_eye_id = $('input[name="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Diagnoses[eye_id_' + id + ']"]:checked').val();
                    existing_id = id;
                }
            });

            var eye_id = side == 'right' ? 2 : 1;

            if (already_in_list) {
                if (eye_id != list_eye_id) {
                    $('input[name="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Diagnoses[eye_id_' + existing_id + ']"][value="3"]').attr('checked', 'checked');
                }
            } else {
                $.ajax({
                    'type': 'GET',
                    'url': baseUrl + '/OphCiExamination/default/getDisorder?disorder_id=' + code[i],
                    'success': function(json) {
                        OphCiExamination_AddDiagnosis(json.id, json.name, eye_id);
                    }
                });
            }
        }
    });

    /**
     * Clear eyedraw
     */
    $(this).delegate('.ed_clear', 'click', function(e) {
        var element = $(this).closest('.element');

        // Get side (if set)
        var side = null;
        if ($(this).closest('[data-side]').length) {
            side = $(this).closest('[data-side]').attr('data-side');
        }

        // Clear inputs marked as clearWithEyedraw
        if (side) {
            var element_or_side = $(this).closest('.js-element-eye');
        } else {
            var element_or_side = element;
        }
        $('.clearWithEyedraw', element_or_side).each(function() {
            if (side) {
                if (side == 'left') {
                    if ($(this).attr('id').match(/_left_description$/)) {
                        $(this).val('');
                    }
                } else {
                    if ($(this).attr('id').match(/_right_description$/)) {
                        $(this).val('');
                    }
                }
            } else {
                if ($(this).attr('id').match(/_description$/)) {
                    $(this).val('');
                }
            }
        });

        for (var i in eyedraw_added_diagnoses) {
            $('a.removeDiagnosis[rel="' + eyedraw_added_diagnoses[i] + '"]').click();
        }

        eyedraw_added_diagnoses = [];

        e.preventDefault();
    });

    // dr grading
    $(this).delegate('a.drgrading_images_link', 'click', function(e) {
        $('.drgrading_images_dialog').dialog('open');
        e.preventDefault();
    });

    // Note. a manual change to DR grade will mark the grade as unsynced, regardless of whether the user
    // manually syncs or not, as we are using the manual change as an indicator that we should no longer automatically
    // update the values. Although this will not apply between saves
    $(this).delegate(
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_right_clinicalret_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_left_clinicalret_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_right_clinicalmac_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_left_clinicalmac_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_right_nscretinopathy_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_left_nscretinopathy_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_right_nscmaculopathy_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_left_nscmaculopathy_id', 'change',
        function(e) {

            var side = getSplitElementSide($(this));
            var gradePK = $(this).val();
            var gradeCode = null;

            $(this).find('option').each(function() {
                if ($(this).attr('value') == gradePK) {
                    gradeCode = $(this).attr('data-code');
                    return false;
                }
            });

            var id = $(this).attr('id');
            var drGradeEl = $(this).parents('.element');
            var desc = id.substr(0, id.length - 2) + 'desc';
            drGradeEl.find('.' + desc).hide();
            drGradeEl.find('#' + desc + '_' + gradeCode).show();

            $(this).closest('.wrapper').removeClass('high severe high-risk proliferative maculopathy moderate pre-prolif mild early background peripheral ungradable low none');
            $(this).closest('.wrapper').addClass($('option:selected', this).attr('class'));

            updateBookingWeeks(side);
        })

    $('body').delegate('.grade-info-all .status-box a', 'click', function(e) {
        var value = $(this).data('id');
        var select_id = $(this).parents('.grade-info-all').data('select-id');
        $(this).parents('.grade-info-all').dialog('close');
        $('#' + select_id).val(value).trigger('change');
        e.preventDefault();
    });



    // management
    function isDeferralOther(element, name) {
        var reasonPK = $('#' + element + '_' + name + '_deferralreason_id').val();
        var other = false;

        $('#' + element + '_' + name + '_deferralreason_id').find('option').each(function() {
            if ($(this).attr('value') == reasonPK) {
                if ($(this).attr('data-other') == "1") {
                    other = true;
                    return false;
                }
            }
        });

        return other;
    }

    function showDeferralOther(element, name) {
        $('#div_' + element + '_' + name + '_deferralreason_other').slideDown().find('textarea').each(function(e) {
            if ($(this).data('stored-value')) {
                // must've changed their mind, restore the value
                $(this).val($(this).data('stored-value'));
            }
            autosize($(this));

        });
    }

    function hideDeferralOther(element, name) {
        if ($('#div_' + element + '_' + name + '_deferralreason_other').is(':visible')) {
            // because of the value storage, only want to do this if its showing
            $('#div_' + element + '_' + name + '_deferralreason_other').slideUp().find('textarea').each(function(e) {
                // clear text value to prevent submission, but store to make available if user changes their mind
                $(this).data('stored-value', $(this).val());
                $(this).val('');
            });
        }
    }

    // abstracted to manage the deferral fields for laser/injection
    function deferralFields(element, name) {
        var thePK = $('#' + element + '_' + name + '_status_id').val();
        // flag for deferred fields
        var deferred = false;
        // flag for booking hint
        var book = false;
        // flag for event creation hint
        var event = false;

        $('#' + element + '_' + name + '_status_id').find('option').each(function() {
            if ($(this).attr('value') == thePK) {
                if ($(this).attr('data-deferred') == "1") {
                    deferred = true;
                }
                if ($(this).attr('data-book') == "1") {
                    book = true;
                }
                if ($(this).data('event') == '1') {
                    event = true;
                }
                return false;
            }
        });

        if (book) {
            if ($('.' + element).find('#' + name + '_booking_hint').contents().length) {
                unmaskFields($('.' + element).find('#' + name + '_booking_hint'));
            }
        } else {
            maskFields($('.' + element).find('#' + name + '_booking_hint'));
        }

        if (event) {
            unmaskFields($('.' + element).find('#' + name + '_event_hint'));
        } else {
            maskFields($('.' + element).find('#' + name + '_event_hint'));
        }


        if (deferred) {
            $('#div_' + element + '_' + name + '_deferralreason').slideDown();
            if ($('#' + element + '_' + name + '_deferralreason_id').data('stored-value')) {
                $('#' + element + '_' + name + '_deferralreason_id').val(
                    $('#' + element + '_' + name + '_deferralreason_id').data('stored-value')
                );
                if (isDeferralOther(name)) {
                    showDeferralOther(name);
                }
            }
        } else {

            $('#div_' + element + '_' + name + '_deferralreason').slideUp();
            if ($('#' + element + '_' + name + '_deferralreason_id').val()) {
                $('#' + element + '_' + name + '_deferralreason_id').data('stored-value', $('#' + element + '_' + name + '_deferralreason_id').val());
                $('#' + element + '_' + name + '_deferralreason_id').val('');
                // call the hide on other in case it's currently showing
                hideDeferralOther(name);
            }
        }
    }

    // show/hide the laser deferral fields
    $(this).delegate('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement_left_laser_status_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement_right_laser_status_id', 'change',
        function() {
            var side = getSplitElementSide($(this));
            deferralFields(OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement', side + '_laser');
            var selVal = $(this).val();
            var showFields = false;
            $(this).find('option').each(function() {
                if ($(this).val() == selVal) {
                    if ($(this).data('book') == '1' || $(this).data('event') == '1') {
                        // need to gather further information
                        showFields = true;
                    }
                    return true;
                }
            });

            if (showFields) {
                unmaskFields($('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement_' + side + '_treatment_fields'));
            } else {
                maskFields($('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement_' + side + '_treatment_fields'));
            }

        });

    $(this).delegate('.lasertype select', 'change', function(e) {
        var selVal = $(this).val();
        var showOther = false;
        $(this).find('option').each(function() {
            if ($(this).val() == selVal) {
                if ($(this).data('other') == '1') {
                    showOther = true;
                }
                return true;
            }
        });

        if (showOther) {
            $(this).parents('.js-element-eye').find('.lasertype_other').show();
        } else {
            $(this).parents('.js-element-eye').find('.lasertype_other').hide();
        }
    });

    // show/hide the injection deferral fields
    $(this).delegate('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagement_injection_status_id', 'change', function() {
        deferralFields(OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagement', 'injection');
    });

    // show/hide the deferral reason option
    $(this).delegate('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement_left_laser_deferralreason_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement_right_laser_deferralreason_id', 'change',
        function() {
            var side = getSplitElementSide($(this));
            var other = isDeferralOther(OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement', side + '_laser');

            if (other) {
                showDeferralOther(OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement', side + '_laser');
            } else {
                hideDeferralOther(OE_MODEL_PREFIX + 'Element_OphCiExamination_LaserManagement', side + '_laser');
            }
        });

    // show/hide the deferral reason option
    $(this).delegate('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagement_injection_deferralreason_id', 'change', function() {
        var other = isDeferralOther('' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagement', 'injection');

        if (other) {
            showDeferralOther(OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagement', 'injection');
        } else {
            hideDeferralOther(OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagement', 'injection');
        }
    });


    // end of management

    // investigation

    // OCT
    $(this).delegate('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_OCT input[name="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_OCT[right_dry]"], ' +
        'input[name="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_OCT[left_dry]"]', 'change',
        function(e) {
            // need to check the value - if it's 0 we should the fluid for the side. otherwise hide it.
            var side = getSplitElementSide($(this));
            if ($(this)[0].value == '0') {
                unmaskFields($('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_OCT_' + side + '_fluid_fields'), null);
                unmaskFields($('#tr_Element_OphCiExamination_OCT_' + side + '_fluidstatus_id'), null);
            } else {
                maskFields($('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_OCT_' + side + '_fluid_fields'), null);
                maskFields($('#tr_Element_OphCiExamination_OCT_' + side + '_fluidstatus_id'), null);
            }
        });

    // end of OCT

    // end of management

    $('#event-content').delegate('.element input[name$="_pxe]"]', 'change', function() {
        var side = $(this).closest('[data-side]').attr('data-side');
        var element_type_id = $(this).closest('.element').attr('data-element-type-id');
        var eyedraw = ED.getInstance('ed_drawing_edit_' + side + '_' + element_type_id);
        eyedraw.setParameterForDoodleOfClass('AntSeg', 'pxe', $(this).is(':checked'));
    });

    $(this).delegate('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Gonioscopy .gonioscopy-mode', 'change', function() {
        OphCiExamination_Gonioscopy_update(this);
    });

    /**
     * Update gonioExpert when gonioBasic is changed (gonioBasic controls are not stored in DB)
     */
    $('body').on('change', '.gonioBasic', function(e) {
        var position = $(this).attr('data-position');
        var expert = $(this).closest('.js-element-eye').find('.gonioExpert[data-position="' + position + '"]');
        if ($(this).val() === '0') {
            $('option', expert).attr('selected', function() {
                return ($(this).attr('data-value') === '0');
            });
        } else {
            $('option', expert).attr('selected', function() {
                return ($(this).attr('data-value') === '4');
            });
        }
        e.preventDefault();
    });

    /**
     * Update gonioBasic when gonioExpert is changed
     */
    $('body').on('change', '.gonioExpert', function(e) {
        var position = $(this).attr('data-position');
        var basic = $(this).closest('.js-element-eye').find('.gonioBasic[data-position="' + position + '"]');
        if ($(this).val() === '5') {
            $('option', basic).attr('selected', function() {
                return ($(this).attr('data-value') === 'No');
            });
        } else {
            $('option', basic).attr('selected', function() {
                return ($(this).attr('data-value') === 'Yes');
            });
        }
        e.preventDefault();
    });

    /**
     * colour vision behaviours
     */
    $(this).delegate('.colourvision_method', 'change', function(e) {
        var side = $(this).closest('.js-element-eye').attr('data-side');
        OphCiExamination_ColourVision_addReading(this, side);
        e.preventDefault();
    });

    $(this).delegate('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_ColourVision .removeCVReading', 'click', function(e) {
        var wrapper = $(this).closest('.js-element-eye');
        var side = wrapper.attr('data-side');
        var row = $(this).closest('tr');
        var id = $('.methodId', row).val();
        var name = $('.methodName', row).text();
        row.remove();
        var method_select = wrapper.find('.colourvision_method');
        method_select.append('<option value="' + id + '">' + name + '</option>');
        sort_selectbox(method_select);

        // No readings
        if ($('[class*="colourvision_table"] tbody tr', wrapper).length === 0) {
            // Hide vision table
            $('[class*="colourvision_table"]', wrapper).hide();
            // Hide clear button
            $(wrapper).find('.clearCV').addClass('hidden');
        }
        e.preventDefault();
    });

    $(this).delegate('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_ColourVision .clearCV', 'click', function(e) {
        var side = $(this).closest('.js-element-eye').attr('data-side');
        $(this).closest('.js-element-eye').find('tr.colourvisionReading i.removeCVReading').click();
        $(this).addClass('hidden');
        e.preventDefault();
    });

    // clinic outcome functions
    $(this).on('change', '#patientticket_queue', function(e) {
        var id = $(e.target).val(),
            placeholder = $('#queue-assignment-placeholder');
        placeholder.html('');
        if (id) {
            $.ajax({
                url: $('#div_' + OE_MODEL_PREFIX + 'Element_OphCiExamination_ClinicOutcome_patientticket').data('queue-assignment-form-uri') + id,
                data: { label_width: 3, data_width: 5 },
                success: function(response) {
                    placeholder.html(response);
                },
                error: function(jqXHR, status, error) {
                    enableButtons();
                    throw new Error("Unable to retrieve assignment form for queue with id " + id + ": " + error);
                },
                complete: function() {}
            });
        }
    });
    // end of clinic outcome functions


    // perform the inits for the elements
    $('.js-active-elements .element').each(function(index, element) {

        // Ignore elements without an element type (such as the event date)
        if (!$(this).data('element-type-class')) {
            return;
        }

        var initFunctionName = $(this).data('element-type-class').replace(OE_MODEL_PREFIX + 'Element_', '') + '_init';
        if (typeof(window[initFunctionName]) === 'function') {
            window[initFunctionName]();
        }
    });

    updateTextMacros();

    // Refresh common ophthalmic diagnosis widget when findings element is changed
    $('.js-active-elements').on('MultiSelectChanged', '#OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings_further_findings_assignment', function() {
        OphCiExamination_RefreshCommonOphDiagnoses();
    });

    // Refresh common ophthalmic diagnosis widget when findings element is removed
    $(document).on('ElementRemoved', '.js-active-elements', function(event, element_class) {
        if (element_class == 'OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings') {
            OphCiExamination_RefreshCommonOphDiagnoses();
        }
    });

    // Handle removal of diagnoses from diagnosis element and trigger refresh of widget
    $('.js-active-elements').on('click', 'a.removeDiagnosis', function() {
        var disorder_id = $(this).attr('rel');
        var new_principal = false;

        if ($('input[name="principal_diagnosis"]:checked').val() == disorder_id) {
            new_principal = true;
        }

        $(this).closest('tr').remove();

        if (new_principal) {
            $('input[name="principal_diagnosis"]:first').attr('checked', 'checked');
        }

        OphCiExamination_RefreshCommonOphDiagnoses();

        $(":input[name^='glaucoma_diagnoses']").trigger('change');
        return false;
    });

    /** Post Operative Complication  Event Bindings **/

    $('#event-content').on('change', '#OphCiExamination_postop_complication_operation_note_id-select', function() {

        var element_id = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_PostOpComplications_id').val();
        var operation_note_id = $(this).val();
        var element_string = "";

        if (element_id !== "") {
            element_string = '/element_id/' + element_id;
        }

        $.getJSON(baseUrl + '/OphCiExamination/default/getPostOpComplicationList' + element_string + '/operation_note_id/' + operation_note_id, function(data) {
            var $right_table = $('#right-complication-list');
            var $left_table = $('#left-complication-list');

            $('#right-complication-list tr, #left-complication-list tr').remove();

            $.each(data.right_values, function(key, val) {
                addPostOpComplicationTr(val.name, 'right-complication-list', val.id, val.display_order);

            });
            $.each(data.left_values, function(key, val) {
                addPostOpComplicationTr(val.name, 'left-complication-list', val.id, val.display_order);
            });

            setPostOpComplicationTableText();

            $('#left-complication-select option').remove();
            $('#right-complication-select option').remove();

            $('#right-complication-select').append($('<option>').text("Select Common Complication"));
            $.each(data.right_select, function(key, val) {
                $('#right-complication-select').append($('<option>', { value: val.id, 'data-display_order': val.display_order }).text(val.name));
            });

            $('#left-complication-select').append($('<option>').text("Select Common Complication"));
            $.each(data.left_select, function(key, val) {
                $('#left-complication-select').append($('<option>', { value: val.id, 'data-display_order': val.display_order }).text(val.name));
            });

        });
    });

    $("#event-content").on('change', '#right-complication-select, #left-complication-select', function() {

        // https://bugs.jquery.com/ticket/9335
        // Chrome triggers "change" on .blur() if the value of the select has changed, so we do a blur before any changes
        $(this).blur();

        var table_id = $(this).attr('id').replace('select', 'list');
        var selected_text = $('#' + $(this).attr('id') + " option:selected").text();
        var select_value = $(this).val();

        if (select_value >= 0) {
            addPostOpComplicationTr(selected_text, table_id, select_value, $(this).find('option:selected').data('display_order'));
            $(this).find('option:selected').remove();
            setPostOpComplicationTableText();
        }

        $(this).val('');

    });

    $('#event-content').on('click', 'a.postop-complication-remove-btn', function() {

        var value = $(this).parent().find('input[type=hidden]').val();
        var text = $(this).parent().closest('tr').find('.postop-complication-name').data('complication-name');

        var select_id = $(this).closest('table').attr('id').replace('list', 'select');

        $select = $('#' + select_id);
        $select.append($('<option>', { value: value }).text(text));

        $(this).closest('tr').remove();

        setPostOpComplicationTableText();
    });
    /** End of Post Operative Complication Event Bindings **/
    $('#event-content').on('change', '.diagnosis-selection select', function() {
        let side = $(this).closest('.js-element-eye').data('side');
        OphCiExamination_InjectionManagementComplex_DiagnosisCheck(side);
    }).on('click', '.jsNoTreatment', function() {
        OphCiExamination_InjectionManagementComplex_init();
    });

    $('#episodes-and-events').on('sidebar_loaded', function() {
        $('li#side-element-Medication-Management').find('a').data('validation-function', medicationManagementValidationFunction);
    });


});
/** Post Operative Complication function **/
function setPostOpComplicationTableText() {
    var $right_table = $('#right-complication-list');
    var $left_table = $('#left-complication-list');

    var $active_form = $right_table.closest('.active-form');
    if ($right_table.find('tbody').find('tr').length === 0) {
        $active_form.find('.recorded').hide();
        $active_form.find('.no-recorded').show();
        $right_table.hide();
    } else {
        $active_form.find('.recorded').show();
        $active_form.find('.no-recorded').hide();
        $right_table.show();
    }

    $active_form = $left_table.closest('.active-form');
    if ($left_table.find('tbody').find('tr').length === 0) {
        $active_form.find('.recorded').hide();
        $active_form.find('.no-recorded').show();
        $left_table.hide();
    } else {
        $active_form.find('.recorded').show();
        $active_form.find('.no-recorded').hide();
        $left_table.show();
    }
}

function medicationManagementValidationFunction() {
    let getTimeUrl = '/Site/getCurrentTimestamp';

    let date = new Date();

    $.ajax({
        'type': 'GET',
        'url': getTimeUrl,
        'data': {},
        'success': function(data) {
            let milliseconds = data.timestamp * 1000;
            date = new Date(milliseconds);
        }
    });

    let todayDate = date.getDate() + " " + date.toLocaleString('default', { month: 'short' }) + " " + date.getFullYear();
    let todayDateWithLeadingZero = "0" + todayDate;
    let event_date = document.getElementsByClassName('js-event-date-input')[0].value;

    if (event_date === todayDate || event_date === todayDateWithLeadingZero) {
        return true;
    } else {
        new OpenEyes.UI.Dialog.Alert({
            content: "Medication Management cannot be added due to event date not being the current date"
        }).open();
        return false;
    }
};

function addPostOpComplicationTr(selected_text, table_id, select_value, display_order) {

    var $table = $('#' + table_id);
    var eye_abbreviation = $table.data('sideletter');
    var $tr = $('<tr>');
    var $td_name = $('<td>', { class: "postop-complication-name" }).text(selected_text).data('complication-name', selected_text);
    var $other_text = '';
    let existing_value = $table.find('input[value="' + select_value + '"]');

    if (!existing_value.length) {
        if (selected_text == "other") {
            $td_name = $td_name.text($td_name.text() + ' ');
            $other_text = $('<input type="text" value="" name="complication_other[' + eye_abbreviation + ']" id="complication_other_' + eye_abbreviation + '">')
        }

        var $hidden_input = $("<input>", {
            type: "hidden",
            id: 'complication_items_' + $table.data('sideletter') + '_' + $('#' + table_id + ' tr').length,
            name: 'complication_items[' + $table.data('sideletter') + '][' + $('#' + table_id + ' tr').length + ']',
            value: select_value,
        });
        $hidden_input.data('display_order', display_order);

        var $td_action = $('<td>', { class: 'right' }).html("<a class='postop-complication-remove-btn' href='javascript:void(0)'><i class='oe-i trash'></i></a>");
        $td_action.append($hidden_input);
        $td_name.append($other_text);

        $tr.append($td_name);
        $tr.append($td_action);
        $table.append($tr);
    }
}
/** End of Post Operative Complication function **/
function updateTextMacros() {
    var active_element_ids = [];
    $('.js-active-elements > .element, .js-active-elements .sub-elements.active > .sub-element').each(function() {
        active_element_ids.push($(this).attr('data-element-type-id'));
    });
    $('.js-active-elements .textMacro option').each(function() {
        if ($(this).val() && $.inArray($(this).attr('data-element-type-id'), active_element_ids) == -1) {
            disableTextMacro(this);
        }
    });
    $('.js-active-elements .textMacro').each(function() {
        var sort = false;
        if ($(this).data('disabled-options')) {
            var select = this;
            $(this).data('disabled-options').filter(function(option) {
                return $.inArray($(option).attr('data-element-type-id'), active_element_ids) != -1;
            }).forEach(function(option, index) {
                enableTextMacro(select, index, option);
                sort = true;
            });
        }
        if (sort) {
            var options = $('option', this);
            options.sort(function(a, b) {
                if (a.text > b.text) return 1;
                else if (a.text < b.text) return -1;
                else return 0;
            });
            $(this).empty().append(options);
        }
        if ($('option', this).length > 1) {
            $(this).removeAttr('disabled');
        } else {
            $(this).attr('disabled', 'disabled');
        }
    });
}

function disableTextMacro(option) {
    var disabled_options = $(option).parent().data('disabled-options');
    if (!disabled_options) {
        disabled_options = [];
    }
    disabled_options.push(option);
    $(option).parent().data('disabled-options', disabled_options);
    $(option).remove();
}

function enableTextMacro(select, index, option) {
    var disabled_options = $(select).data('disabled-options');
    $(select).append(option);
    disabled_options.splice(index, 1);
}

function OphCiExamination_ColourVision_getNextKey(side) {
    var keys = $('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_ColourVision [data-side="' + side + '"] .colourvisionReading').map(function(index, el) {
        return parseInt($(el).attr('data-key'));
    }).get();
    if (keys.length) {
        return Math.max.apply(null, keys) + 1;
    } else {
        return 0;
    }
}

function OphCiExamination_ColourVision_addReading(selected_items, eye_side, $table) {
    let template = $('#colourvision_reading_template').html();
    if (selected_items.length) {
        for (let index in selected_items) {
            let selected_data = [];
            selected_data.method_id = selected_items[index]['id'];
            selected_data.method_name = selected_items[index]['label'];
            selected_data.side = eye_side;
            selected_data.key = OphCiExamination_ColourVision_getNextKey(eye_side);
            selected_data.method_values = OphCiExamination_ColourVision_getMethodValues(selected_items[index]['id']);

            var form = Mustache.render(template, selected_data);
            $table.find('tbody').append(form);
        }
    }
}

/**
 * @return {string}
 */
function OphCiExamination_ColourVision_getMethodValues(method_id) {
    let method_values = '';
    // ColourVisionMethod values are being set in form_Element_OphCiExamination_ColourVision
    if (colourVisionMethodValues[method_id]) {
        for (let id in colourVisionMethodValues[method_id]) {
            if (colourVisionMethodValues[method_id].hasOwnProperty(id)) {
                method_values += '<option value="' + id + '">' + colourVisionMethodValues[method_id][id] + '</option>';
            }
        }
    }
    return method_values;
}


// Global function to route eyedraw event to the correct element handler
function eDparameterListener(drawing) {
    var doodle = null;
    if (drawing.selectedDoodle) {
        doodle = drawing.selectedDoodle;
    }
    var element_type = $(drawing.canvasParent).closest('.element').attr('data-element-type-class');
    if (typeof window['update' + element_type] === 'function') {
        window['update' + element_type](drawing, doodle);
    }
}

function sort_ul(element) {
    rootItem = element.children('li:first').text();
    element.append(element.children('li').sort(selectSort));
}

function OphCiExamination_OCT_init() {
    // history tool tip
    $(".Element_OphCiExamination_OCT").find('.sft-history').each(function() {
        var quick = $(this);
        var iconHover = $(this).parent().find('.sft-history-icon');

        iconHover.hover(function(e) {
            var infoWrap = $('<div class="quicklook"></div>');
            infoWrap.appendTo('body');
            infoWrap.html(quick.html());

            var offsetPos = $(this).offset();
            var top = offsetPos.top;
            var left = offsetPos.left + 25;

            top = top - (infoWrap.height() / 2) + 8;

            if (left + infoWrap.width() > 1150) left = left - infoWrap.width() - 40;
            infoWrap.css({ 'position': 'absolute', 'top': top + "px", 'left': left + "px" });
            infoWrap.fadeIn('fast');
        }, function(e) {
            $('body > div:last').remove();
        });
    });
}

// setup the dr grading fields (called once the Posterior Segment is fully loaded)
// will verify whether the form values match that of the loaded eyedraws, and if not, mark as dirty
function OphCiExamination_DRGrading_dirtyCheck(_drawing) {
    var dr_grade = $('.' + OE_MODEL_PREFIX + dr_grade_et_class);
    var grades = gradeCalculator(_drawing);
    if (grades === false)
        return;

    var retinopathy = grades[0],
        maculopathy = grades[1],
        ret_photo = grades[2] ? '1' : '0',
        mac_photo = grades[3] ? '1' : '0',
        clinicalret = grades[4],
        clinicalmac = grades[5],
        dirty = false,
        side = 'right';

    if (_drawing.eye) {
        side = 'left';
    }

    // clinical retinopathy
    var cSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalret_id');
    var cSelVal = cSel.val();

    cSel.find('option').each(function() {
        if ($(this).attr('value') == cSelVal) {
            if ($(this).attr('data-code') != clinicalret) {
                dirty = true;
                clinicalret = $(this).attr('data-code');
            }

            return false;
        }
    });

    // display clinical retinopathy description
    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalret_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalret_desc_' + clinicalret.replace(/\s+/g, '')).show();

    // clinical maculopathy
    var cmSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalmac_id');
    var cmSelVal = cmSel.val();

    cmSel.find('option').each(function() {
        if ($(this).attr('value') == cmSelVal) {
            if ($(this).attr('data-code') != clinicalmac) {
                dirty = true;
                clinicalmac = $(this).attr('data-code');
            }

            return false;
        }
    });

    // display clinical maculopathy description
    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalmac_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_clinicalmac_desc_' + clinicalmac.replace(/\s+/g, '')).show();

    //retinopathy
    var retSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_id');
    var retSelVal = retSel.val();

    retSel.find('option').each(function() {
        if ($(this).attr('value') == retSelVal) {
            if ($(this).attr('data-code') != retinopathy) {
                dirty = true;
                retinopathy = $(this).attr('data-code');
            }

            return false;
        }
    });

    // retinopathy photocogaulation
    // at the beginning neither Yes nor No value is selected so the value of this input field is undefined. However ret_photo is 0
    if (($('input[name="' + OE_MODEL_PREFIX + dr_grade_et_class + '\[' + side + '_nscretinopathy_photocoagulation\]"]:checked').val() || 0) != ret_photo) {
        dirty = true;
    }

    // maculopathy photocoagulation
    if (($('input[name="' + OE_MODEL_PREFIX + dr_grade_et_class + '\[' + side + '_nscmaculopathy_photocoagulation\]"]:checked').val() || 0) != mac_photo) {
        dirty = true;
    }

    // Maculopathy
    var macSel = dr_grade.find('select#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscmaculopathy_id');
    var macSelVal = macSel.val();

    macSel.find('option').each(function() {
        if ($(this).attr('value') == macSelVal) {
            if ($(this).attr('data-code') != maculopathy) {
                dirty = true;
                maculopathy = $(this).attr('data-code');
            }
            return false;
        }
    });

    // display descriptions
    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscretinopathy_desc_' + retinopathy).show();

    dr_grade.find('div .' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscmaculopathy_desc').hide();
    dr_grade.find('div#' + OE_MODEL_PREFIX + dr_grade_et_class + '_' + side + '_nscmaculopathy_desc_' + maculopathy).show();

    dr_grade.find('.js-element-eye[data-side="' + side + '"]').removeClass('uninitialised');
}

/**
 * returns true if the dr side can be updated with calculated grades
 *
 * @param side
 */
function OphCiExamination_DRGrading_canUpdate(side) {
    var dr_side = $(".js-active-elements ." + OE_MODEL_PREFIX + "Element_OphCiExamination_DRGrading").find('.js-element-eye[data-side="' + side + '"]');

    if (dr_side.length && !dr_side.hasClass('uninitialised')) {
        return true;
    }
    return false;
}

function OphCiExamination_DRGrading_init() {

    $('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading').find('.drgrading_images_dialog').dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: 480
    });

    $('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading').find('.grade-info').each(function() {
        var quick = $(this);
        var iconHover = $(this).parent().find('.grade-info-icon');

        iconHover.hover(function(e) {
            var infoWrap = $('<span class="oe-tooltip" style="text-align: center; position: fixed;"></span>');
            infoWrap.html(quick.html());
            var offset = $(this).offset();
            var leftPos = offset.left - 94; // tooltip is 200px (and center on the icon)

            var $selector = infoWrap.appendTo('body');
            var tooltip = $('.oe-tooltip');
            $selector.css('left', leftPos);
            $selector.css('top', offset.top - tooltip.height() - 20);
            $selector.fadeIn('fast');
        }, function(e) {
            $('body').find(".oe-tooltip").remove();
        });
    });

    $('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading').delegate('.grade-info-icon', 'click', function(e) {
        var side = getSplitElementSide($(this));
        var info_type = $(this).data('info-type');
        var dialog = new OpenEyes.UI.Dialog({
            title: '',
            content: $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_DRGrading_' + side + '_all_' + info_type + '_desc').clone(),
            dialogClass: 'oe-popup',
            width: "50%"
        });
        dialog.open();
        $(dialog.content).find('.grade-info-all').show();
        $(dialog.content).on('click', '.status-box a', function(e) {
            var value = $(this).attr('data-id');
            $('.oe-popup-wrap').not('#js-overlay').hide();
            $('#' + $(this).data('select-id')).val(value).trigger('change');
        });
        // remove hovering:
        $(this).trigger('mouseleave');
        e.preventDefault();
    });

}

function OphCiExamination_Management_init() {
    updateBookingWeeks('left');
    updateBookingWeeks('right');
}

/**
 * partner function to unmaskFields, will empty the input fields in the given element, ignoring
 * fields that match the given selector in ignore
 *
 * @param element
 * @param ignore
 */
function maskFields(element, ignore) {
    if (element.is(':visible')) {
        var els = element.find('input, select, textarea');
        if (ignore != null) {
            els = els.filter(':not(' + ignore + ')');
        }
        els.each(function() {
            if ($(this).attr('type') == 'radio') {
                $(this).data('stored-checked', $(this).prop('checked'));
            }
            $(this).data('stored-val', $(this).val());
            $(this).val('');
            $(this).prop('disabled', true);
        });
        element.hide();
    }
}

/**
 * partner function maskFields, will set values back into input fields in the given element that have been masked,
 * ignoring fields that match the given selector in ignore
 *
 * @param element
 * @param ignore
 */
function unmaskFields(element, ignore) {
    if (!element.is(':visible')) {
        var els = element.find('input, select, textarea');
        if (ignore != null && ignore.length > 0) {
            els = els.filter(':not(' + ignore + ')');
        }
        els.each(function() {
            if ($(this).attr('type') == 'radio') {
                $(this).prop('checked', $(this).data('stored-checked'));
            } else {
                $(this).val($(this).data('stored-val'));
            }
            $(this).prop('disabled', false);
        });
        element.show();
    }
}

function OphCiExamination_InjectionManagementComplex_check(side) {
    var model_side = OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_' + side;
    var no_treatment = $('#' + model_side + '_no_treatment');
    if (no_treatment.length > 0) { //truthiness
        val = no_treatment[0].checked;
    } else {
        val = false;
    }

    if (val) {
        var no_treatment_reason = $('#div_' + model_side + '_no_treatment_reason_id');
        unmaskFields(no_treatment_reason);
        maskFields($('#div_' + model_side + '_treatment_fields'), '[id$="eye_id"]');
        // if we have an other selection on no treatment, need to display the text field
        var selVal = no_treatment_reason.find('select').val();
        var other = false;

        $('#' + model_side + '_no_treatment_reason_id').find('option').each(function() {
            if ($(this).val() == selVal) {
                if ($(this).data('other') == '1') {
                    other = true;
                }
                return true;
            }
        });
        if (other) {
            unmaskFields($('#div_' + model_side + '_no_treatment_reason_other'));
        } else {
            maskFields($('#div_' + model_side + '_no_treatment_reason_other'));
        }
    } else {
        maskFields($('#div_' + model_side + '_no_treatment_reason_id'));
        maskFields($('#div_' + model_side + '_no_treatment_reason_other'));
        unmaskFields($('#div_' + model_side + '_treatment_fields'), '[id$="eye_id"]');
    }
}

function OphCiExamination_InjectionManagementComplex_loadQuestions(side) {
    var disorders = [
        $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis1_id').val(),
        $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis2_id').val()
    ];

    var params = {
        'disorders': disorders,
        'side': side
    };

    $.ajax({
        'type': 'GET',
        'url': OphCiExamination_loadQuestions_url + '?' + $.param(params),
        'success': function(html) {
            // ensure we maintain any answers for questions that still remain after load (e.g. only level 2 has changed)
            let answers = {};
            let model_side_id = '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_' + side;
            let questions = $(model_side_id + '_Questions');
            let parent = $(model_side_id + '_Questions_Parent');

            questions.find('input:radio:checked').each(function() {
                answers[$(this).attr('id')] = $(this).val();
            });

            questions.replaceWith(html);

            // !! to make sure it is a boolean
            parent.toggle(!!$(html).children().length);

            for (var ans in answers) {
                if (answers.hasOwnProperty(ans)) {
                    $('#' + ans + '[value=' + answers[ans] + ']').attr('checked', 'checked');
                }
            }
        }
    });
}

function OphCiExamination_InjectionManagementComplex_DiagnosisCheck(side) {
    let el = $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis1_id');
    let l2_wrapper = $('#' + side + '_diagnosis2_wrapper');

    if (el.is(":visible") && el.val()) {
        let l2_el = $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis2_id');
        let l2_data = $(el).find('option:selected').data('level2');
        let l2_selected_val = l2_el.val();

        l2_el.find('option').remove();
        l2_el.append($('<option>').val(null).text("Select"));

        if (l2_data) {
            for (let i in l2_data) {
                let $option = $('<option>').val(l2_data[i].id).text(l2_data[i].term);
                l2_el.append($option);
            }
            l2_wrapper.slideDown();
            l2_el.attr("disabled", false);

        } else {
            l2_wrapper.slideUp();
            l2_el.attr("disabled", true);
        }
        l2_el.val(l2_selected_val);
        OphCiExamination_InjectionManagementComplex_loadQuestions(side);
    } else {
        l2_wrapper.slideUp();
        $('#Element_OphCiExamination_InjectionManagementComplex_' + side + '_Questions').html('');
    }
}

/**
 * Function called dynamically (initFunctionName) based on element-type-class + _init
 * @constructor
 */
function OphCiExamination_InjectionManagementComplex_init() {
    OphCiExamination_InjectionManagementComplex_check('left');
    OphCiExamination_InjectionManagementComplex_check('right');

    $('.jsNoTreatment').find(':checkbox').bind('change', function() {
        var side = getSplitElementSide($(this));
        OphCiExamination_InjectionManagementComplex_check(side);
    });

    $('.' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_no_treatment_reason_id').find('select').bind('change', function() {
        var side = getSplitElementSide($(this));
        OphCiExamination_InjectionManagementComplex_check(side);
    });

    $('#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_right_diagnosis1_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_left_diagnosis1_id,' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_right_diagnosis2_id, ' +
        '#' + OE_MODEL_PREFIX + 'Element_OphCiExamination_InjectionManagementComplex_left_diagnosis2_id').bind('change', function() {
        var side = getSplitElementSide($(this));
        OphCiExamination_InjectionManagementComplex_DiagnosisCheck(side);
    });

}

// END InjectionManagementComplex

/**
 * Add disorder or finding to exam
 * @param string type
 * @param integer conditionId
 * @param string label
 * @constructor
 */

function OphCiExamination_AddFinding(finding_id, label) {
    var updateFindings = function() {
        $('#OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings_further_findings_assignment').val(finding_id).trigger('change');
        OphCiExamination_RefreshCommonOphDiagnoses();
    };
    if ($('.OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings').length > 0) {
        updateFindings();
    } else {
        var el = $("[data-element-type-class='OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings']");
        if (el.length) {
            addElement(el.first(), false, true, 0, {}, updateFindings);
        } else {
            if (event_sidebar) {
                event_sidebar.addElementByTypeClass('OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings', {}, updateFindings);
            } else {
                console.log('Cannot find sidebar to manipulate elements for VA change');
            }
        }
    }

}

function OphCiExamination_AddDiagnosis(disorderId, name, eyeId, isDiabetic, isGlaucoma, external) {

    console.error("OphCiExamination/assets/js/module.js :: OphCiExamination_AddDiagnosis() function is DEPRICATED;");

    var max_id = -1;
    var count = 0;
    $('#OphCiExamination_diagnoses').children('tr').not('.read-only').map(function() {
        var id = parseInt($(this).children('td:nth-child(2)').children('label:nth-child(1)').children('input').attr('name').match(/[0-9]+/));
        if (id >= max_id) {
            max_id = id;
        }
        count += 1;
    });

    var id = max_id + 1;

    eyeId = eyeId || $('input[name="' + OE_MODEL_PREFIX + 'OphCiExamination_Diagnosis[eye_id]"]:checked').val();

    var checked_principal = (count == 0 ? 'checked="checked" ' : '');

    var row = '<tr' + (external ? ' class="external"' : '') + '>' +
        '<td>' +
        ((isDiabetic) ? '<input type="hidden" name="diabetic_diagnoses[]" value="1" /> ' : '') +
        ((isGlaucoma) ? '<input type="hidden" name="glaucoma_diagnoses[]" value="1" /> ' : '') +
        '<input type="hidden" name="selected_diagnoses[]" value="' + disorderId + '" /> ' + name + ' </td>' +
        '<td class="eye">' +
        '<label class="inline">' +
        '<input type="radio" name="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Diagnoses[eye_id_' + id + ']" value="2" /> Right' +
        '</label> ' +
        '<label class="inline">' +
        '<input type="radio" name="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Diagnoses[eye_id_' + id + ']" value="3" /> Both' +
        '</label> ' +
        '<label class="inline">' +
        '<input type="radio" name="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Diagnoses[eye_id_' + id + ']" value="1" /> Left' +
        '</label> ' +
        '</td>' +
        '<td>' +
        '<input type="radio" name="principal_diagnosis" value="' + disorderId + '" ' + checked_principal + '/>' +
        '</td>' +
        '<td>' +
        '<a href="#" class="removeDiagnosis" rel="' + disorderId + '"><i class="oe-i trash"></i></a>' +
        '</td>' +
        '</tr>';

    $('.js-diagnoses').append(row);
    OphCiExamination_RefreshCommonOphDiagnoses();
    //Adding new element to array doesn't trigger change so do it manually
    $(":input[name^='diabetic_diagnoses']").trigger('change');
    $(":input[name^='glaucoma_diagnoses']").trigger('change');
}

function OphCiExamination_Gonioscopy_Eyedraw_Controller(drawing) {
    this.notificationHandler = function(message) {
        switch (message.eventName) {
            case 'ready':
            case 'doodlesLoaded':
                OphCiExamination_Gonioscopy_switch_mode(drawing.canvas, drawing.firstDoodleOfClass('Gonioscopy').getParameter('mode'));
                break;
            case 'doodleAdded':
                {
                    let angleGradeNorthDoodle = drawing.firstDoodleOfClass('AngleGradeNorth');
                    let newDoodle = message.object;
                    if (angleGradeNorthDoodle && newDoodle.className === 'AntSynech') {
                        newDoodle.setParameterFromString('colour', angleGradeNorthDoodle.colour, true);
                    }
                }
                break;
            case 'parameterChanged':
                {
                    if (message.object.doodle.className == 'Gonioscopy' && message.object.parameter == 'mode') {
                        OphCiExamination_Gonioscopy_switch_mode(drawing.canvas, message.object.value);
                    }

                    let doodlesToSyncInGonioscopy = [
                        "AngleGradeNorth",
                        "AngleGradeEast",
                        "AngleGradeSouth",
                        "AngleGradeWest",
                        "AntSynech",
                    ];

                    let doodleChanged = message.object.doodle;
                    let doodleChangedContainsIris = false;
                    for (let i = 0; i < doodlesToSyncInGonioscopy.length; ++i) {
                        if (doodleChanged.className === doodlesToSyncInGonioscopy[i]) {
                            doodleChangedContainsIris = true;
                            break;
                        }
                    }

                    if (doodleChangedContainsIris && message.object.parameter === 'colour') {
                        doodlesToSyncInGonioscopy.forEach(function(doodleName) {
                            let doodlesToUpdate = drawing.allDoodlesOfClass(doodleName);
                            doodlesToUpdate.forEach(function(doodleToUpdate) {
                                if (doodleToUpdate.colour != doodleChanged.colour) {
                                    doodleToUpdate.setParameterFromString('colour', doodleChanged.colour, true);
                                }
                            });
                        });
                        drawing.repaint();
                    }
                    break;
                }
            case 'reset':
            case 'resetEdit':
                $(drawing.canvasParent).closest('.ed-body').find('select.gonioExpert').val(2).trigger('change');
                break;
        }
    };
    drawing.registerForNotifications(this);

    const anteriorSegmentCanvas = $(".OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment").
    find("[data-side='" + (drawing.eye === 1 ? "left" : "right") + "']").
    find('canvas');
    if (anteriorSegmentCanvas) {
        let anteriorSegmentController = anteriorSegmentCanvas.data('controller');
        if (anteriorSegmentController) {
            anteriorSegmentController.setGonioscopyDrawing(drawing);
        }
    }
}

function OphCiExamination_Gonioscopy_init() {
    ED.Checker.onAllReady(function() {
        $('.gonioscopy-mode').each(function(_, element) {
            OphCiExamination_Gonioscopy_update(element);
            $(this).change(function(_) {
                OphCiExamination_Gonioscopy_update(element);
            });
        });
    });

}

function OphCiExamination_Gonioscopy_update(field) {
    var fields = $(field).closest('.eyedraw-fields'),
        expert = fields.find('.expert-mode'),
        basic = fields.find('.basic-mode'),
        isExpert = fields.find('.gonioscopy-mode').val() === "Expert";
    if (isExpert) {
        expert.show();
        basic.hide();
    } else {
        expert.hide();
        basic.show();
    }
}

function OphCiExamination_Gonioscopy_switch_mode(canvas, mode) {
    var body = $(canvas).closest('.ed-body');
    var expert = body.find('.expert-mode');
    var basic = body.find('.basic-mode');

    if (mode == 'Expert') {
        expert.show();
        basic.hide();
    } else {
        expert.hide();
        basic.show();
    }
}

function OphCiExamination_RefreshCommonOphDiagnoses() {}

function OphCiExamination_AddAllergy() {
    var other_name;
    var allergy_id = $('#allergy_id').val();
    if (allergy_id > 0) {
        var comments = $('#comments').val();
        if ($('#allergy_id').find(':selected').text() == 'Other') {
            other_name = $('#other_allergy').val();
        }

        if ($('#allergy_id').find(':selected').text() == 'Other' && other_name == '') {
            alert("Please specify other allergy name!");
        } else {

            row = '<tr data-allergy-name="' + $('#allergy_id').find(':selected').text() + '" data-allergy-id="' + allergy_id + '"><td>';
            if (other_name !== undefined) {
                row += other_name;
            } else {
                row += $('#allergy_id').find(':selected').text();
            }
            row += '<input type="hidden" name="selected_allergies[]" value="' + allergy_id + '">';
            row += '<input type="hidden" name="allergy_comments[]" value="' + comments + '">';
            row += '<input type="hidden" name="other_names[]" value="' + other_name + '">';
            row += '</td><td>' + comments + '</td><td><a href="#" class="small removeAllergy">Remove</a></td></tr>';
            $('#OphCiExamination_allergy').append(row);
            $('.allergies_confirm_no').slideUp('fast');
            $('#no_allergies').prop('checked', false);
            $('#comments').val('');
            $('#other_allergy').val('');
            $('#allergy_other').slideUp('fast'); //close the div
            if ($('#allergy_id').find(':selected').text() != 'Other') {
                $('#allergy_id').find('option:selected').remove();
            }
            $('#allergy_id').val('');
        }
    } else {
        alert("Please select an option from the allergies!");
    }
}

function removeAllergyFromSelect(allergy_id, allergy_name) {
    if (allergy_name != 'Other') {
        $('#allergy_id').find("option[value='" + allergy_id + "']").remove();
    }
}

function OphCiExamination_ToggleSafeguardingPaediatricFields(show_fields) {
    let $fields = $('.js-safeguarding-paediatric-field');
    let $rows = $('tr.js-safeguarding-paediatric-row');
    let $clear_fields_input = $('input#clear_safeguarding_paediatric_fields');

    if (show_fields) {
        $rows.show();
        $fields.prop('disabled', false);
        $clear_fields_input.val("0");
    } else {
        $rows.hide();
        $fields.prop('disabled', true);
        $clear_fields_input.val("1");
    }
}

var eyedraw_added_diagnoses = [];

$(document).ready(function() {
    autosize($('textarea'));
});

/*
 * If any text is entered into the Comments field, then "No Abnormality" is removed from the automatic report.
 */

$(document).on("keyup", ".eyedraw-fields textarea[id$='_description'], .eyedraw-fields textarea[id$='_comments']", function(event) {
    var $textarea = $(event.target);
    var $report_input = $("#" + $textarea.attr("id").replace(/(_description|_comments)$/, "_ed_report"));
    var $report_html = $("#" + $textarea.attr("id").replace(/(_description|_comments)$/, "_ed_report_display"));

    var report_text = $report_input.val();

    if (report_text !== '' && report_text !== "No abnormality") {
        return;
    }

    var txt = $textarea.val();

    if (txt !== '') {
        $report_input.val("");
        $report_html.text("");
    } else {
        // Get eyedraw report

        var element;
        element = $(this).closest('.sub-element');
        if (element.length === 0) {
            element = $(this).closest('.element');
        }

        var side = null;
        if ($(this).closest('[data-side]').length) {
            side = $(this).closest('[data-side]').attr('data-side');
        }

        var eyedraw = element.attr('data-element-type-id');
        if (side) {
            eyedraw = side + '_' + eyedraw;
        }
        eyedraw = ED.getInstance('ed_drawing_edit_' + eyedraw);

        var text = eyedraw.report();
        text = text.replace(/, +$/, '');

        $report_input.val(text);
        $report_html.text(text);
    }
});

function registerElementController(controller, name, bindTo) {
    window[name] = controller;
    if (typeof window[bindTo] !== 'undefined') {
        window[bindTo].bindController(controller, name);
        controller.bindController(window[bindTo], bindTo);
        window[bindTo].options.onControllerBound(controller, name);
        controller.options.onControllerBound(window[bindTo], bindTo);
    }
}

function unregisterElementController(controller, name, binded_controller) {
    controller.unbindController(window[binded_controller], binded_controller);
    window[binded_controller].unbindController(controller, name);
    delete window[name];
}

/*
 * @params weight kg
 * @params height meters
 */
function bmi_calculator(weight, height) {
    height_meter = height / 100;
    result = weight / (height_meter * height_meter);
    return result;

}

function decimal2heximal(decimal) {
    return ('0' + decimal.toString(16)).substr(-2);
}

function generateId(length) {
    let array = new Uint8Array((length || 10) / 2);
    window.crypto.getRandomValues(array);
    return Array.from(array, decimal2heximal).join('')
}