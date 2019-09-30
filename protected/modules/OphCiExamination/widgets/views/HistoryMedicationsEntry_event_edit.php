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

$to_be_copied = !$entry->originallyStopped && isset($entry->medication) && $entry->medication->getToBeCopiedIntoMedicationManagement();

$is_posting = Yii::app()->request->getIsPostRequest();

?>

<tr
    class="divider col-gap js-first-row <?= $field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : '' ?><?= $row_type == 'closed' ? ' stopped' : '' ?><?= $is_new ? "new" : "" ?>"
    data-key="<?= $row_count ?>"
    data-event-medication-use-id="<?php echo $entry->id; ?>"
    <?php if (!is_null($entry->medication_id)) :
        ?>data-allergy-ids="<?php echo implode(",", array_map(function ($e) {
            return $e->id;

        }, $entry->medication->allergies)); ?>"<?php
    endif; ?>

    <?= $row_type == 'closed' ? ' style="display:none;"' : '' ?>>

    <td class="drug-details" rowspan="2">
        <div class="medication-display">
            <?= is_null($entry->medication_id) ? "{{medication_name}}" : $entry->getMedicationDisplay() ?>
            <span class="js-prepended_markup">
            <?php if (!is_null($entry->medication_id)) {
                if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
                    echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' . implode(',', $patient->getPatientDrugAllergy($entry->medication_id)) . '"></i>';
                }
                            $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
            } else {
                echo "{{& prepended_markup}}";
            } ?>
            </span>
        </div>

        <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]"
                     value="<?= (int)$entry->is_copied_from_previous_event; ?>"/>
        <input type="hidden" class="rgroup" name="<?= $field_prefix ?>[group]" value="<?= $row_type; ?>"/>
        <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[medication_id]"
                     value="<?= !isset($entry->medication_id) ? "{{medication_id}}" : $entry->medication_id ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay() ?>"
                     class="medication-name"/>
        <input type="hidden" name="<?= $field_prefix ?>[usage_type]"
                     value="<?= isset($entry->usage_type) ? $entry->usage_type : $usage_type ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" value="<?= $entry->prescription_item_id ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[to_be_copied]" class="js-to-be-copied"
                     value="<?php echo (int)$to_be_copied; ?>"/>
        <input type="hidden" name="<?= $field_prefix ?>[bound_key]" class="js-bound-key"
                     value="<?= !isset($entry->bound_key) && isset($is_template) && $is_template ? "{{bound_key}}" : $entry->bound_key ?>">
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
                    <div class="alternative-display-element textual" <?= $direct_edit || !empty($entry->errors) ? 'style="display: none;"' : '' ?>>
                        <a class="textual-display-dose textual-display hint" href="javascript:void(0);"
                             onclick="switch_alternative(this);">
                            <?php $entry_text_display = $entry->getAdministrationDisplay();
                            echo $entry_text_display != "" ? $entry_text_display : "Add dose/frequency/route"; ?>
                        </a>
                    </div>
                    <div class="alternative-display-element" <?= !$direct_edit && empty($entry->errors) ? 'style="display: none;"' : '' ?>>
                        <input class="fixed-width-small js-dose " type="text" name="<?= $field_prefix ?>[dose]"
                                     value="<?= $entry->dose ?>" placeholder="00"/>
                        <span class="js-dose-unit-term cols-2"><?php echo $entry->dose_unit_term; ?></span>
                        <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>"
                                     class="dose_unit_term" <?= $show_unit ? 'disabled' : '' ?> />
                        <?php echo CHtml::dropDownList($field_prefix . '[dose_unit_term]', null, $unit_options, array('empty' => '-Unit-', 'disabled' => $show_unit ? '' : 'disabled', 'class' => 'js-unit-dropdown cols-2', 'style' => 'display:' . ($show_unit ? '' : 'none'))); ?>
                        <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => 'Frequency', 'class' => 'js-frequency cols-4')) ?>
                        <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => 'Route', 'class' => 'js-route cols-3')) ?>
                        <?php echo CHtml::dropDownList($field_prefix . '[laterality]',
                            $entry->laterality,
                            $laterality_options,
                            array('empty' => '-Laterality-', 'class' => 'admin-route-options js-laterality cols-2', 'style' => $entry->routeOptions() ? '' : 'display:none')); ?>
                    </div>
                </div>
            </div>
            <?php /* if(!$is_new): ?><button type="button" class="alt-display-trigger small">Change</button><?php endif; */ ?>
        </div>
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
                        data-hide-method="display"
                        style="<?php if ($entry->comments) :
                            ?>display: none;<?php
                               endif; ?>"
        >
            <i class="oe-i comments small-icon"></i>
        </button>
    </td>
    <td></td>
    <td class="edit-column">
        <?php if ($removable) { ?>
            <i class="oe-i trash js-remove"></i>
        <?php } ?>
    </td>
</tr>
<tr data-key="<?= $row_count ?>" class="no-line col-gap js-second-row">
    <td class="nowrap">
        <div class="flex-meds-inputs">
                <span id="<?= $model_name . "_entries_" . $row_count . "_start_date_error" ?>">
                    <i class="oe-i start small pad-right"></i>
                        <?php if ($is_new) : ?>
                                                    <input id="<?= $model_name ?>_datepicker_2_<?= $row_count ?>"
                                                                 name="<?= $field_prefix ?>[start_date]"
                                                                 value="<?= $entry->start_date ?>"
                                                                 style="width:80px" placeholder="yyyy-mm-dd" class="js-start-date"
                                                                 autocomplete="off">

                        <?php else : ?>
                                                    <input type="hidden" name="<?= $field_prefix ?>[start_date]" class="js-start-date"
                                                                 value="<?= $entry->start_date ? $entry->start_date : date('Y-m-d') ?>"/>
                                                    <?= $entry->getStartDateDisplay() ?>
                        <?php endif; ?>
                </span>
            <span class="end-date-column" id="<?= $model_name . "_entries_" . $row_count . "_end_date_error" ?>">

                    <div class="alternative-display inline">
            <div class="alternative-display-element textual">
                <a class="js-meds-stop-btn" data-row_count="<?= $row_count ?>" href="javascript:void(0);">
                    <?php if (!is_null($entry->end_date)) : ?>
                                            <i class="oe-i stop small pad"></i>
                                            <?= Helper::formatFuzzyDate($end_sel_year . '-' . $end_sel_month . '-' . $end_sel_day) ?>
                                            <?php /* echo !is_null($entry->stop_reason_id) ?
                            ' ('.$entry->stopReason->name.')' : ''; */ ?>
                    <?php else : ?>
                                            <span><button type="button"><i class="oe-i stop small pad-right"></i> Stopped</button></span>
                    <?php endif; ?>
                </a>
            </div>
            <fieldset style="display: none;" class="js-datepicker-wrapper js-end-date-wrapper">
                            <i class="oe-i stop small pad"></i>
                <input id="<?= $model_name ?>_datepicker_3_<?= $row_count ?>" class="js-end-date"
                                             name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>"
                                             data-default="<?= date('Y-m-d') ?>"
                                             style="width:80px" placeholder="yyyy-mm-dd"
                                             autocomplete="off">
            </fieldset>
        </div>
                </span>


            <span id="<?= $model_name . "_entries_" . $row_count . "_stop_reason_id_error" ?>"
                        class="js-stop-reason-select cols-5"
                        style="<?= $is_new || is_null($entry->end_date) ? "display:none" : "" ?>">
            <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-?-', 'class' => ' js-stop-reason')) ?>
        </span>
            <div class="js-stop-reason-text" style="<?= $is_new || is_null($entry->end_date) ? "" : "display:none" ?>">
                <?= !is_null($entry->stop_reason_id) ? $entry->stopReason->name : ''; ?>
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
