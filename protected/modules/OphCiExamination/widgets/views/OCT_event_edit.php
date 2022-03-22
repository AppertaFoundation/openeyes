<?php

use OEModule\OphGeneric\models\Assessment;
use OEModule\OphGeneric\models\AssessmentEntry;

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/modules/OphGeneric/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/Attachment.js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/Assessment.js');

$assessments = [];
$model_name = CHtml::modelName($element);
$event_ids = [];
foreach ($this->assessments as $assessment) {
    $event_ids[] = $assessment->event_id;
}

if (!empty($this->assessments)) {
// reformat array of posted assessments to order them by row and then by side
    $posted_assessments = [];
    if (isset($_POST['OEModule_OphGeneric_models_Assessment']['entries'])) {
        foreach ($_POST['OEModule_OphGeneric_models_Assessment']['entries'] as $assessment_id => $assessment_entry_side) {
            foreach ($assessment_entry_side as $eye_side => $assessment_entry_data) {
                $posted_assessments[$assessment_entry_data['row']][$eye_side] = $assessment_entry_data;
                $posted_assessments[$assessment_entry_data['row']][$eye_side]['id'] = $assessment_id;
            }
        }
    }

    $event_type = EventType::model()->find('name = "Examination"');
    $api = Yii::app()->moduleAPI->get('OphGeneric');
    ?>

    <?php echo CHtml::activeHiddenField($this->element, "id"); ?>

    <div class="element-fields full-width js-oct-container" id="<?= $model_name ?>_element">
        <?php
        foreach ($posted_assessments as $row => $side_assessments) { ?>
            <div class="js-oct-row">

                <?php
                $event_ids = [];
                foreach (['left', 'right'] as $eye_side) {
                    if (isset($side_assessments[$eye_side]['id'])) {
                        $event_ids[] = Assessment::model()->findByPk($side_assessments[$eye_side]['id'])->event_id;
                    }
                }

                $this->widget(
                    'application.modules.OphGeneric.widgets.Attachment',
                    [
                        'event_ids' => $event_ids,
                        'allow_attach' => false,
                        'element' => null,
                        'show_titles' => true,
                        'is_examination' => true,
                    ]
                );
                ?>
                <div class="js-assessment-container element-fields element-eyes">
                    <?php if (array_key_exists('left', $side_assessments) && !array_key_exists('right', $side_assessments)) { ?>
                        <div class="js-element-eye right-eye left" data-side="right"></div>
                    <?php } ?>

                    <?php foreach ($side_assessments as $eye_side => $assessment_data) {
                        $assessment_entry = AssessmentEntry::model()->findByPk($assessment_data['entry_id']);
                        $assessment_entry->attributes = $assessment_data;

                        $assessment = Assessment::model()->findByPk($assessment_data['id']);

                        $datetime = new DateTime($assessment->event->event_date);

                        if ($eye_side === 'left') {
                            $page_side = 'right';
                        } else {
                            $page_side = 'left';
                        }
                        ?>
                        <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?>" data-side="<?= $eye_side ?>">
                            <div class="assessment cols-full"
                                 data-assessment-id='<?= $assessment->id ?>'
                                 data-assessment-side='<?= $assessment->eye->name ?>'
                                 data-assessment-date='<?= $datetime->format("Ymd") ?>'
                                 data-assessment-time="<?= $datetime->format('His') ?>">
                                <?php $this->widget('application.modules.OphGeneric.widgets.Assessment', [
                                    // TODO TODO TODO during the development cycle this will be overridden in the widget init
                                    'assessment' => $assessment,
                                    'entry' => $assessment_entry,
                                    'patient' => $this->patient,
                                    'event_type' => $event_type,
                                    'key' => $assessment->id,
                                    'row' => $assessment_data['row'],
                                    'side' => $eye_side
                                ]);
                                ?>
                            </div>
                            <?php if ($page_side === "right" && $assessment->eye_id === Eye::BOTH || $assessment->eye_id !== Eye::BOTH) { ?>
                                <div class="flex-layout flex-right">
                                    <i class="oe-i trash js-delete-assessment"></i>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if (array_key_exists('right', $side_assessments) && !array_key_exists('left', $side_assessments)) { ?>
                        <div class="js-element-eye left-eye right" data-side="left"></div>
                    <?php } ?>
                </div>
            </div>
            <hr class="divider">
        <?php } ?>
    </div>
    <br>
    <div class="add-data-actions flex-right flex-layout" id="add-oct">
        <button class="button hint green" id="add-oct-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>


    <script type="text/javascript">
        baseUrl = "<?=Yii::app()->getBaseUrl(true)?>";

        $('#assesment-selector').on('change', function () {
            $('.assessment').hide();
            $('.assessment.' + $(this).val()).show();
        });

        $('.js-oct-container').on('click', '.js-delete-assessment', function () {
            let $element_eye = $(this).closest('.js-element-eye');
            let eye_side = $element_eye.data('side');
            let assessment_side = $element_eye.find('.assessment').data('assessment-side');
            let opposite_side = assessment_side === 'Left' ? 'right' : 'left';

            if (assessment_side == "Both") {
                $(this).closest('.js-oct-row').remove();
            }
            let $assessment_container = $element_eye.parent();
            let $attachments = $assessment_container.siblings('.attachment').find('.element-eyes').find('.js-element-eye[data-side="' + eye_side + '"]');

            $assessment_container.css({"border-bottom": ""});
            $element_eye.html('');
            $attachments.html('');
            disableClickAssessmentList();

            if ($assessment_container.find('.js-element-eye.' + opposite_side + '-eye').html() === '') {
                $($assessment_container).parent().remove();
            }
        });

        /** Search for assessments present in the examination and disable them in the adder dialog list */
        function disableClickAssessmentList() {
            let ids = $('section.OEModule_OphCiExamination_models_OCT .assessment');
            $('section.OEModule_OphCiExamination_models_OCT .select-options li').css({
                "pointer-events": "",
                "opacity": ""
            });
            for (let i = 0; i < ids.length; i++) {
                let assessmentId = $(ids[i]).data('assessment-id');
                $('section.OEModule_OphCiExamination_models_OCT .select-options li[data-id="' + assessmentId + '"]').css({
                    "pointer-events": "none",
                    "opacity": 0.5
                });
            }
        }

        function updateOctHiddenInputOrder($element) {
            let rows = $element.find('.js-oct-row');

            for (let rowIndex = 0; rowIndex < rows.length; rowIndex++) {
                let assessments = $(rows[rowIndex]).find('.assessment');
                for (let assessmentIndex = 0; assessmentIndex < assessments.length; assessmentIndex++) {
                    $(assessments[assessmentIndex]).find('.js-hidden-oct-row').val(rowIndex);
                    $(assessments[assessmentIndex]).find('.js-hidden-oct-side').val($(assessments[assessmentIndex]).data('assessment-side'));
                }
            }
        }

        /** Return html string of a div wrapper for left/right/both sides */
        function getHtmlSideWrapper(row, side, assessment) {
            let html = '<div class="js-oct-row">' +
                '<div class="js-assessment-container element-fields element-eyes">';
            // left side must be aligned to the right of the screen;
            // the only way to do this was to add a blank right-eye side
            if (side === "Left") {
                html += '<div class="js-element-eye right-eye column left"></div>' + assessment.html;
            } else if (side === "Right") {
                html += assessment.html + '<div class="js-element-eye left-eye column right"></div>';
            } else {
                // for Both
                html += assessment.html_right + assessment.html_left;
            }

            return html + '</div>' + '</div>';
        }

        /** Logic for inserting the assesments in a sorted manner */
        function addAssessmentToList($element, assessment) {
            // let rows = $element.find('.js-assessment-container'); TODO TEST TODO TEST TODO TEST TODO TEST
            let rows = $element.find('.js-oct-row');
            let inserted = false;

            for (let rowIndex = 0; rowIndex < rows.length; rowIndex++) {
                let row = rows[rowIndex];
                let rowDate = getRowDate(row);

                // if assesment's date is before current row's date, insert assessment before current row
                if (parseInt(assessment.date) < rowDate) {
                    inserted = true;
                    $(getHtmlSideWrapper(rows.length, assessment.side, assessment)).insertBefore(row);
                    return rowIndex;
                    // if the assessment has the same date as the current row's date
                } else if (parseInt(assessment.date) === rowDate) {
                    let otherSide = getOtherSide(assessment.side);
                    let otherSideTime = getSideTime(row, otherSide);
                    // if assessment side is empty
                    //     AND the other side is not empty
                    //     AND difference between assessment's time and other side's time is smaller than 1 hour
                    if (sideIsEmpty(row, assessment.side)
                        && !sideIsEmpty(row, otherSide)
                        && Math.abs(parseInt(assessment.time) - otherSideTime) <= 10000) {
                        inserted = true;
                        // set assessment in row's empty side
                        $(row).find('div.' + assessment.side.toLowerCase() + '-eye').replaceWith(assessment.html);
                        return rowIndex;
                    }
                    // otherwise, if assessment's time is before other side's time or the entire row's time, insert assessment before current row
                    if (parseInt(assessment.time) < otherSideTime || parseInt(assessment.time) < getRowTime(row)) {
                        inserted = true;
                        $(getHtmlSideWrapper(rows.length, assessment.side, assessment)).insertBefore(row);
                        return rowIndex;
                    }
                }
            }

            // assessment did not match any check: append it at the end of the list
            if (!inserted) {
                $element.append(getHtmlSideWrapper(rows.length, assessment.side, assessment));
                return rows.length;
            }
        }

        function getOtherSide(side) {
            return side.toLowerCase() === "left" ? "right" : (side.toLowerCase() === "right" ? "left" : null);
        }

        function getRowTime(row) {
            return parseInt($(row).find('.assessment').data('assessment-time'));
        }

        function getRowDate(row) {
            return parseInt($(row).find('.assessment').data('assessment-date'));
        }

        function sideIsEmpty(row, side) {
            if (!side || side.toLowerCase() === "both") {
                return null;
            }
            let sideSlot = $(row).find("." + side.toLowerCase() + "-eye .assessment");
            return sideSlot === null || sideSlot.length === 0 || sideSlot.html().length == 0;
        }

        function getSideTime(row, side) {
            if (!side) {
                return null;
            }
            return parseInt($(row).find('.' + side.toLowerCase() + '-eye .assessment').data('assessment-time'));
        }

        function addAssessmentsToList($element, data) {
            let modifiedRows = [];
            for (let i = 0; i < data.length; i++) {
                modifiedRows.push(addAssessmentToList($element, data[i]));
            }

            let rows = $element.find('.js-oct-row');
            for (let i = 0; i < modifiedRows.length; i++) {
                // get attachments only for the modified rows or new rows
                let rowIndex = modifiedRows[i];
                let assessmentIds = [];
                let assessments = $(rows[rowIndex]).find('.assessment');
                for (let assessment_i = 0; assessment_i < assessments.length; assessment_i++) {
                    assessmentIds.push($(assessments[assessment_i]).data('assessment-id'));
                }

                $.ajax({
                    'type': 'GET',
                    'url': baseUrl + '/OphCiExamination/Default/getAttachment?assessment_ids=' + JSON.stringify(assessmentIds),
                    'success': function (data) {
                        // remove old attachment if exists and insert the new one
                        $(rows[rowIndex]).find('.attachment').remove();
                        $(rows[rowIndex]).prepend(data);
                    },
                    'error': function () {
                        new OpenEyes.UI.Dialog.Alert({
                            content: "Sorry, an internal error occurred and the attachment could not be loaded.\n\nPlease contact support for assistance."
                        }).open();
                    }
                });
            }

            updateOctHiddenInputOrder($element);

            rows.find('.js-assessment-container').each(function (index) {
                if (index < rows.length - 1) {
                    $(this).css({
                        "border-bottom": "2px dotted #9e9ec7",
                        "padding": "0 0 0.5em 0",
                        "margin": "0 0 0.5em 0"
                    });
                }
            });
        }

        $(function () {
            new OpenEyes.UI.AdderDialog({
                openButton: $('#add-oct-btn'),
                itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($assessment) {
                            $api = Yii::app()->moduleAPI->get('OphGeneric');
                            return [
                                'label' => "{$api->getLaterality($assessment->event->id)->getAdjective()} {$assessment->event->firstEventSubtypeItem->event_subtype} ({$assessment->event->event_date})",
                                'id' => $assessment->id
                            ];
                    }, $this->assessments)
                                                               ) ?>,
                    {'multiSelect': true}
                ),
                ],
                onReturn: function (adderDialog, selectedItems) {
                    // nothing was selected
                    if (!selectedItems.length) {
                        return;
                    }

                    let $element = $("#<?=$model_name?>_element");
                    let assessmentIds = [];

                    for (let i = 0; i < selectedItems.length; i++) {
                        assessmentIds.push(selectedItems[i].id);
                    }

                    $.ajax({
                        'type': 'GET',
                        'url': baseUrl + '/OphCiExamination/Default/getOctAssessment?assessment_ids=' + JSON.stringify(assessmentIds),
                        'success': function (data) {
                            addAssessmentsToList($element, data);
                            disableClickAssessmentList();
                        },
                        'error': function () {
                            new OpenEyes.UI.Dialog.Alert({
                                content: "Sorry, an internal error occurred and the OCT events could not be loaded.\n\nPlease contact support for assistance."
                            }).open();
                        }
                    });
                },
                onOpen: function () {
                },
            });

            $(document).ready(function () {
                disableClickAssessmentList();
            });
        });
    </script>
<?php } else {
    ?>
    <div class="cols-12 column text-center">
        There are no recorded OCT events for this patient.
    </div>
<?php } ?>
