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
                }
                else {
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

//returns the number of weeks booking recommendation from the DR grades (based on nsc retinopathy value for the given side)
function getDRBookingVal(side) {
    var dr_grade = $('.' + dr_grade_et_class);
    var booking = null;

    var val = dr_grade.find('select#'+dr_grade_et_class+'_'+side+'_nscretinopathy_id').val();
    $('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscretinopathy_id').find('option').each(function() {
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
    if (weeks){
        $('.Element_OphCiExamination_LaserManagement').find('#'+side+'_laser_booking_hint').text('Laser treatment needs to be booked within ' + weeks.toString() + ' weeks').show();
    }
    else {
        $('.Element_OphCiExamination_LaserManagement').find('#'+side+'_laser_booking_hint').text('').hide();
    }
}

function updateDRGrades(_drawing, retinopathy, maculopathy, ret_photo, mac_photo, clinicalret, clinicalmac) {
    if (_drawing.eye) {
        var side = 'left';
    }
    else {
        var side = 'right';
    }

    var dr_grade = $('.js-active-elements .' + OE_MODEL_PREFIX + dr_grade_et_class);
    // clinical retinopathy
    var crSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalret_id');
    crSel.find('option').each(function() {
        if ($(this).attr('data-code') == clinicalret) {
            crSel.val($(this).val());
            crSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            return false;
        }
    });

    // description
    dr_grade.find('div .'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalret_desc').hide();
    dr_grade.find('div#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalret_desc_' + clinicalret.replace(/\s+/g, '')).show();

    // clinical maculopathy
    var cmSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalmac_id');
    cmSel.find('option').each(function() {
        if ($(this).attr('data-code') == clinicalmac) {
            cmSel.val($(this).val());
            cmSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            return false;
        }
    });

    // description
    dr_grade.find('div .'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalmac_desc').hide();
    dr_grade.find('div#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalmac_desc_' + clinicalmac.replace(/\s+/g, '')).show();

    // Retinopathy
    var retSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscretinopathy_id');
    retSel.find('option').each(function() {
        if ($(this).attr('data-code') == retinopathy) {
            retSel.val($(this).val());
            retSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            return false;
        }
    });

    ret_photo_id = OE_MODEL_PREFIX + dr_grade_et_class+'_'+side+'_nscretinopathy_photocoagulation_';
    if (ret_photo) {
        dr_grade.find('input#' + ret_photo_id + '1').attr('checked', 'checked');
    }
    else {
        dr_grade.find('input#' + ret_photo_id + '0').attr('checked', 'checked');
    }

    // display description
    dr_grade.find('div .'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscretinopathy_desc').hide();
    dr_grade.find('div#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscretinopathy_desc_' + retinopathy).show();

    // Maculopathy
    var macSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscmaculopathy_id');
    macSel.find('option').each(function() {
        if ($(this).attr('data-code') == maculopathy) {
            macSel.closest('.wrapper').attr('class', 'wrapper field-highlight inline ' + $(this).attr('class'));
            macSel.val($(this).val());
            return false;
        }
    });

    mac_photo_id = OE_MODEL_PREFIX + dr_grade_et_class+'_'+side+'_nscmaculopathy_photocoagulation_';
    if (mac_photo) {
        dr_grade.find('input#' + mac_photo_id + '1').attr('checked', 'checked');
    }
    else {
        dr_grade.find('input#' + mac_photo_id + '0').attr('checked', 'checked');
    }

    // display description
    dr_grade.find('div .'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscmaculopathy_desc').hide();
    dr_grade.find('div#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscmaculopathy_desc_' + maculopathy).show();

    updateBookingWeeks(side);
}

function posteriorListener(_drawing) {
    this.drawing = _drawing;
    var side = 'right';
    if (this.drawing.eye) {
        side = 'left';
    }
    this.side = side;

    this.drawing.registerForNotifications(this, 'callBack', ['doodleAdded', 'doodleDeleted', 'parameterChanged']);

    this.callBack = function (_messageArray) {
        OphCiExamination_DRGrading_update(side);
    }
}

/**
 * Listener function for Anterior Segment to make the Traby Flap doodle deletable
 *
 * @param _drawing
 */
function anteriorListener(_drawing) {
    this.drawing = _drawing;

    this.drawing.registerForNotifications(this, 'callBack', ['doodleAdded']);

    this.callBack = function (_messageArray) {
        var obj = _messageArray.object;
        if (obj.className == 'TrabyFlap' || obj.className == 'Tube') {
                obj.isDeletable = true;
                this.drawing.selectDoodle(obj);
        }
    }
}

$(document).ready(function() {
    if(!$('#OphCiExamination_allergy').find('tr').length) {
      $('.allergies_confirm_no').show();
    }

    /**
     * Save event
     */
    handleButton($('#et_save'));

    handleButton($('#et_print'),function(e) {
        printEvent(null);
        e.preventDefault();
    });

    /**
     * Delete event
     */
    handleButton($('#et_deleteevent'));

    /**
     * Cancel event delete
     */
    handleButton($('#et_canceldelete'));


    $('#pcr-risk-info').tooltip(
        {show: {
            effect: "slideDown",
            delay: 250}
        }
    );

    // because these elements will be loaded later with AJAX we need to use live()
    $('.btn_save_allergy').live('click',OphCiExamination_AddAllergy);
    $('#allergy_id').live('change', function () {
        if ($(this).find(':selected').text() == 'Other') {
            $('#allergy_other').slideDown('fast');
        } else {
            $('#allergy_other').slideUp('fast');
        }
    });

    $('.removeAllergy').live('click',function() {
        var row = $(this).parent().parent();
        var allergy_id = row.data('allergy-id');
        var allergy_name = row.data('allergy-name');
        if(allergy_name != 'Other') {
            $('#allergy_id').append('<option value="' + allergy_id + '">' + allergy_name + '</option>');
        }
        if( row.data('assignment-id') !== undefined) {
            $('#OphCiExamination_allergy').append('<input type="hidden" name="deleted_allergies[]" value="' + row.data('assignment-id') + '">');
        }
        row.remove(); // we remove the <tr>
        if($('#OphCiExamination_allergy tr').length == 0 ){
            $('.allergies_confirm_no').slideDown('fast');
        }
    });

    $(this).delegate('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk_risk_id', 'change', function(e) {
        // Update Clinic Outcome follow up
        var clinic_outcome_element = $('.js-active-elements .'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome');
        if(clinic_outcome_element.length) {
            var template_id = $('option:selected', this).attr('data-clinicoutcome-template-id');
            OphCiExamination_ClinicOutcome_LoadTemplate(template_id);
        }

        // Change colour of dropdown background
        if (!$('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk .risk').hasClass($('option:selected', this).attr('class'))) {
            $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk .risk').removeClass('low');
            $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk .risk').removeClass('moderate');
            $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk .risk').removeClass('high');
            $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk .risk').addClass($('option:selected', this).attr('class'));
        }
    });
    $(this).delegate('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk a.descriptions_link', 'click', function(e) {
        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk_descriptions').dialog('open');
        e.preventDefault();
    });
    $('body').delegate('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk_descriptions a', 'click', function(e) {
        var value = $(this).attr('data-risk-id');
        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk_descriptions').dialog('close');
        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk_risk_id').val(value).trigger('change');
        e.preventDefault();
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
                description.val(description.val().replace(new RegExp(OphCiExamination_reports[element.data('element-type-id')]),text));
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
                    list_eye_id = $('input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_Diagnoses[eye_id_'+id+']"]:checked').val();
                    existing_id = id;
                }
            });

            var eye_id = side == 'right' ? 2 : 1;

            if (already_in_list) {
                if (eye_id != list_eye_id) {
                    $('input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_Diagnoses[eye_id_'+existing_id+']"][value="3"]').attr('checked','checked');
                }
            } else {
                $.ajax({
                    'type': 'GET',
                    'url': baseUrl+'/OphCiExamination/default/getDisorder?disorder_id='+code[i],
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
            var element_or_side = $(this).closest('.side');
        } else {
            var element_or_side = element;
        }
        $('.clearWithEyedraw',element_or_side).each(function() {
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
            $('a.removeDiagnosis[rel="'+eyedraw_added_diagnoses[i]+'"]').click();
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
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_right_clinicalret_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_left_clinicalret_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_right_clinicalmac_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_left_clinicalmac_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_right_nscretinopathy_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_left_nscretinopathy_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_right_nscmaculopathy_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_left_nscmaculopathy_id'
            , 'change', function(e) {

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
        var desc = id.substr(0,id.length-2) + 'desc';
        drGradeEl.find('.'+desc).hide();
        drGradeEl.find('#'+desc + '_' + gradeCode).show();
        if ($('.js-active-elements .'+OE_MODEL_PREFIX+'Element_OphCiExamination_PosteriorPole').length) {
            $('#drgrading_dirty').show();
        }

        $(this).closest('.wrapper').removeClass('high severe high-risk proliferative maculopathy moderate pre-prolif mild early background peripheral ungradable low none');
        $(this).closest('.wrapper').addClass($('option:selected', this).attr('class'));

        updateBookingWeeks(side);
    })

    $('body').delegate('.grade-info-all a', 'click', function(e) {
        var value = $(this).data('id');
        var select_id = $(this).parents('.grade-info-all').data('select-id');
        $(this).parents('.grade-info-all').dialog('close');
        $('#'+select_id).val(value).trigger('change');
        e.preventDefault();
    });

    $(this).delegate('input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading[right_nscretinopathy_photocoagulation]"], ' +
        'input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading[left_nscretinopathy_photocoagulation]"], ' +
        'input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading[right_nscmaculopathy_photocoagulation]"], ' +
        'input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading[left_nscmaculopathy_photocoagulation]"]'
            , 'change', function(e) {
                if ($('.js-active-elements .Element_OphCiExamination_PosteriorPole').length) {
                    $('#drgrading_dirty').show();
                }
    });

    $(this).delegate('a#drgrading_dirty', 'click', function(e) {
        $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_PosteriorPole').find('canvas').each(function() {
            var drawingName = $(this).attr('data-drawing-name');
            var drawing = ED.getInstance(drawingName);
            if (drawing) {
                // the posterior segment drawing is available to sync values with
                var grades = gradeCalculator(drawing);

                updateDRGrades(drawing, grades[0], grades[1], grades[2], grades[3], grades[4], grades[5]);
            }
        });
        $(this).hide();
        e.preventDefault();
    });

    // When VA updated we may need to update the DR Grade
    $(this).delegate('.va-selector', 'change', function(e) {
        side = getSplitElementSide($(this));

        OphCiExamination_DRGrading_update(side);
    });

    // end of DR

    // management
    function isDeferralOther(element, name) {
        var reasonPK = $('#'+element+'_'+name+'_deferralreason_id').val();
        var other = false;

        $('#'+element+'_'+name+'_deferralreason_id').find('option').each(function() {
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
        $('#div_'+element+'_'+name+'_deferralreason_other').slideDown().find('textarea').each(function(e) {
            if ($(this).data('stored-value')) {
                // must've changed their mind, restore the value
                $(this).val($(this).data('stored-value'));
            }
            $(this).autosize();

        });
    }

    function hideDeferralOther(element, name) {
        if ($('#div_'+element+'_'+name+'_deferralreason_other').is(':visible')) {
            // because of the value storage, only want to do this if its showing
            $('#div_'+element+'_'+name+'_deferralreason_other').slideUp().find('textarea').each(function(e) {
                // clear text value to prevent submission, but store to make available if user changes their mind
                $(this).data('stored-value', $(this).val());
                $(this).val('');
            });
        }
    }

    // abstracted to manage the deferral fields for laser/injection
    function deferralFields(element, name) {
        var thePK = $('#'+element+'_'+name+'_status_id').val();
        // flag for deferred fields
        var deferred = false;
        // flag for booking hint
        var book = false;
        // flag for event creation hint
        var event = false;

        $('#'+element+'_'+name+'_status_id').find('option').each(function() {
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
            if ($('.'+element).find('#'+name+'_booking_hint').contents().length) {
                unmaskFields($('.'+element).find('#'+name+'_booking_hint'));
            }
        }
        else {
            maskFields($('.'+element).find('#'+name+'_booking_hint'));
        }

        if (event) {
            unmaskFields($('.'+element).find('#'+name+'_event_hint'));
        }
        else {
            maskFields($('.'+element).find('#'+name+'_event_hint'));
        }


        if (deferred) {
            $('#div_'+element+'_'+name+'_deferralreason').slideDown();
            if ($('#'+element+'_'+name+'_deferralreason_id').data('stored-value')) {
                $('#'+element+'_'+name+'_deferralreason_id').val(
                    $('#'+element+'_'+name+'_deferralreason_id').data('stored-value')
                );
                if (isDeferralOther(name)) {
                    showDeferralOther(name);
                }
            }
        }
        else {

            $('#div_'+element+'_'+name+'_deferralreason').slideUp();
            if ($('#'+element+'_'+name+'_deferralreason_id').val()) {
                $('#'+element+'_'+name+'_deferralreason_id').data('stored-value', $('#'+element+'_'+name+'_deferralreason_id').val());
                $('#'+element+'_'+name+'_deferralreason_id').val('');
                // call the hide on other in case it's currently showing
                hideDeferralOther(name);
            }
        }
    }

    // show/hide the laser deferral fields
    $(this).delegate('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement_left_laser_status_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement_right_laser_status_id', 'change', function(e) {
        var side = getSplitElementSide($(this));
        deferralFields(OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement', side + '_laser');
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
            unmaskFields($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement_'+side+'_treatment_fields'));
        }
        else {
            maskFields($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement_'+side+'_treatment_fields'));
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
            $(this).parents('.side').find('.lasertype_other').removeClass('hidden');
        }
        else {
            $(this).parents('.side').find('.lasertype_other').addClass('hidden');
        }
    });

    // show/hide the injection deferral fields
    $(this).delegate('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagement_injection_status_id', 'change', function(e) {
        deferralFields(OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagement', 'injection');
    });

    // show/hide the deferral reason option
    $(this).delegate('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement_left_laser_deferralreason_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement_right_laser_deferralreason_id', 'change', function(e) {
        var side = getSplitElementSide($(this));
        var other = isDeferralOther(OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement', side + '_laser');

        if (other) {
            showDeferralOther(OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement', side + '_laser');
        }
        else {
            hideDeferralOther(OE_MODEL_PREFIX+'Element_OphCiExamination_LaserManagement', side + '_laser');
        }
    });

    // show/hide the deferral reason option
    $(this).delegate('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagement_injection_deferralreason_id', 'change', function(e) {
        var other = isDeferralOther(''+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagement', 'injection');

        if (other) {
            showDeferralOther(OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagement', 'injection');
        }
        else {
            hideDeferralOther(OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagement', 'injection');
        }
    });


    // end of management

    // investigation

    // OCT

    $('.event.ophciexamination').delegate('input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_OCT[right_dry]"], ' +
        'input[name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_OCT[left_dry]"]'
        , 'change', function(e) {
            // need to check the value - if it's 0 we should the fluid for the side. otherwise hide it.
            var side = getSplitElementSide($(this));
            if ($(this)[0].value == '0') {
                unmaskFields($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_OCT_' + side + '_fluid_fields'),null);
            }
            else {
                maskFields($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_OCT_' + side + '_fluid_fields'),null);
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

    $(this).delegate('#event-content .' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Refraction .refractionType', 'change', function() {
        OphCiExamination_Refraction_updateType(this);
    });

    $(this).delegate('#event-content .' + OE_MODEL_PREFIX + 'Element_OphCiExamination_Gonioscopy .gonioscopy-mode', 'change', function() {
        OphCiExamination_Gonioscopy_update(this);
    });

    $('#event-content').delegate('.element .segmented select', 'change', function() {
        var field = $(this).nextAll('input');
        var containerEL = $(this).parent();
        OphCiExamination_Refraction_updateSegmentedField(field , containerEL);
    });

    function visualAcuityChange(target, near) {
        var suffix = 'VisualAcuity';
        if(near === 'near'){
            suffix = 'NearVisualAcuity';
        }
        removeElement($(target).closest('.sub-element[data-element-type-class="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix+'"]'), true);
        var el = $('.event-content').find('ul.sub-elements-list li[data-element-type-class="' + OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix+'"]');
        if (el.length) {
            el.addClass('clicked');
            addElement(el, true, true, false, {unit_id: $(target).val()});
        } else {
            // use a different selector
            var sidebar = $('aside.episodes-and-events').data('patient-sidebar');
            if (sidebar) {
                sidebar.addElementByTypeClass(OE_MODEL_PREFIX + 'Element_OphCiExamination_'+suffix, {unit_id: $(target).val()});
            } else {
                console.log('Cannot find sidebar to manipulate elements for VA change');
            }

        }

    }

    $(this).delegate('#nearvisualacuity_unit_change', 'change', function(e) {
        visualAcuityChange(this, 'near');
    });

    $(this).delegate('#visualacuity_unit_change', 'change', function(e) {
        visualAcuityChange(this, '');
    });

    $(this).delegate(
        '.'+OE_MODEL_PREFIX+'Element_OphCiExamination_VisualAcuity .removeReading, .'+OE_MODEL_PREFIX+'Element_OphCiExamination_NearVisualAcuity .removeReading',
        'click', function(e) {
        var activeForm = $(this).closest('.active-form');

        $(this).closest('tr').remove();
        if ($('tbody', activeForm).children('tr').length == 0) {
            $('.noReadings', activeForm).show();
            $('table', activeForm).hide();
        }
        else {
            // VA can affect DR
            var side = getSplitElementSide($(this));
            OphCiExamination_DRGrading_update(side);
        }
        e.preventDefault();
    });

    $(this).delegate('.addReading, .addNearReading', 'click', function(e) {
        var side = $(this).closest('.side').attr('data-side');
        if($(this).hasClass('addNearReading')){
            OphCiExamination_NearVisualAcuity_addReading(side);
        } else {
            OphCiExamination_VisualAcuity_addReading(side);
        }
        // VA can affect DR
        OphCiExamination_DRGrading_update(side);
        e.preventDefault();
    });

    $(this).delegate('a.foster_images_link', 'click', function(e) {
        var side = $(this).closest('[data-side]').attr('data-side');
        $('.foster_images_dialog[data-side="'+side+'"]').dialog('open');
        e.preventDefault();
    });
    $('body').delegate('.foster_images_dialog area', 'click', function() {
        var value = $(this).attr('data-vh');
        var side = $(this).closest('[data-side]').attr('data-side');
        $('.foster_images_dialog[data-side="'+side+'"]').dialog('close');
        $('#OEModule_OphCiExamination_models_Element_OphCiExamination_Gonioscopy_'+side+'_van_herick_id option').attr('selected', function () {
            return ($(this).text() == value + '%');
        });
    });

    /**
     * Update gonioExpert when gonioBasic is changed (gonioBasic controls are not stored in DB)
     */
    $('body').delegate('.gonioBasic', 'change', function(e) {
        var position = $(this).attr('data-position');
        var expert = $(this).closest('.side').find('.gonioExpert[data-position="'+position+'"]');
        if($(this).val() == 0) {
            $('option',expert).attr('selected', function () {
                return ($(this).attr('data-value') == '1');
            });
        } else {
            $('option',expert).attr('selected', function () {
                return ($(this).attr('data-value') == '3');
            });
        }
        e.preventDefault();
    });

    /**
     * colour vision behaviours
     */
    $(this).delegate('.colourvision_method', 'change', function(e) {
        var side = $(this).closest('.side').attr('data-side');
        OphCiExamination_ColourVision_addReading(this, side);
        e.preventDefault();
    });

    $(this).delegate('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_ColourVision .removeCVReading', 'click', function(e) {
        var wrapper = $(this).closest('.side');
        var side = wrapper.attr('data-side');
        var row = $(this).closest('tr');
        var id = $('.methodId', row).val();
        var name = $('.methodName', row).text();
        row.remove();
        var method_select = wrapper.find('.colourvision_method');
        method_select.append('<option value="'+id+'">'+name+'</option>');
        sort_selectbox(method_select);

        // No readings
        if ($('.colourvision_table tbody tr', wrapper).length == 0) {
            // Hide vision table
            $('.colourvision_table', wrapper).hide();
            // Hide clear button
            $(wrapper).find('.clearCV').addClass('hidden');
        }
        e.preventDefault();
    });

    $(this).delegate('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_ColourVision .clearCV', 'click', function(e) {
        var side = $(this).closest('.side').attr('data-side');
        $(this).closest('.side').find('tr.colourvisionReading a.removeCVReading').click();
        $(this).addClass('hidden');
        e.preventDefault();
    });

    // clinic outcome functions
    function isClinicOutcomeStatusFollowup() {
        var statusPK = $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_status_id').val();
        var followup = false;

        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_status_id').find('option').each(function() {
            if ($(this).attr('value') == statusPK) {
                if ($(this).attr('data-followup') == "1") {
                    followup = true;
                    return false;
                }
            }
        });

        return followup;
    }

    function isClinicOutcomeStatusPatientTicket() {
        var statusPK = $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_status_id').val();
        var patientticket = false;

        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_status_id').find('option').each(function() {
            if ($(this).attr('value') == statusPK) {
                if ($(this).attr('data-ticket') == "1") {
                    patientticket = true;
                    return false;
                }
            }
        });

        return patientticket;
    }

    function showOutcomeStatusFollowup() {
        // Retrieve any previously stashed values
        if ($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_quantity').data('store-value')) {
            $('#Element_OphCiExamination_ClinicOutcome_followup_quantity').val($('#Element_OphCiExamination_ClinicOutcome_followup_quantity').data('store-value'));
        }
        if ($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_period_id').data('store-value')) {
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_period_id').val($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_period_id').data('store-value'));
        }
        if ($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_id').data('store-value')) {
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_id').val($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_id').data('store-value'));
        }
        if ($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_comments').data('store-value')) {
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_comments').val($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_comments').data('store-value'));
        }

        $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup').slideDown();
        $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role').slideDown();

    }

    function hideOutcomeStatusFollowup() {
        if ($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup').is(':visible')) {
            // only do hiding and storing if currently showing something.
            $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role').slideUp();
            $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup').slideUp();

            // Stash current values as data in case we need them again and to avoid saving them
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_id').data('store-value', $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_id').val());
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_id').val('');
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_comments').data('store-value', $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_comments').val());
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_role_comments').val('');
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_quantity').data('store-value', $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_quantity').val());
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_quantity').val('');
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_period_id').data('store-value', $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_period_id').val());
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_period_id').val('');
        }
    }

    function showOutcomeStatusPatientTicket() {
        $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_patientticket').slideDown();
    }

    function hideOutcomeStatusPatientTicket() {
        $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_patientticket').slideUp();
    }

    // show/hide the followup period fields
    $(this).delegate('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_status_id', 'change', function(e) {
        var followup = isClinicOutcomeStatusFollowup();
        if (followup) {
            showOutcomeStatusFollowup();
        }
        else {
            hideOutcomeStatusFollowup();
        }
        var referral = isClinicOutcomeStatusPatientTicket();
        if (referral) {
            showOutcomeStatusPatientTicket();
        }
        else {
            hideOutcomeStatusPatientTicket();
        }
    });

    $(this).on('change', '#patientticket_queue', function(e) {
        var id = $(e.target).val(),
            placeholder = $('#queue-assignment-placeholder'),
            loader = $('.OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome .loader');
        placeholder.html('');
        if (id) {
            loader.show();
            $.ajax({
                url: $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_patientticket').data('queue-ass-form-uri') + id,
                data: {label_width: 3, data_width: 5},
                success: function(response) {
                    placeholder.html(response);
                },
                error: function(jqXHR, status, error) {
                    enableButtons();
                    throw new Error("Unable to retrieve assignment form for queue with id " + id + ": " + error);
                },
                complete: function() {
                    loader.hide();
                }
            });
        }
    });
    // end of clinic outcome functions


    // perform the inits for the elements
    $('.js-active-elements .element,.js-active-elements .sub-element').each(function() {
        var initFunctionName = $(this).attr('data-element-type-class').replace(OE_MODEL_PREFIX + 'Element_', '') + '_init';
        if(typeof(window[initFunctionName]) == 'function') {
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
        if(element_class == 'OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings') {
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

        $('.js-diagnoses').find('input[type="hidden"]').map(function() {
            if ($(this).val() == disorder_id) {
                $(this).remove();
            }
        });

        $(this).parent().parent().remove();

        if (new_principal) {
            $('input[name="principal_diagnosis"]:first').attr('checked','checked');
        }

        OphCiExamination_RefreshCommonOphDiagnoses();

        return false;
    });

    OphCiExamination_GetCurrentConditions();

    $('.signField').die('change').live('change',function(e) {
        var sign_id = $(this).val() >0 ? 1 : 2;
        var type = $(this).data('type');
        var value = $(this).next('select').val();

        $(this).next('select').html('');

        var list = window['Element_OphCiExamination_Refraction_' + type][sign_id];

        for (var i in list) {
            $(this).next('select').append('<option value="' + list[i] + '">' + list[i] + '</option>');
        }

        $(this).next('select').val(value).change();
    });

        /** Post Operative Complication  Event Bindings **/

        $('#event-content').on('change', '#OphCiExamination_postop_complication_operation_note_id-select', function(){

            var element_id = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_PostOpComplications_id').val();
            var operation_note_id = $(this).val();
            var element_string = "";

            if(element_id !== ""){
                element_string = '/element_id/' + element_id;
            }

            $.getJSON( baseUrl + '/OphCiExamination/default/getPostOpComplicationList' + element_string + '/operation_note_id/' + operation_note_id, function( data ) {

                var $right_table = $('#right-complication-list');
                var $left_table = $('#left-complication-list');

                $('#right-complication-list tr, #left-complication-list tr').remove();

                $.each( data.right_values, function( key, val ) {
                    addPostOpComplicationTr(val.name, 'right-complication-list', val.id, val.display_order);

                });
                $.each( data.left_values, function( key, val ) {
                    addPostOpComplicationTr(val.name, 'left-complication-list', val.id, val.display_order);
                });

                setPostOpComplicationTableText();

                $('#left-complication-select option').remove();
                $('#right-complication-select option').remove();

                $('#right-complication-select').append( $('<option>').text("-- Select --") );
                $.each( data.right_select, function( key, val ) {
                    $('#right-complication-select').append( $('<option>', {value: val.id, 'data-display_order':val.display_order}).text(val.name) );
                });

                $('#left-complication-select').append( $('<option>').text("-- Select --") );
                $.each( data.left_select, function( key, val ) {
                    $('#left-complication-select').append( $('<option>', {value: val.id, 'data-display_order':val.display_order}).text(val.name) );
                });

            });
        });

        $("#event-content").on('change', '#right-complication-select, #left-complication-select', function(){

            // https://bugs.jquery.com/ticket/9335
            // Chrome triggers "change" on .blur() if the value of the select has changed, so we do a blur before any changes
            $(this).blur();

            var table_id = $(this).attr('id').replace('select', 'list');
            var selected_text = $( '#' + $(this).attr('id') + " option:selected").text();
            var select_value = $(this).val();

            if(select_value >= 0){
                addPostOpComplicationTr(selected_text, table_id, select_value, $(this).find('option:selected').data('display_order')  );
                $(this).find('option:selected').remove();
                setPostOpComplicationTableText();
            }

        });

        $('#event-content').on('click','a.postop-complication-remove-btn', function(){

            var value = $(this).parent().find('input[type=hidden]').val();
            var text = $(this).closest('tr').find('.postop-complication-name').text();

            var select_id = $(this).closest('table').attr('id').replace('list', 'select');

            $select = $('#' + select_id);
            $select.append( $('<option>',{value: value}).text(text));

            $(this).closest('tr').remove();

            setPostOpComplicationTableText();

        });

        /** End of Post Operative Complication Event Bindings **/

        /* Visual Acuity readings event binding */

        $('#event-content').on('change', '.OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .va-selector', function(){

            var $section =  $(this).closest('section');
            var $cviAlert = $section.find('.cvi-alert');
            var threshold = parseInt($cviAlert.data('threshold'));

            if( $section.find('.cvi_alert_dismissed').val() !== "1"){
                var show_alert = null;
                $section.find('.va-selector').each(function(k,v){
                    var val = parseInt($(this).val());
                    if (val < threshold) {
                        show_alert = (show_alert === null) ? true : show_alert;
                    } else {
                        show_alert = false;
                    }
                    return;
                });

                if (show_alert) {
                    $cviAlert.slideDown(500);
                } else {
                    $cviAlert.slideUp(500);
                }
            }
        });

        // Dismiss alert box
        $('#event-content').on('click', '.OEModule_OphCiExamination_models_Element_OphCiExamination_VisualAcuity .dismiss_cva_alert', function(){
            var $section = $(this).closest('section');

            if( $('.ophciexamination.column.event.view').length ) {
                // viewing
                $.get( baseUrl + '/OphCiExamination/default/dismissCVIalert', { "element_id": $section.find('.element_id').val() }, function( result ) {
                    var response = $.parseJSON(result);
                    if(response.success === 'true'){
                        $section.find('.cvi-alert').slideUp(500).remove();
                    }
                });
            } else {
                // editing
                $section.find('.cvi-alert').slideUp(500);
                $section.find('.cvi_alert_dismissed').val("1");
            }
        });

        /* End of Visual Acuity readings event binding */
});

    /** Post Operative Complication function **/
     function setPostOpComplicationTableText()
    {
        var $right_table = $('#right-complication-list');
        var $left_table = $('#left-complication-list');

        var $active_form = $right_table.closest('.active-form');

        if( $right_table.find('tr').length === 0  ){
            $active_form.find('h5.recorded').hide();
            $active_form.find('h5.no-recorded').show();
        } else {
            $active_form.find('h5.recorded').show();
            $active_form.find('h5.no-recorded').hide();
        }

        $active_form = $left_table.closest('.active-form');
        if( $left_table.find('tr').length === 0  ){
            $active_form.find('h5.recorded').hide();
            $active_form.find('h5.no-recorded').show();
        } else {
            $active_form.find('h5.recorded').show();
            $active_form.find('h5.no-recorded').hide();
        }
    }

    function addPostOpComplicationTr(selected_text, table_id, select_value, display_order)
    {

        var $table = $('#' + table_id);

        var $tr = $('<tr>');
        var $td_name = $('<td>', {class: "postop-complication-name"}).text(selected_text);

        var $hidden_input = $("<input>", {
            type:"hidden",
            id:'complication_items_' + $table.data('sideletter') + '_' + $('#' + table_id + ' tr').length,
            name: 'complication_items[' + $table.data('sideletter') + '][' + $('#' + table_id + ' tr').length +']',
            value: select_value,
        });
        $hidden_input.data('display_order', display_order);

        var $td_action = $('<td>',{class:'right'}).html( "<a class='postop-complication-remove-btn' href='javascript:void(0)'>Remove</a>" );
        $td_action.append($hidden_input);

        $tr.append($td_name);
        $tr.append($td_action);
        $table.append( $tr );
    }

    /** End of Post Operative Complication function **/


function updateTextMacros() {
    var active_element_ids = [];
    $('.js-active-elements > .element, .js-active-elements .sub-elements.active > .sub-element').each(function() {
        active_element_ids.push($(this).attr('data-element-type-id'));
    });
    $('.js-active-elements .textMacro option').each(function() {
        if($(this).val() && $.inArray($(this).attr('data-element-type-id'), active_element_ids) == -1) {
            disableTextMacro(this);
        }
    });
    $('.js-active-elements .textMacro').each(function() {
        var sort = false;
        if($(this).data('disabled-options')) {
            var select = this;
            $(this).data('disabled-options').filter(function(option) {
                return $.inArray($(option).attr('data-element-type-id'), active_element_ids) != -1;
            }).forEach(function(option, index) {
                enableTextMacro(select, index, option);
                sort = true;
            });
        }
        if(sort) {
            var options = $('option', this);
            options.sort(function(a, b) {
                if(a.text > b.text) return 1;
                else if(a.text < b.text) return -1;
                else return 0;
            });
            $(this).empty().append(options);
        }
        if($('option', this).length > 1) {
            $(this).removeAttr('disabled');
        } else {
            $(this).attr('disabled', 'disabled');
        }
    });
}

function disableTextMacro(option) {
    var disabled_options = $(option).parent().data('disabled-options');
    if(!disabled_options) {
        disabled_options = [];
    }
    disabled_options.push(option);
    $(option).parent().data('disabled-options', disabled_options);
    $(option).remove();
}

function enableTextMacro(select, index, option) {
    var disabled_options = $(select).data('disabled-options');
    $(select).append(option);
    disabled_options.splice(index,1);
}

function OphCiExamination_ColourVision_getNextKey(side) {
    var keys = $('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_ColourVision [data-side="' + side +'"] .colourvisionReading').map(function(index, el) {
        return parseInt($(el).attr('data-key'));
    }).get();
    if(keys.length) {
        return Math.max.apply(null, keys) + 1;
    } else {
        return 0;
    }
}

function OphCiExamination_ColourVision_addReading(element, side) {
    var method_id = $('option:selected', element).val();
    if (method_id) {
        var method_name = $('option:selected', element).text();
        $('option:selected', element).remove();
        var template = $('#colourvision_reading_template').html();
        var method_values = '';
        if (colourVisionMethodValues[method_id]) {
            for (var id in colourVisionMethodValues[method_id]) {
                if (colourVisionMethodValues[method_id].hasOwnProperty(id)) {
                    method_values += '<option value="'+id+'">'+colourVisionMethodValues[method_id][id]+'</option>';
                }
            }
        }
        var data = {
            "key" : OphCiExamination_ColourVision_getNextKey(side),
            "side": side,
            "method_name": method_name,
            "method_id": method_id,
            "method_values": method_values
        };
        var form = Mustache.render(template, data);

        // Show clear button
        $('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_ColourVision [data-side="' + side +'"] .clearCV').removeClass("hidden");

        // Show table
        var table = $('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_ColourVision [data-side="' + side +'"] .colourvision_table');
        table.show();
        $('tbody', table).append(form);
    }
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

function OphCiExamination_Refraction_updateSegmentedField(field , containerEL) {
    var parts = $(field).parent().children('select');

    /*
    If error box exists, the parent-children structure breaks
     */
    if (typeof parts[0] === "undefined"){
        var parts = containerEL.children('select');
        var value = $(parts[0]).val() * (parseFloat($(parts[1]).val()) + parseFloat($(parts[2]).val()));

        if(isNaN(value)){
            $(field).val('');
        } else {
            containerEL.find('input').val(value.toFixed(2));
        }
    } else {
        var value = $(parts[0]).val() * (parseFloat($(parts[1]).val()) + parseFloat($(parts[2]).val()));
        if(isNaN(value)){
            $(field).val('');
        } else {
            $(field).val(value.toFixed(2));
        }
    }
}

/**
 * Show other type field only if type is set to "Other"
 */
function OphCiExamination_Refraction_updateType(field) {
    var other = $(field).closest('.refraction-type-container').find('.refraction-type-other');
    if ($(field).val() == '') {
        other.show();
        other.find('.refraction-type-other-field').focus();
    } else {
        other.find('.refraction-type-other-field').val('');
        other.hide();
    }
}

function OphCiExamination_OCT_init() {
    // history tool tip
    $(".Element_OphCiExamination_OCT").find('.sft-history').each(function(){
        var quick = $(this);
        var iconHover = $(this).parent().find('.sft-history-icon');

        iconHover.hover(function(e){
            var infoWrap = $('<div class="quicklook"></div>');
            infoWrap.appendTo('body');
            infoWrap.html(quick.html());

            var offsetPos = $(this).offset();
            var top = offsetPos.top;
            var left = offsetPos.left + 25;

            top = top - (infoWrap.height()/2) + 8;

            if (left + infoWrap.width() > 1150) left = left - infoWrap.width() - 40;
            infoWrap.css({'position': 'absolute', 'top': top + "px", 'left': left + "px"});
            infoWrap.fadeIn('fast');
        },function(e){
            $('body > div:last').remove();
        });
    });
}

/**
 * Visual Acuity
 */

function OphCiExamination_VisualAcuity_ReadingTooltip(row) {
    var iconHover = row.find('.va-info-icon:last');

    iconHover.hover(function(e) {
        var sel = $(this).parent().parent().find('select.va-selector');
        var val = sel.val();
        var tooltip_text = '';
        if (val) {
            var conversions = [];

            sel.find('option').each(function() {
                if ($(this).val() == val) {
                    conversions = $(this).data('tooltip');
                    return true;
                }
            });

            var approx = false;
            for (var i = 0; i < conversions.length; i++) {
                tooltip_text += conversions[i].name + ": " + conversions[i].value;
                if (conversions[i].approx) {
                    approx = true;
                    tooltip_text += '*';
                }
                tooltip_text += "<br />";
            }
            if (approx) {
                tooltip_text += "<i>* Approximate</i>";
            }
        }
        else {
            tooltip_text = 'Please select a VA value';
        }

        var infoWrap = $('<div class="quicklook">' + tooltip_text + '</div>');
        infoWrap.appendTo('body');
        var offsetPos = $(this).offset();
        var top = offsetPos.top;
        var left = offsetPos.left + 25;

        top = top - (infoWrap.height()/2) + 8;

        if (left + infoWrap.width() > 1150) left = left - infoWrap.width() - 40;
        infoWrap.css({'position': 'absolute', 'top': top + "px", 'left': left + "px"});
        infoWrap.fadeIn('fast');

    }, function(e) {
        $('body > div:last').remove();
    });
}

function OphCiExamination_VisualAcuity_getNextKey() {
    var keys = $('.visualAcuityReading').map(function(index, el) {
        return parseInt($(el).attr('data-key'));
    }).get();
    if(keys.length) {
        return Math.max.apply(null, keys) + 1;
    } else {
        return 0;
    }
}
function OphCiExamination_NearVisualAcuity_addReading(side){
    var template = $('#nearvisualacuity_reading_template').html();
    OphCiExamination_VisualAcuity_addReading(side, template, 'NearVisualAcuity')
}

function OphCiExamination_VisualAcuity_addReading(side, template, suffix) {
    if(typeof template === 'undefined'){
        template = $('#visualacuity_reading_template').html();
    }
    if(typeof suffix === 'undefined'){
        suffix = 'VisualAcuity';
    }
    var data = {
        "key" : OphCiExamination_VisualAcuity_getNextKey(),
        "side" : side
    };
    var form = Mustache.render(template, data);

    $('section[data-element-type-class="'+OE_MODEL_PREFIX+'Element_OphCiExamination_'+suffix+'"] .element-eye.'+side+'-eye .noReadings').hide().find('input:checkbox').each(function() {
        $(this).attr('checked', false);
    });
    var table = $('section[data-element-type-class="'+OE_MODEL_PREFIX+'Element_OphCiExamination_'+suffix+'"] .element-eye[data-side="'+side+'"] table.va_readings');
    table.show();
    var nextMethodId = OphCiExamination_VisualAcuity_getNextMethodId(side, suffix);
    $('tbody', table).append(form);
    $('.method_id', table).last().val(nextMethodId);

    OphCiExamination_VisualAcuity_ReadingTooltip(table.find('tr').last());

}

/**
 * Which method ID to preselect on newly added readings.
 * Returns the next unused ID.
 * @param side
 * @returns integer
 */
function OphCiExamination_VisualAcuity_getNextMethodId(side, suffix) {
    var method_ids = OphCiExamination_VisualAcuity_method_ids;
    $('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_'+suffix+' [data-side="' + side + '"] .method_id').each(function() {
        var method_id = $(this).val();
        method_ids = $.grep(method_ids, function(value) {
            return value != method_id;
        });
    });
    return method_ids[0];
}

function OphCiExamination_VisualAcuity_bestForSide(side) {
    var table = $('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_VisualAcuity [data-side="' + side + '"] table');
    if (table.is(':visible')) {
        var best = 0;
        table.find('tr .va-selector').each(function() {
            if (parseInt($(this).val()) > best) {
                best = parseInt($(this).val());
            }
        });
        return best;
    }
    return null;
}

function OphCiExamination_VisualAcuity_init() {
    // ensure tooltip works when loading for an edit
    $('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_VisualAcuity .side').each(function() {
        $(this).find('tr.visualAcuityReading').each(function() {
            OphCiExamination_VisualAcuity_ReadingTooltip($(this));
        });
    });
}

function OphCiExamination_NearVisualAcuity_init() {
    // ensure tooltip works when loading for an edit
    $('#event-content .'+OE_MODEL_PREFIX+'Element_OphCiExamination_NearVisualAcuity .side').each(function() {
        $(this).find('tr.nearvisualAcuityReading').each(function() {
            OphCiExamination_VisualAcuity_ReadingTooltip($(this));
        });
    });
}


// setup the dr grading fields (called once the Posterior Segment is fully loaded)
// will verify whether the form values match that of the loaded eyedraws, and if not, mark as dirty
function OphCiExamination_DRGrading_dirtyCheck(_drawing) {
    var dr_grade = $('.' + OE_MODEL_PREFIX+dr_grade_et_class);
    var grades = gradeCalculator(_drawing);
    if (grades === false)
      return;

    var retinopathy = grades[0],
        maculopathy = grades[1],
        ret_photo		= grades[2] ? '1' : '0',
        mac_photo		= grades[3] ? '1' : '0',
        clinicalret = grades[4],
        clinicalmac = grades[5],
        dirty				= false,
        side				= 'right';

    if (_drawing.eye) {
            side = 'left';
        }

    // clinical retinopathy
    var cSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalret_id');
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
    dr_grade.find('div .'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalret_desc').hide();
    dr_grade.find('div#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalret_desc_' + clinicalret.replace(/\s+/g, '')).show();

    // clinical maculopathy
    var cmSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalmac_id');
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
    dr_grade.find('div .'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalmac_desc').hide();
    dr_grade.find('div#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_clinicalmac_desc_' + clinicalmac.replace(/\s+/g, '')).show();

        //retinopathy
        var retSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscretinopathy_id');
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
        if ($('input[name="'+OE_MODEL_PREFIX + dr_grade_et_class+'\['+side+'_nscretinopathy_photocoagulation\]"]:checked').val() != ret_photo) {
            dirty = true;
        }

        // maculopathy photocoagulation
        if ($('input[name="'+OE_MODEL_PREFIX + dr_grade_et_class+'\['+side+'_nscmaculopathy_photocoagulation\]"]:checked').val() != mac_photo) {
            dirty = true;
        }

        // Maculopathy
        var macSel = dr_grade.find('select#'+OE_MODEL_PREFIX+dr_grade_et_class+'_'+side+'_nscmaculopathy_id');
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
        dr_grade.find('div .'+OE_MODEL_PREFIX + dr_grade_et_class+'_'+side+'_nscretinopathy_desc').hide();
        dr_grade.find('div#'+OE_MODEL_PREFIX + dr_grade_et_class+'_'+side+'_nscretinopathy_desc_' + retinopathy).show();

        dr_grade.find('div .'+OE_MODEL_PREFIX + dr_grade_et_class+'_'+side+'_nscmaculopathy_desc').hide();
        dr_grade.find('div#'+OE_MODEL_PREFIX + dr_grade_et_class+'_'+side+'_nscmaculopathy_desc_' + maculopathy).show();

        if (dirty) {
            $('#drgrading_dirty').show();
        }
    dr_grade.find('.side[data-side="'+side+'"]').removeClass('uninitialised');
}

/**
 * returns true if the dr side can be updated with calculated grades
 *
 * @param side
 */
function OphCiExamination_DRGrading_canUpdate(side) {
    var dr_side = $(".js-active-elements ."+OE_MODEL_PREFIX+"Element_OphCiExamination_DRGrading").find('.side[data-side="'+side+'"]');

    if (dr_side.length && !dr_side.hasClass('uninitialised') && !$('#drgrading_dirty').is(":visible")) {
        return true;
    }
    return false;
}

/**
 * update the dr grades for the given side (if they can be updated)
 *
 * @param side
 */
function OphCiExamination_DRGrading_update(side) {
    var physical_side = 'left';
    if (side == 'left') {
        physical_side = 'right';
    }
    if (OphCiExamination_DRGrading_canUpdate(side)) {
        var cv = $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_PosteriorPole').find('.side.' + physical_side).find('canvas');
        var drawingName = cv.data('drawing-name');
        var drawing = ED.getInstance(drawingName);
        var grades = gradeCalculator(drawing);
        if (grades) {
            updateDRGrades(drawing, grades[0], grades[1], grades[2], grades[3], grades[4], grades[5]);
        }
    }
}

function OphCiExamination_PosteriorPole_init() {
    $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_PosteriorPole').find('canvas').each(function() {

        var drawingName = $(this).attr('data-drawing-name');

        var func = function() {
            var _drawing = ED.getInstance(drawingName);
            var side = 'right';
            if (_drawing.eye) {
                side = 'left';
            }
            var dr_grade = $('#' + _drawing.canvas.id).closest('.element').find('.' + OE_MODEL_PREFIX + dr_grade_et_class);
            var dr_side = dr_grade.find('.side[data-side="'+side+'"]');

            OphCiExamination_DRGrading_dirtyCheck(_drawing);

            if (!$('#drgrading_dirty').is(":visible")) {
                var grades = gradeCalculator(_drawing);
                if (grades !== false)
                    updateDRGrades(_drawing, grades[0], grades[1], grades[2], grades[3], grades[4], grades[5]);
            }
        };

        edChecker = getOEEyeDrawChecker();
        edChecker.registerForReady(func);
    });
}

function OphCiExamination_DRGrading_init() {

    $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading').find('.drgrading_images_dialog').dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: 480
    });

    $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading').find('.grade-info-all').each(function() {
        $(this).dialog({
            title: 'Grade Definitions',
            autoOpen: false,
            modal: true,
            resizable: false,
            width: 800
        });
    });

    OphCiExamination_PosteriorPole_init();

    $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading').find('.grade-info').each(function(){
        var quick = $(this);
        var iconHover = $(this).parent().find('.grade-info-icon');

        iconHover.hover(function(e){
            var infoWrap = $('<div class="quicklook"></div>');
            infoWrap.appendTo('body');
            infoWrap.html(quick.html());

            var offsetPos = $(this).offset();
            var top = offsetPos.top;
            var left = offsetPos.left + 25;

            top = top - (infoWrap.height()/2) + 8;

            if (left + infoWrap.width() > 1150) left = left - infoWrap.width() - 40;
            infoWrap.css({'position': 'absolute', 'top': top + "px", 'left': left + "px", 'z-index': 110});
            infoWrap.fadeIn('fast');
        },function(e){
            $('body > div:last').remove();
        });
    });

    $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading').delegate('.grade-info-icon', 'click', function(e) {
        var side = getSplitElementSide($(this));
        var info_type = $(this).data('info-type');
        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_DRGrading_' + side + '_all_' + info_type + '_desc').dialog('open');
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
            els = els.filter(':not('+ignore+')');
        }
        els.each( function() {
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
            els = els.filter(':not('+ignore+')');
        }
        els.each( function() {
            if ($(this).attr('type') == 'radio') {
                $(this).prop('checked', $(this).data('stored-checked'));
            }
            else {
                $(this).val($(this).data('stored-val'));
            }
            $(this).prop('disabled', false);
        });
        element.show();
    }
}

function OphCiExamination_InjectionManagementComplex_check(side) {
    if ($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment').length >0) {
        val = $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment')[0].checked;
    } else {
        val = false;
    }

    if (val) {
        unmaskFields($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment_reason_id'));
        maskFields($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_treatment_fields'),'[id$="eye_id"]');

        // if we have an other selection on no treatment, need to display the text field
        var selVal = $('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment_reason_id').find('select').val();
        var other = false;

        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_right_no_treatment_reason_id').find('option').each(function() {
            if ($(this).val() == selVal) {
                if ($(this).data('other') == '1') {
                    other = true;
                }
                return true;
            }
        });
        if (other) {
            unmaskFields($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment_reason_other'));
        } else {
            maskFields($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment_reason_other'));
        }
    } else {
        maskFields($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment_reason_id'));
        maskFields($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_no_treatment_reason_other'));
        unmaskFields($('#div_'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_'+side+'_treatment_fields'),'[id$="eye_id"]');
    }
}

function OphCiExamination_InjectionManagementComplex_loadQuestions(side) {
    var disorders = Array($('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis1_id').val(),
                                     $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis2_id').val());
    var params = {
        'disorders': disorders,
        'side': side
    };

    $.ajax({
        'type': 'GET',
        'url': OphCiExamination_loadQuestions_url + '?' + $.param(params),
        'success': function(html) {
            // ensure we maintain any answers for questions that still remain after load (e.g. only level 2 has changed)
            var answers = {};
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_Questions').find('input:radio:checked').each(function() {
                answers[$(this).attr('id')] = $(this).val();
            });
            $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_Questions').replaceWith(html);
            for (var ans in answers) {
                if (answers.hasOwnProperty(ans)) {
                    $('#'+ans+'[value='+answers[ans]+']').attr('checked', 'checked');
                }
            }
        }
    });
}

function OphCiExamination_InjectionManagementComplex_DiagnosisCheck(side) {
    var el = $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis1_id');

    if (el.is(":visible") && el.val()) {
        var l2_el = $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis2_id');
        // check l2 selection needs updating
        if (l2_el.data('parent_id') != el.val()) {

            var l2_data;
            el.find('option').each(function() {
                if ($(this).val() == el.val()) {
                    l2_data = $(this).data('level2');
                    return true;
                }
            });

            if (l2_data) {
                // need to update the list of options in the level 2 drop down
                var options = '<option value="">- Please Select -</option>';
                for (var i in l2_data) {
                    options += '<option value="' + l2_data[i].id + '">' + l2_data[i].term + '</option>';
                }
                $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis2_id').html(options);
                $('#' + side + '_diagnosis2_wrapper').removeClass('hidden');
            }
            else {
                $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_' + side + '_diagnosis2_id').val('');
                $('#' + side + '_diagnosis2_wrapper').addClass('hidden');
            }
            // store the parent_id on the selector for later checking
            l2_el.data('parent_id', el.val());
        }
        else {
            // ensure its displayed
            $('#' + side + '_diagnosis2_wrapper').removeClass('hidden');
        }
        OphCiExamination_InjectionManagementComplex_loadQuestions(side);
    }
    else {
        $('#' + side + '_diagnosis2_wrapper').addClass('hidden');
        $('#Element_OphCiExamination_InjectionManagementComplex_' + side + '_Questions').html('');
    }
}

function OphCiExamination_InjectionManagementComplex_init() {
    OphCiExamination_InjectionManagementComplex_check('left');
    OphCiExamination_InjectionManagementComplex_check('right');

    $('.jsNoTreatment').find(':checkbox').bind('change', function() {
        var side = getSplitElementSide($(this));
        OphCiExamination_InjectionManagementComplex_check(side);
    });

    $('.'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_no_treatment_reason_id').find('select').bind('change', function() {
        var side = getSplitElementSide($(this));
        OphCiExamination_InjectionManagementComplex_check(side);
    });

    $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_right_diagnosis1_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_left_diagnosis1_id,' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_right_diagnosis2_id, ' +
        '#'+OE_MODEL_PREFIX+'Element_OphCiExamination_InjectionManagementComplex_left_diagnosis2_id').bind('change', function() {
        var side = getSplitElementSide($(this));
        OphCiExamination_InjectionManagementComplex_DiagnosisCheck(side);
    });

}

// END InjectionManagementComplex

function OphCiExamination_GetCurrentConditions() {
    var disorders = $("input[name='selected_diagnoses[]']").map(function() {
        return {'type': 'disorder', 'id': $(this).val()};
    });
    var findings = $(".OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings .multi-select-free-text-selections li input").map(function() {
        return {'type': 'finding', 'id': $(this).val()};
    });
    return {disorders: disorders, findings: findings};
}

/**
 * Add disorder or finding to exam
 * @param string type
 * @param integer conditionId
 * @param string label
 * @constructor
 */
function OphCiExamination_AddDisorderOrFinding(type, conditionId, label, isDiabetic, isGlaucoma) {
    if(type == 'disorder') {
        OphCiExamination_AddDiagnosis(conditionId, label, null, isDiabetic, isGlaucoma);
    } else if(type == 'finding') {
        OphCiExamination_AddFinding(conditionId, label);
    } else {
        console.log("Error: Unknown type: "+type);
    }
}

function OphCiExamination_AddFinding(finding_id, label) {
    var updateFindings = function() {
        $('#OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings_further_findings_assignment').val(finding_id).trigger('change');
        OphCiExamination_RefreshCommonOphDiagnoses();
    };
    if($('.OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings').length > 0) {
        updateFindings();
    } else {
        var el = $("[data-element-type-class='OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings']");
        if (el.length) {
            addElement(el.first(), false, true, 0, {}, updateFindings);
        } else {
            var sidebar = $('aside.episodes-and-events').data('patient-sidebar');
            if (sidebar) {
                sidebar.addElementByTypeClass('OEModule_OphCiExamination_models_Element_OphCiExamination_FurtherFindings', {}, updateFindings);
            } else {
                console.log('Cannot find sidebar to manipulate elements for VA change');
            }
        }

    }

}

function OphCiExamination_AddDiagnosis(disorderId, name, eyeId, isDiabetic, isGlaucoma, external) {
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

    eyeId = eyeId || $('input[name="'+OE_MODEL_PREFIX+'OphCiExamination_Diagnosis[eye_id]"]:checked').val();

    var checked_right = (eyeId == 2 ? 'checked="checked" ' : '');
    var checked_both = (eyeId == 3 ? 'checked="checked" ' : '');
    var checked_left = (eyeId == 1 ? 'checked="checked" ' : '');
    var checked_principal = (count == 0 ? 'checked="checked" ' : '');

    var row = '<tr' + (external ? ' class="external"' : '') + '>' +
        '<td>'+
        ((isDiabetic) ? '<input type="hidden" name="diabetic_diagnoses[]" value="1" /> ' : '') +
        ((isGlaucoma) ? '<input type="hidden" name="glaucoma_diagnoses[]" value="1" /> ' : '') +
        '<input type="hidden" name="selected_diagnoses[]" value="'+disorderId+'" /> '+name+' </td>'+
        '<td class="eye">'+
            '<label class="inline">'+
                '<input type="radio" name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_Diagnoses[eye_id_'+id+']" value="2" '+checked_right+'/> Right'+
            '</label> '+
            '<label class="inline">'+
                '<input type="radio" name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_Diagnoses[eye_id_'+id+']" value="3" '+checked_both+'/> Both'+
            '</label> '+
            '<label class="inline">'+
                '<input type="radio" name="'+OE_MODEL_PREFIX+'Element_OphCiExamination_Diagnoses[eye_id_'+id+']" value="1" '+checked_left+'/> Left'+
            '</label> '+
        '</td>'+
        '<td>'+
            '<input type="radio" name="principal_diagnosis" value="'+disorderId+'" '+checked_principal+'/>'+
        '</td>'+
        '<td>'+
            '<a href="#" class="removeDiagnosis" rel="'+disorderId+'">Remove</a>'+
        '</td>'+
    '</tr>';

    $('.js-diagnoses').append(row);
    OphCiExamination_RefreshCommonOphDiagnoses();
    //Adding new element to array doesn't trigger change so do it manually
    $(":input[name^='diabetic_diagnoses']").trigger('change');
    $(":input[name^='glaucoma_diagnoses']").trigger('change');
}

function OphCiExamination_Gonioscopy_Eyedraw_Controller(drawing) {
    this.notificationHandler = function (message) {
        switch (message.eventName) {
            case 'ready':
            case 'doodlesLoaded':
                OphCiExamination_Gonioscopy_switch_mode(drawing.canvas, drawing.firstDoodleOfClass('Gonioscopy').getParameter('mode'));
                break;
            case 'parameterChanged':
                if (message.object.doodle.className == 'Gonioscopy' && message.object.parameter == 'mode') {
                    OphCiExamination_Gonioscopy_switch_mode(drawing.canvas, message.object.value);
                }
                break;
        }
    };
    drawing.registerForNotifications(this);
}

function OphCiExamination_Gonioscopy_init() {
    $(".foster_images_dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: 480
    });

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
    if(isExpert) {
    expert.show(); basic.hide();
    } else {
    expert.hide(); basic.show();
    }
}

function OphCiExamination_Gonioscopy_switch_mode(canvas, mode) {
    var body = $(canvas).closest('.ed-body');
    var expert = body.find('.expert-mode');
    var basic = body.find('.basic-mode');

    if (mode == 'Expert') {
        expert.show(); basic.hide();
    } else {
        expert.hide(); basic.show();
    }
}

function OphCiExamination_GlaucomaRisk_init() {
    $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_GlaucomaRisk_descriptions').dialog({
        title: 'Glaucoma Risk Stratifications',
        autoOpen: false,
        modal: true,
        resizable: false,
        width: 800
    });
}

function OphCiExamination_ClinicOutcome_LoadTemplate(template_id) {
    if(Element_OphCiExamination_ClinicOutcome_templates[template_id]) {
        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_status_id')
            .val(Element_OphCiExamination_ClinicOutcome_templates[template_id]['clinic_outcome_status_id'])
            .trigger('change');
        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_quantity')
            .val(Element_OphCiExamination_ClinicOutcome_templates[template_id]['followup_quantity']);
        $('#'+OE_MODEL_PREFIX+'Element_OphCiExamination_ClinicOutcome_followup_period_id')
            .val(Element_OphCiExamination_ClinicOutcome_templates[template_id]['followup_period_id']);

    }
}

function OphCiExamination_RefreshCommonOphDiagnoses() {
    DiagnosisSelection_updateSelections();
}

function OphCiExamination_AddAllergy(){
    var other_name;
    var allergy_id = $('#allergy_id').val();
    if( allergy_id > 0) {
        var comments = $('#comments').val();
        if ($('#allergy_id').find(':selected').text() == 'Other') {
            other_name = $('#other_allergy').val();
        }

        if($('#allergy_id').find(':selected').text() == 'Other' && other_name == ''){
            alert("Please specify other allergy name!");
        }else {

            row = '<tr data-allergy-name="'+$('#allergy_id').find(':selected').text()+'" data-allergy-id="'+allergy_id+'"><td>';
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
            $('#allergy_other').slideUp('fast');  //close the div
            if( $('#allergy_id').find(':selected').text() != 'Other' ) {
                $('#allergy_id').find('option:selected').remove();
            }
            $('#allergy_id').val('');
        }
    }else{
        alert("Please select an option from the allergies!");
   }
}

function removeAllergyFromSelect( allergy_id, allergy_name ){
    if( allergy_name != 'Other') {
        $('#allergy_id').find("option[value='" + allergy_id + "']").remove();
    }
}

var eyedraw_added_diagnoses = [];

$(document).ready(function() {
    $('textarea').autosize();
});
