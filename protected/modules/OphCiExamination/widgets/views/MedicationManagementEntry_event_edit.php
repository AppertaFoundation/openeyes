<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\MedicationManagementEntry;

?>

<?php

/** @var MedicationManagementEntry $entry */

if (isset($entry->start_date)) {
    $start_date = $entry->start_date;
} else {
    $start_date = date('Y-m-d');
}

if (isset($entry->end_date)) {
    list($end_sel_year, $end_sel_month, $end_sel_day) = explode('-', $entry->end_date);
} else {
    $end_sel_day = date('d');
    $end_sel_month = date('m');
    $end_sel_year = date('Y');
}

$prescribe = isset($entry->prescribe) ? $entry->prescribe : ($row_type == "prescribed");
$stop = isset($entry->stop) ? $entry->stop : ($row_type == "closed");
$is_new = isset($is_new) ? $is_new : false;

$prescribe_hide_style = $entry->prescribe ? "display: initial" : "display: none";

$entry_allergy_ids = isset($entry->medication_id) ?
    implode(",", array_map(function ($e) {
        return $e->id;
    }, $entry->medication->allergies)) : $allergy_ids ?? '';
?>

<tr data-key="<?= $row_count ?>" data-event-medication-use-id="<?php echo $entry->id; ?>" data-allergy-ids="<?php echo $entry_allergy_ids ?>" class="divider col-gap js-first-row <?= $field_prefix ?>_row <?= $is_new ? ' new' : '' ?><?= $entry->hidden === '1' ? ' hidden' : '' ?>">

    <td id="<?= $model_name . "_entries_" . $row_count . '_duplicate_error' ?>" class="drug-details" rowspan="2">
        <?php if ($entry->originallyStopped) { ?>
            <i class="oe-i stop small pad"></i>
        <?php } ?>
        <span class="js-medication-display">
            <?= is_null($entry->medication_id) ? "{{medication_name}}" : $entry->getMedicationDisplay(true) ?>
        </span>
        <span class="js-prepended_markup">
            <?php
            if (!is_null($entry->medication_id)) {
                if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
                    echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' . implode(',', $patient->getPatientDrugAllergy($entry->medication_id)) . '"></i>';
                }

                $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
            } else {
                echo "{{& allergy_warning}}";
                echo "{{& prepended_markup}}";
            }
            ?>
        </span>



        <?php /* <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int)$entry->is_copied_from_previous_event; ?>" /> */ ?>
        <input type="hidden" class="rgroup" name="<?= $field_prefix ?>[group]" value="<?= $row_type; ?>" />
        <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[medication_id]" value="<?= $is_new ? "{{medication_id}}" : $entry->medication_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay(true) ?>" class="medication-name" />
        <input type="hidden" name="<?= $field_prefix ?>[usage_type]" class="js-usage-type" value="<?= isset($entry->usage_type) ? $entry->usage_type : $usage_type ?>" />
        <?php if ($entry->id && $entry->id !== '') { ?>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>" />
        <?php } ?>
        <input type="hidden" name="<?= $field_prefix ?>[hidden]" class="js-hidden" value="<?= $entry->hidden ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" class="js-prescription-item-id" value="<?= $entry->prescription_item_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int) $entry->is_copied_from_previous_event; ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[is_discontinued]" value="<?= (int) $entry->is_discontinued; ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[locked]" value="<?= $locked ?>" class="js-locked" />
        <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date ? $entry->start_date : date('Y-m-d') ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[bound_key]" class="js-bound-key" value="<?= $entry->bound_key ?>">
        <input type="hidden" name="<?= $field_prefix ?>[source_subtype]" class="js-source-subtype" value="{{source_subtype}}" disabled="disabled">
    </td>
    <td class="dose-frequency-route alternative-display">
        <?php
        $dfrl_validation_error = array_intersect(
            array("dose", "dose_unit_term", "frequency_id", "route_id", "laterality"),
            array_keys($entry->errors)
        ); ?>
        <div class="flex-meds-inputs alternative-display-element" id="<?= $model_name . "_entries_" . $row_count . "_dfrl_error" ?>" >
            <input class="fixed-width-small js-dose" id="<?= $model_name . "_entries_" . $row_count . "_dose" ?>" type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
            <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" />
            <?php if ($is_template) { ?>
                {{#has_dose_unit_term}}
                    <span class="js-dose-unit-term"><?php echo $entry->dose_unit_term; ?></span>
                {{/has_dose_unit_term}}
                {{^has_dose_unit_term}}
                    <?php
                    echo CHtml::dropDownList(
                        $field_prefix . '[dose_unit_term]',
                        null,
                        $unit_options,
                        [
                            'empty' => '-Unit-',
                            'disabled' => $direct_edit || $dfrl_validation_error ? '' : 'disabled',
                            'class' => 'js-unit-dropdown cols-2',
                            'style' => $direct_edit || $dfrl_validation_error ? '' : 'display:none'
                        ]
                    );
                    ?>
                {{/has_dose_unit_term}}
            <?php } else {
                if ($entry->dose_unit_term !== "") { ?>
                    <span class="js-dose-unit-term"><?php echo $entry->dose_unit_term; ?></span>
                <?php } else {
                    echo CHtml::dropDownList(
                        $field_prefix . '[dose_unit_term]',
                        null,
                        $unit_options,
                        [
                            'empty' => '-Unit-',
                            'disabled' => $direct_edit || $dfrl_validation_error ? '' : 'disabled',
                            'class' => 'js-unit-dropdown cols-2',
                            'style' => $direct_edit || $dfrl_validation_error ? '' : 'display:none'
                        ]
                    );
                }
            } ?>
            <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Frequency-', 'class' => 'js-frequency cols-4')) ?>
            <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Route-', 'class' => 'js-route cols-3')) ?>
            <span class="oe-eye-lat-icons admin-route-options js-laterality" style="<?= $entry->routeOptions() ? "" : "display: none" ?>">
                <?php
                $lateralityClass = ($entry->hasErrors('laterality') ? 'error' : '')
                ?>
                <label class="inline highlight <?= $lateralityClass ?>">
                    <input value="2" name="eyelat-select-R" type="checkbox" <?= $entry->laterality === "2" || $entry->laterality === "3" ? "checked" : "" ?>>R
                </label>
                <label class="inline highlight <?= $lateralityClass ?>">
                    <input value="1" name="eyelat-select-L" type="checkbox" <?= $entry->laterality === "1" || $entry->laterality === "3" ? "checked" : "" ?>> L
                </label>
            </span>
            <?php echo CHtml::hiddenField(
                $field_prefix . '[laterality]',
                $entry->laterality,
                array('class' => 'laterality-input')
            ); ?>
        </div>
        
    </div>
    </td>
    <td>
        <div class="flex-meds-inputs">
            <!-- Duration/dispense/comments-->
            <?= CHtml::dropDownList(
                $field_prefix . '[duration_id]',
                $entry->duration_id,
                CHtml::listData(
                    MedicationDuration::model()->activeOrPk([$entry->duration_id])->findAll(array('order' => 'display_order')),
                    'id',
                    'name'
                ),
                array('empty' => '- Select -', 'class' => 'cols-4 js-duration', 'style' => $prescribe_hide_style)
            ) ?>
            <?= CHtml::dropDownList(
                $field_prefix . '[dispense_condition_id]',
                $entry->dispense_condition_id,
                CHtml::listData(
                    OphDrPrescription_DispenseCondition::model()->withSettings($overprint_setting, $fpten_dispense_condition->id)->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION),
                    'id',
                    'name'
                ),
                array('class' => 'js-dispense-condition cols-5', 'style' => $prescribe_hide_style, 'empty' => 'Select', 'options' => $dispense_condition_options)
            ); ?>

            <?php
            $locations = $entry->dispense_condition ? $entry->dispense_condition->getLocations() : array('');
            $style = $entry->dispense_condition ? '' : 'display: none;';
            echo CHtml::dropDownList(
                $field_prefix . '[dispense_location_id]',
                $entry->dispense_location_id,
                CHtml::listData(
                    $locations,
                    'id',
                    'name'
                ),
                array('class' => 'js-dispense-location cols-3', 'style' => $prescribe_hide_style)
            );
            ?>
        </div>
    </td>
    <!-- </td> -->
    <td>
        <div>
            <label class="toggle-switch">
                <input name="<?= $field_prefix ?>[prescribe]" type="checkbox" value="1" <?php if ($entry->prescribe) {
                                                                                            echo "checked";
                             } ?> />
                <span class="toggle-btn js-btn-prescribe"></span>
            </label>
        </div>
    </td>
    <td class="nowrap">
        <button class="js-reset-mm" style="display: none">
            <i class="oe-i reset small"></i> &nbsp;Reset
        </button>
        &nbsp;
        <!-- Actions -->
        <?php $tooltip_content_comes_from_history = "This item comes from medication history. " .
            "If you wish to delete it, it must be deleted from the Medication History element. " .
            "Alternatively, mark this item as stopped."; ?>
        <span data-tooltip-content-comes-from-history="<?= $tooltip_content_comes_from_history ?>">
            <i class="oe-i trash js-remove"></i>
        </span>
    </td>
</tr>
<tr class="no-line col-gap js-second-row <?= $entry->hidden === "1" ? ' hidden' : '' ?>" data-key="<?= $row_count ?>">
    <td class="nowrap">
        <div class="flex-meds-inputs">
            <span class="end-date-column" id="<?= $model_name . "_entries_" . $row_count . "_end_date_error" ?>" style="<?php if ($entry->prescribe) {
                ?> display: none <?php
                                              } ?>">
                <div class="alternative-display">
                    <div class="alternative-display-element textual" style="display: <?= $entry->hasErrors() && ($entry->end_date || $entry->stop_reason_id) ? 'none' : '' ?>">
                        <a class="js-meds-stop-btn" data-row_count="<?= $row_count ?>" href="javascript:void(0);">
                            <?php if (!is_null($entry->end_date)) : ?>
                                <i class="oe-i stop small pad"></i>
                                <?= Helper::formatFuzzyDate($end_sel_year . '-' . $end_sel_month . '-' . $end_sel_day) ?>
                            <?php else : ?>
                                <span><button type="button"><i class="oe-i stop small pad-right"></i>Click here to stop</button></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <fieldset class="js-datepicker-wrapper js-end-date-wrapper" <?= ($entry->hasErrors() && !$is_new) && ($entry->end_date || $entry->stop_reason_id) ? '' : ' style="display:none;"' ?>>
                        <i class="oe-i stop small pad"></i>
                        <input id="<?= $model_name ?>_entries_<?= $row_count ?>_end_date" class="js-end-date" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" data-default="<?= date('Y-m-d') ?>" data-reset-value="<?= $entry->end_date ?>" style="width:80px" placeholder="yyyy-mm-dd" autocomplete="off">
                    </fieldset>
                </div>
            </span>
            <span id="<?= $model_name . '_entries_' . $row_count . '_stop_reason_id_error' ?>" class="js-stop-reason-select cols-5" style="<?= (($entry->hasErrors() || $is_new) && !($entry->end_date || $entry->stop_reason_id)) || $entry->prescribe || !$entry->hasErrors() ? 'display: none' : '' ?>">
                <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => 'Reason stopped?', 'class' => ' js-stop-reason')) ?>
            </span>
            <div class="js-stop-reason-text" style="<?= $is_new || (!$entry->hasErrors() && $entry->end_date && $entry->stop_reason_id) ? "" : "display:none" ?>">
                <?= !is_null($entry->stop_reason_id) ? '&nbsp;<em class="fade">(' . $entry->stopReason->name . ')</em>' : ''; ?>
            </div>
        </div>
    </td>
    <td>
        <div class="js-comment-container flex-layout flex-left" id="<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>" style="<?php if (!$entry->comments) :
            ?>display: none;<?php
                                                                    endif; ?>" data-comment-button="#<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button">
            <?= CHtml::textArea($field_prefix . '[comments]', $entry->comments, [
                'class' => 'js-comment-field autosize cols-full',
                'rows' => '1',
                'placeholder' => 'Comments',
                'autocomplete' => 'off',
                                                                                                                                                ]) ?>
            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
        </div>
        <button id="<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button" class="button js-add-comments" data-comment-container="#<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>" type="button" data-hide-method="display" style="<?php if ($entry->comments) :
            ?>display: none;<?php
                    endif; ?>">
            <i class="oe-i comments small-icon"></i>
        </button>
    </td>
    <td>
        <button class="js-add-taper" type="button" title="Add taper" style="<?= $prescribe_hide_style ?>">
            <i class="oe-i child-arrow small"></i>
        </button>
    </td>
    <td></td>
</tr>
<?php

if (!empty($entry->tapers)) {
    $tcount = 0;
    foreach ($entry->tapers as $taper) {
        $this->render(
            "MedicationManagementEntryTaper_event_edit",
            array(
                "element" => $this->element,
                "entry" => $taper,
                "row_count" => $row_count,
                "model_name" => $model_name,
                "taper_count" => $tcount,
                "field_prefix" => $model_name . "[entries][$row_count][taper][$tcount]"
            )
        );
        $tcount++;
    }
}

?>