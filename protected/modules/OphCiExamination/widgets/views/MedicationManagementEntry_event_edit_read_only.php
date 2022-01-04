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

use OEModule\OphCiExamination\models\HistoryMedicationsStopReason;
use OEModule\OphCiExamination\models\MedicationManagementEntry; ?>

<?php

/** @var MedicationManagementEntry $entry */

if (isset($entry->start_date)) {
    $start_date = $entry->start_date;
} else {
    $start_date = date('Y-m-d');
}

list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $start_date);

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
?>

    <tr
        data-key="<?=$row_count?>"
        data-event-medication-use-id="<?php echo $entry->id; ?>"
        data-allergy-ids="<?php if (!is_null($entry->medication_id)) {
            echo implode(",", array_map(function ($e) {
                return $e->id;

            }, $entry->medication->allergies));
                          } else {
                              echo "{{& allergy_ids}}";
                          }?>"
        class="divider col-gap <?=$field_prefix ?>_row <?= ($is_new || /*$entry->group*/ "new" == 'new') ? " new" : ""?><?= $entry->hidden == 1 ? ' hidden' : '' ?>"
    >

        <td class="drug-details" rowspan="2">
            <?= is_null($entry->medication_id) ? "{{medication_name}}" : $entry->getMedicationDisplay(true) ?>
            <span class="js-prepended_markup">
            <?php
            if (!is_null($entry->medication_id)) {
                if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
                    echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to '.implode(',', $patient->getPatientDrugAllergy($entry->medication_id)).'"></i>';
                }

                $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
                echo $entry->renderPGDInfo();
            } else {
                echo "{{& allergy_warning}}";
                echo "{{& prepended_markup}}";
                echo "{{& pgd_info_icon}}";
            }
            ?>
          </span>

            <?php if ($entry->originallyStopped) { ?>
                <i class="oe-i stop small pad"></i>
            <?php } ?>

            <?php /* <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int)$entry->is_copied_from_previous_event; ?>" /> */ ?>
            <input type="hidden" class="rgroup" name="<?= $field_prefix ?>[group]" value="<?= $row_type; ?>" />
            <input type="hidden" class="pgdpsd_id" name="<?= $field_prefix ?>[pgdpsd_id]" value="<?= $is_new ? "{{pgdpsd_id}}" : $entry->pgdpsd_id ?>" />
            <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[medication_id]"
                   value="<?= $is_new ? "{{medication_id}}" : $entry->medication_id ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[medication_name]"
                   value="<?= $entry->getMedicationDisplay(true) ?>" class="medication-name"/>
            <input type="hidden" name="<?= $field_prefix ?>[usage_type]"
                   value="<?= isset($entry->usage_type) ? $entry->usage_type : $usage_type ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
            <input type="hidden" name="<?= $field_prefix ?>[hidden]" class="js-hidden" value="<?=$entry->hidden ?>" />
            <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]"
                   value="<?= $entry->prescription_item_id ?>" class="js-prescription-item-id"/>
            <input type="hidden" name="<?= $field_prefix ?>[locked]" value="<?= $locked ?>" class="js-locked" />
            <input type="hidden" name="<?= $field_prefix ?>[bound_key]" class="js-bound-key" value="<?= $entry->bound_key ?>">
        </td>
        <td class="dose-frequency-route">
            <div id="<?= $model_name."_entries_".$row_count."_dfrl_error" ?>">
                <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" />
                                <div class="textual-display">
                                    <span class="js-textual-display-dose"><?= isset($entry->dose) ? $entry->dose . ' ' .$entry->dose_unit_term : ''; ?></span>&nbsp;
                                    <span class="js-textual-display-frequency"><?= $entry->frequency; ?></span>&nbsp;
                                    <span class="js-textual-display-route-laterality"><?= ($entry->laterality ? $entry->medicationLaterality->name : ''); ?> <?= (is_null($entry->route_id) ? "" : $entry->route); ?></span>
                                </div>
                        </div>
        </td>

        <td>
            <?php $drug_duration = MedicationDuration::model()->findByPk($entry->duration_id); ?>
            <?= isset($drug_duration) ? $drug_duration->name : ""?>

            <?php if ($entry->dispense_condition_id) {
                if ($entry->dispense_condition->name === 'Print to {form_type}') {
                    echo str_replace(
                            '{form_type}',
                            $form_setting,
                            $entry->dispense_condition->name
                        ) . " / {$entry->dispense_location->name}";
                } else {
                    echo $entry->dispense_condition->name . " / " . (isset($entry->dispense_location) ? $entry->dispense_location->name : "");
                }
            } ?>
        </td>
        <td>
                <i class="oe-i no-permissions medium-icon js-has-tooltip" data-tooltip-content="Entries from past examinations cannot be modified. You must start a new Examination event"></i>
                <input type="hidden" name="<?= $field_prefix ?>[prescribe]" value="<?php echo (int)$entry->prescribe; ?>" />
        </td>
        <td>
        </td>
    </tr>
<tr class="no-line col-gap">
    <td class="nowrap">

                    <?php if (!is_null($entry->end_date)) {?>
                        <i class="oe-i stop small pad"></i>
                        <input type="hidden" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date?>"/>
                        <?=Helper::formatFuzzyDate($end_sel_year.'-'.$end_sel_month.'-'.$end_sel_day) ?>
                    <?php } ?>
                </div>
            <?php if (isset($entry->stop_reason_id)) {
                $stop_reason     = HistoryMedicationsStopReason::model()->findByPk($entry->stop_reason_id);
                echo isset($stop_reason) ? '&nbsp;<em class="fade">(' . $stop_reason->name . ')</em>' : "";
            } ?>
        </td>
    <td>
        <?= $entry->comments ?>
    </td>
</tr>
<?php

if (!empty($entry->tapers)) {
    foreach ($entry->tapers as $tcount => $taper) {
        $this->render(
            "MedicationManagementEntryTaper_event_edit_read_only",
            array(
                "element" => $this->element,
                "entry" => $taper,
                "row_count" => $row_count,
                "taper_count" => $tcount,
                "field_prefix" => $model_name."[entries][$row_count][taper][$tcount]"
            )
        );
    }
}
?>
