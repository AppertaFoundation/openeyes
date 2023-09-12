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

?>

<?php

if (!$entry->end_date && $entry->prescriptionItem && $entry->prescriptionItem->duration_id) {
    $end_date = $entry->prescriptionItem->stopDateFromDuration();
    if ($end_date) {
        $entry->end_date = $end_date->format('Y-m-d');
    }
}

if (isset($entry->start_date) && strtotime($entry->start_date)) {
    list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $entry->start_date);
} else {
    $start_sel_day = $start_sel_month = null;
    $start_sel_year = date('Y');
    $entry->start_date = $start_sel_year . '-00-00'; // default to the year displayed in the select dropdowns
}
if (isset($entry->end_date) && strtotime($entry->end_date)) {
    list($end_sel_year, $end_sel_month, $end_sel_day) = explode('-', $entry->end_date);
} else {
    $end_sel_day = $end_sel_month = null;
    $end_sel_year = date('Y');
}
if ($entry->id && $entry->id !== '' && $entry->latest_med_use_id) {
    $previous_stop_reason_details = $entry->getPreviousStopReason($entry->latest_med_use_id);
}
$entry_is_stopped = $entry->originallyStopped || $stopped;
$to_be_copied = !$entry_is_stopped && $entry->medication->getToBeCopiedIntoMedicationManagement();
?>

<tr data-test="<?= "event-medication-history-row-" . $row_count ?>" data-key="<?=$row_count?>"
    <?php if (!is_null($entry->medication_id)) :
        ?>data-allergy-ids="<?php echo implode(",", array_map(function ($e) {
            return $e->id;
        }, $entry->medication->allergies)); ?>"<?php
    endif; ?>
    class="divider col-gap <?= $stopped ? 'fade' : ''?> js-first-row <?=$field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : ''?>" >
    <td id="<?= $model_name . "_entries_" . $row_count . '_duplicate_error' ?>" class="drug-details" rowspan="2">
        <?php if ($entry->id && $entry->id !== '') { ?>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
        <?php } ?>
        <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" class="js-prescription-item-id" value="<?=$entry->prescription_item_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[originallyStopped]" value="<?= (int)$entry->originallyStopped ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int) $entry->is_copied_from_previous_event; ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[usage_type]" value="<?=$entry->usage_type ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[to_be_copied]" class="js-to-be-copied" value="<?php echo (int)$to_be_copied; ?>" />
        <input type="hidden" name="previous_stop_reason_details" value="<?= $previous_stop_reason_details ?? '' ?>">
        <input type="hidden" name="<?= $field_prefix ?>[bound_key]" class="js-bound-key" value="<?= $entry->bound_key ?>">
        <input type="hidden" name="<?= $field_prefix ?>[stopped_in_event_id]" value="<?= $entry->stopped_in_event_id ?>" />
            <span class="js-prepended_markup" data-test="medication-name">
                            <?= $entry->getMedicationDisplay(true) ?>
            <?php if (!is_null($entry->medication_id)) {
                if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
                    echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' . implode(',', $patient->getPatientDrugAllergy($entry->medication_id)) . '"></i>';
                }
                $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
                echo $entry->renderPGDInfo();
            } ?>
            </span>

        <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[medication_id]" value="<?= $entry->medication_id ?>"/>
        <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[pgdpsd_id]" value="<?= $entry->pgdpsd_id ?>"/>

    </td>
    <td class="dose-frequency-route">
            <div class="flex-meds-inputs">
            <input type="hidden" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[frequency_id]" value="<?= $entry->frequency_id ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[route_id]" value="<?= $entry->route_id ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[laterality]" value="<?= $entry->laterality ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>"  />
            <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay(true) ?>" />

            <?= $entry->getAdministrationDisplay(true) ?>
        </div>
    </td>
        <td>
            <div class="js-comment-container flex-layout flex-left"
                     id="<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
                     style="<?php if (!$entry->comments) :
                            ?>display: none;<?php
                            endif; ?>"
                     data-comment-button="#<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button">
                <?= CHtml::textArea($field_prefix . '[comments]', $entry->comments, [
                    'class' => 'js-comment-field autosize cols-full',
                    'rows' => '1',
                    'placeholder' => 'Comments',
                    'autocomplete' => 'off',
                            ]) ?>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
            <button id="<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button"
                            class="button js-add-comments"
                            data-comment-container="#<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
                            type="button"
                            data-hide-method = "display"
                            style="<?php if ($entry->comments) :
                                ?>display: none;<?php
                                   endif; ?>"
            >
                <i class="oe-i comments small-icon"></i>
            </button>
        </td>
        <td></td>
    <td class="text-center">
        <i class="oe-i info small pad js-has-tooltip" data-tooltip-content=
        "This medication was prescribed through OpenEyes.<?= $entry->prescriptionNotCurrent() ? ' The prescription has been altered since this entry was recorded.' : ''; ?>"></i>
    </td>
</tr>
    <tr data-key="<?= $row_count ?>" class="no-line col-gap js-second-row <?= $stopped ? 'fade' : ''?>">
    <td class="nowrap">
            <div class="flex-meds-inputs">
                <span class="start-date-column" id="<?= $model_name . "_entries_" . $row_count . "_start_date_error" ?>" >
                    <input type="hidden" name="<?= $field_prefix ?>[start_date]" class="js-start-date"
                            value="<?= $entry->start_date ? $entry->start_date : date('Y-m-d') ?>"/>
                    <i class="oe-i start small pad"></i>
                    <span class="oe-date"><?= $entry->getStartDateDisplay() ?></span>
                </span>

                <span data-test="end-date-column" class="end-date-column" id="<?= $model_name . "_entries_" . $row_count . "_end_date_error" ?>">
                    <div class="alternative-display">
                        <div class="alternative-display-element textual">
                            <a class="js-meds-stop-btn" data-row_count="<?= $row_count ?>" href="javascript:void(0);">
                                <?php if (!is_null($entry->end_date)) : ?>
                                                        <i class="oe-i stop small pad"></i>
                                                        <?= Helper::formatFuzzyDate($end_sel_year . '-' . $end_sel_month . '-' . $end_sel_day) ?>
                                <?php else : ?>
                                                        <span><button data-test=<?= "stopped-btn-" . $row_count ?> type="button"><i class="oe-i stop small pad-right"></i> Stopped</button></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <fieldset class="js-datepicker-wrapper js-end-date-wrapper" <?= !($entry->hasErrors('end_date')) ? 'style="display: none;"' : "" ?>>
                                    <i class="oe-i stop small pad"></i>
                            <input id="<?= $model_name ?>_entries_<?= $row_count ?>_end_date" class="js-end-date"
                                        name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>"
                                        data-default="<?= date('Y-m-d') ?>"
                                        style="width:80px" placeholder="yyyy-mm-dd"
                                        autocomplete="off">
                        </fieldset>
                    </div>
                </span>

                <?php if ($entry->end_date) {
                    $entry->setStopReasonTo('Course complete');
                } ?>
                <span id="<?= $model_name . "_entries_" . $row_count . "_stop_reason_id_error" ?>" class="js-stop-reason-select cols-5 "
                            style="<?=  is_null($entry->end_date) ? "display:none" : "" ?>">
                    <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-?-', 'class' => 'js-stop-reason')) ?>
                </span>
                <div class="js-stop-reason-text" style="<?= is_null($entry->end_date) ? "" : "display:none" ?>">
                    <?= !is_null($entry->stop_reason_id) ? '&nbsp;<em class="fade">(' . $entry->stopReason->name . ')</em>' : ''; ?>
                </div>
            </div>
    </td>

</tr>

<script>
    <?php
    if (!$entry->isStopped() && $entry->hasRisk()) { ?>
        if($('.' + OE_MODEL_PREFIX + 'HistoryRisks').length === 0){
            let sidebar = $('#episodes-and-events').data('patient-sidebar');
            sidebar.addElementByTypeClass(OE_MODEL_PREFIX + 'HistoryRisks');
        }
    <?php } ?>
</script>
