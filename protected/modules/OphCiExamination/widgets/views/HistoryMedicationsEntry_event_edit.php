<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
if (isset($entry->start_date)) {
    $start_date = $entry->start_date;
} else {
    $start_date = date('Y-m-d');
}
if ($entry->id && $entry->id !== '' && $entry->latest_med_use_id) {
    $previous_stop_reason_details = $entry->getPreviousStopReason($entry->latest_med_use_id);
}

if (isset($entry->end_date)) {
    list($end_sel_year, $end_sel_month, $end_sel_day) = explode('-', $entry->end_date);
} else {
    $end_sel_day = date('d');
    $end_sel_month = date('m');
    $end_sel_year = date('Y');
}

$chk_prescribe = isset($entry->chk_prescribe) ? $entry->chk_prescribe : ($row_type == "prescribed");
$chk_stop = isset($entry->chk_stop) ? $entry->chk_stop : ($row_type == "closed");
$is_new = isset($is_new) ? $is_new : false;
$entry_is_stopped = $entry->originallyStopped || $stopped;
$to_be_copied = !$entry_is_stopped && isset($entry->medication) && $entry->medication->getToBeCopiedIntoMedicationManagement();
$disabled = $removable && $entry->is_copied_from_previous_event && !$stopped;

$is_posting = Yii::app()->request->getIsPostRequest();


$entry_allergy_ids = !is_null($entry->medication_id) ?
    implode(",", array_map(function ($e) {
        return $e->id;
    }, $entry->medication->allergies)) :
    [];

$stop_fields_validation_error = array_intersect(
    array("end_date", "stop_reason_id"),
    array_keys($entry->errors)
);

?>

<tr class="divider col-gap js-first-row <?= $stopped ? 'fade' : '' ?> <?= $field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : '' ?><?= $row_type == 'closed' ? ' stopped' : '' ?><?= $is_new ? "new" : "" ?>" data-key="<?= $row_count ?>" data-event-medication-use-id="<?php echo $entry->id; ?>"
<?php if (!is_null($entry->medication_id)) { ?>
    data-allergy-ids="<?= $entry_allergy_ids ?>"
<?php } elseif ($allergy_ids) { ?>
    data-allergy-ids="<?= $allergy_ids ?>" 
<?php } ?> <?= $row_type == 'closed' ? ' style="display:none;"' : '' ?>>
    <td id="<?= $model_name . "_entries_" . $row_count . '_duplicate_error' ?>" class="drug-details" rowspan="2">
        <div class="medication-display">
            <?= is_null($entry->medication_id) ? "{{medication_name}}" : $entry->getMedicationDisplay(true) ?>
            <span class="js-prepended_markup">
                <?php if (!is_null($entry->medication_id)) {
                    if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
                        echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' . implode(',', $patient->getPatientDrugAllergy($entry->medication_id)) . '"></i>';
                    }
                    $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
                    echo $entry->renderPGDInfo();
                } else {
                    echo "{{& allergy_warning}}";
                    echo "{{& prepended_markup}}";
                    echo "{{& pgd_info_icon}}";
                } ?>
            </span>
        </div>

        <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int)$entry->is_copied_from_previous_event; ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[copied_from_med_use_id]" value="<?= (int) $entry->copied_from_med_use_id ?>" />
        <input type="hidden" class="rgroup" name="<?= $field_prefix ?>[group]" value="<?= $row_type; ?>" />
        <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[pgdpsd_id]" value="<?= $entry->pgdpsd_id ?>" />
        <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[medication_id]" value="<?= !isset($entry->medication_id) ? "{{medication_id}}" : $entry->medication_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay(true) ?>" class="medication-name" />
        <input type="hidden" name="<?= $field_prefix ?>[usage_type]" value="<?= isset($entry->usage_type) ? $entry->usage_type : $usage_type ?>" />
        <?php if ($entry->id && $entry->id !== '') { ?>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>" />
        <?php } ?>
        <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" class="js-prescription-item-id" value="<?= $entry->prescription_item_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[to_be_copied]" class="js-to-be-copied" value="<?php echo (int)$to_be_copied; ?>" />
        <input type="hidden" name="previous_stop_reason_details" value="<?= $previous_stop_reason_details ?? '' ?>">
        <input type="hidden" name="<?= $field_prefix ?>[bound_key]" class="js-bound-key" value="<?= !isset($entry->bound_key) && isset($is_template) && $is_template ? "{{bound_key}}" : $entry->bound_key ?>">
        <input type="hidden" name="<?= $field_prefix ?>[stopped_in_event_id]" value="<?= $entry->stopped_in_event_id ?>" />
    </td>
    <?php if (!empty($entry->errors) || !isset($entry->dose)) {
        $show_unit = in_array(true, array_map(function ($i) {
            return strpos($i, 'dose') !== false;
        }, array_keys($entry->errors)));
    } else {
        $show_unit = $direct_edit;
    } ?>
    <td class="dose-frequency-route">
        <div id="<?= $model_name . "_entries_" . $row_count . "_dfrl_error" ?>">
            <div class="flex-meds-inputs">
                <div class="alternative-display inline">
                    <div class="alternative-display-element textual flex-meds-inputs">
                        <div class="textual-display hint">
                            <?php $entry_text_display = $entry->getAdministrationDisplay(true);
                            echo ($entry_text_display != "" && !$element_errors) ? $entry_text_display : "Add dose/frequency/route"; ?>
                        </div>
                        <span class="tabspace"></span>                   
                    </div>
                    <div class="alternative-display-element" <?= !$direct_edit && !$element_errors ? 'style="display: none;"' : '' ?>>
                        <input class="fixed-width-small js-dose " type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
                        <span class="js-dose-unit-term cols-2"><?php echo $entry->dose_unit_term; ?></span>
                        <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" <?= $show_unit ? 'disabled' : '' ?> />
                        <?php echo CHtml::dropDownList($field_prefix . '[dose_unit_term]', null, $unit_options, array('empty' => '-Unit-', 'disabled' => $show_unit ? '' : 'disabled', 'class' => 'js-unit-dropdown cols-2', 'style' => 'display:' . ($show_unit ? '' : 'none'))); ?>
                        <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Frequency-', 'class' => 'js-frequency cols-4')) ?>
                        <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Route-', 'class' => 'js-route cols-3')) ?>
                        <span class="oe-eye-lat-icons admin-route-options js-laterality" style="<?= $entry->routeOptions() ? "" : "display:none" ?>">
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
            </div>
            <?php /* if(!$is_new): ?><button type="button" class="alt-display-trigger small">Change</button><?php endif; */ ?>
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
    <td></td>
    <td class="edit-column">
        <?php
        if ($entry->latest_prescribed_med_use_id) {
            echo '<i class="oe-i info small pad js-has-tooltip" data-tooltip-content= "This item was previously prescribed through OpenEyes and cannot be changed. Please use the <strong><em>Stopped</em></strong> button to end this entry"></i></i>';
        } elseif ($removable) {
            if (!$entry->is_copied_from_previous_event) {
                echo '<i class="oe-i trash js-remove"></i>';
            } elseif (!$stopped) {
                echo '<i class="oe-i no-permissions js-has-tooltip" data-tooltip-content="This item cannot be deleted as it was added in a previous event. Please use the <strong><em>Click here to stop</em></strong> button to end this entry"></i>';
            }
        } ?>
</tr>
<?php
$start_date_display = str_replace('-00', '', $entry->start_date);
$start_date_display = str_replace('0000', '', $start_date_display);
$end_date_display = str_replace('-00', '', $entry->end_date);
$end_date_display = str_replace('0000', '', $end_date_display);
?>
<tr data-key="<?= $row_count ?>" class="no-line col-gap js-second-row <?= $stopped ? 'fade' : '' ?>">
    <td class="nowrap">
        <div class="flex-meds-inputs">
            <span class="start-date-column" id="<?= $model_name . "_entries_" . $row_count . "_start_date_error" ?>">
                <div class="alternative-display inline">
                    <?php if (!$is_new && empty($entry->errors)) { ?>
                        <div class="alternative-display-element textual">
                            <a class="js-start-date-display" href="javascript:void(0);">
                                <i class="oe-i start small pad-right"></i>
                                <?= $entry->getStartDateDisplay() ?>
                            </a>
                        </div>
                    <?php } ?>
                    <fieldset style="display: <?= $is_new || !empty($entry->errors) ? 'block' : 'none' ?> " class="js-datepicker-wrapper js-start-date-wrapper">
                        <i class="oe-i start small pad-right"></i>
                        <input
                            <?= ($disabled || $stopped) && !$entry->isUndated() ? 'disabled="disabled"' : ''?>
                            id="<?= $model_name ?>_entries_<?= $row_count ?>_start_date"
                            value="<?= $start_date_display ? Helper::convertDate2NHS($start_date_display) : '' ?>"
                            style="width:80px;"
                            placeholder="dd Mth yyyy"
                            autocomplete="off"
                            class="date medical-history-date"
                            autocomplete="off"
                            data-pmu-format="d b Y"
                            data-hidden-input-selector="#medical_history-start-date-<?= $row_count; ?>"
                        >
                        <input type="hidden"
                            id="medical_history-start-date-<?= $row_count; ?>"
                            class="js-start-date"
                            name="<?= $field_prefix ?>[start_date]"
                            value="<?= $start_date_display ?? '' ?>"
                        >
                    </fieldset>
                </div>
            </span>
            <span class="end-date-column" id="<?= $model_name . "_entries_" . $row_count . "_end_date_error" ?>">

                <div class="alternative-display">
                    <div class="alternative-display-element textual">
                        <a class="js-meds-stop-btn" id="<?= $model_name . "_entries_" . $row_count . "_stopped_button" ?>" data-row_count="<?= $row_count ?>" href="javascript:void(0); " 
                            <?php if ($entry->hasErrors() && ($entry->end_date || $entry->stop_reason_id)) {
                                ?> style="display: none;" <?php
                            } ?>>
                            <?php if (!is_null($entry->end_date)) : ?>
                                <i class="oe-i stop small pad"></i>
                                <?= Helper::formatFuzzyDate($end_sel_year . '-' . $end_sel_month . '-' . $end_sel_day) ?>
                                <?php /* echo !is_null($entry->stop_reason_id) ?
                            ' ('.$entry->stopReason->name.')' : ''; */ ?>
                            <?php else : ?>
                                <span><button type="button"><i class="oe-i stop small pad-right"></i>Click here to stop</button></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <fieldset <?php if (!($entry->hasErrors() && ($entry->end_date || $entry->stop_reason_id))) {
                        ?> style="display: none;" <?php
                              } ?> class="js-datepicker-wrapper js-end-date-wrapper">
                        <i class="oe-i stop small pad"></i>
                        <input
                            id="<?= $model_name ?>_entries_<?= $row_count ?>_end_date"
                            class="medical-history-date"
                            <?= $stopped ? 'disabled="disabled"' : ''?>
                            value="<?= $end_date_display ?? Helper::convertDate2NHS($end_date_display) ?>"
                            data-default="<?= date('d M Y') ?>"
                            style="width:80px"
                            placeholder="dd-Mth-yyyy"
                            autocomplete="off"
                            data-pmu-format="d b Y"
                            data-hidden-input-selector="#medical_history-stop-date-<?= $row_count; ?>"
                        >
                        <input type="hidden"
                               id="medical_history-stop-date-<?= $row_count; ?>"
                               class="js-end-date"
                               name="<?= $field_prefix ?>[end_date]"
                               value="<?= $end_date_display ?>"
                        >
                    </fieldset>
                </div>
            </span>
            <span id="<?= $model_name . "_entries_" . $row_count . "_stop_reason_id_error" ?>" class="js-stop-reason-select cols-5" style="<?= ((!($stop_fields_validation_error || $entry->hasErrors()) && ($entry->end_date)) || (($is_new || $entry->hasErrors()) && !($entry->end_date || $entry->stop_reason_id)) || (!$is_new && !$entry->end_date)) ? "display:none" : "" ?>">
                <?php $stop_reason_layout = ['empty' => 'Reason stopped?', 'class' => ' js-stop-reason'];
                if ($stopped) {
                    $stop_reason_layout['disabled'] = 'disabled';
                } ?>
                <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, $stop_reason_layout) ?>
            </span>
            <div class="js-stop-reason-text" style="<?= ((!$stop_fields_validation_error && !$entry->end_date) || (!$entry->hasErrors() && $entry->end_date)) ? "" : "display:none" ?>">
                <?= !is_null($entry->stop_reason_id) ? '&nbsp;<em class="fade">(' . $entry->stopReason->name . ')</em>' : ''; ?>
            </div>
        </div>
    </td>
    <td>
    </td>
    <td>

    </td>
    <td>

    </td>
</tr>