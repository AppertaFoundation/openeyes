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

/** @var EventMedicationUse $entry */

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

$to_be_copied = !$is_new && !$entry->originallyStopped && $entry->medication->getToBeCopiedIntoMedicationManagement();

$is_posting = Yii::app()->request->getIsPostRequest();

?>

<tr data-key="<?=$row_count?>"
    data-event-medication-use-id="<?php echo $entry->id; ?>"
	<?php if(!is_null($entry->medication_id)): ?>data-allergy-ids="<?php echo implode(",", array_map(function($e){ return $e->id; }, $entry->medication->allergies)); ?>"<?php endif; ?>
    class="<?=$field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : ''?><?= $row_type == 'closed' ? ' stopped' : '' ?>" <?= $row_type == 'closed' ? ' style="display:none;"' : '' ?>>

    <td>
        <div class="medication-display">
            <span class="js-prepended_markup">
            <?php if(!is_null($entry->medication_id)) {
				if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
					echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to '.implode(',',$patient->getPatientDrugAllergy($entry->medication_id)).'"></i>';
				}
                $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
            }
            else {
                echo "{{& prepended_markup}}";
            }?>
            </span>
            <?= is_null($entry->medication_id) ? "{{medication_name}}" : $entry->getMedicationDisplay() ?>
        </div>

        <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int)$entry->is_copied_from_previous_event; ?>" />
        <input type="hidden" class="rgroup" name="<?= $field_prefix ?>[group]" value="<?= $row_type; ?>" />
        <input type="hidden" class="medication_id" name="<?= $field_prefix ?>[medication_id]" value="<?= !isset($entry->medication_id) ? "{{medication_id}}" : $entry->medication_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay() ?>" class="medication-name" />
        <input type="hidden" name="<?= $field_prefix ?>[usage_type]" value="<?= isset($entry->usage_type) ? $entry->usage_type : $usage_type ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" value="<?=$entry->prescription_item_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[to_be_copied]" class="js-to-be-copied" value="<?php echo (int)$to_be_copied; ?>" />
    </td>
    <td class="dose-frequency-route">
        <div id="<?= $model_name."_entries_".$row_count."_dfrl_error" ?>">
            <div class="flex-layout">
                <div class="alternative-display inline">
                    <div class="alternative-display-element textual" <?php if($direct_edit){ echo 'style="display: none;"'; }?>>
                        <a class="textual-display-dose textual-display" href="javascript:void(0);" onclick="switch_alternative(this);">
							<?= $entry->getAdministrationDisplay() ?>
                        </a>
                    </div>
                    <div class="alternative-display-element" <?php if(!$direct_edit){ echo 'style="display: none;"'; }?>>
                        <input class="cols-1 js-dose" style="width: 14%; display: inline-block;" type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
                        <span class="js-dose-unit-term cols-2"><?php echo $entry->dose_unit_term; ?></span>
                        <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" />
                        <?php echo CHtml::dropDownList($field_prefix.'[dose_unit_term]', null, $unit_options, array('empty' => '-Unit-', 'disabled'=>'disabled', 'class' => 'js-unit-dropdown cols-2', 'style' => 'display:none')); ?>
                        <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Frequency-', 'class' => 'js-frequency cols-3')) ?>
                        <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Route-', 'class'=>'js-route cols-2')) ?>
                        <?php echo CHtml::dropDownList($field_prefix . '[laterality]',
                            $entry->laterality,
                            $laterality_options,
                            array('empty' => '-Laterality-', 'class'=>'admin-route-options js-laterality cols-2', 'style'=>$entry->routeOptions()?'':'display:none' )); ?>
                    </div>
                </div>
                <?php /* if(!$is_new): ?><button type="button" class="alt-display-trigger small">Change</button><?php endif; */ ?>
            </div>
        </div>
    </td>
    <td>
        <fieldset>
            <i class="oe-i start small pad"></i>
            <?php if($is_new): ?>
                <input id="<?= $model_name ?>_datepicker_2_<?= $row_count ?>" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date ? $entry->start_date : "" ?>"
                       style="width:80px" placeholder="yyyy-mm-dd" class="js-start-date"
                       autocomplete="off">
                <i class="js-has-tooltip oe-i info small pad right"
                   data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
            <?php else: ?>
                <input type="hidden" name="<?= $field_prefix ?>[start_date]" class="js-start-date"
                       value="<?= $entry->start_date ? $entry->start_date : date('Y-m-d') ?>"/>
							<?= $entry->getStartDateDisplay() ?>
            <?php endif; ?>
        </fieldset>
    </td>
    <td class="end-date-column">
        <div class="alternative-display inline">
            <div class="alternative-display-element textual">
                <a class="js-meds-stop-btn" data-row_count="<?= $row_count ?>" href="javascript:void(0);">
                    <?php if(!is_null($entry->end_date)): ?>
                        <?=Helper::formatFuzzyDate($end_sel_year.'-'.$end_sel_month.'-'.$end_sel_day) ?>
                        <?php /* echo !is_null($entry->stop_reason_id) ?
                            ' ('.$entry->stopReason->name.')' : ''; */?>
                    <?php else: ?>
                        stopped?
                    <?php endif; ?>
                </a>
            </div>
            <fieldset style="display: none;" class="js-datepicker-wrapper js-end-date-wrapper">
                <input id="<?= $model_name ?>_datepicker_3_<?= $row_count ?>" class="js-end-date"
                       name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" data-default="<?=date('Y-m-d') ?>"
                       style="width:80px" placeholder="yyyy-mm-dd"
                       autocomplete="off">
                <i class="js-has-tooltip oe-i info small pad right"
                   data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
            </fieldset>
        </div>
  </td>

    <td>
        <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-?-', 'class'=>'js-stop-reason', 'style' => $is_new || is_null($entry->end_date) ? "display:none" : null)) ?>
        <?php /* <a class="meds-stop-cancel-btn" href="javascript:void(0);" onclick="switch_alternative(this);">Cancel</a> */ ?>
    </td>

    <td class="edit-column">
        <?php if ($removable) { ?>
            <i class="oe-i trash js-remove"></i>
        <?php } ?>
    </td>
</tr>


