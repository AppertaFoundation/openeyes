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

$to_be_copied = !$entry->originallyStopped && $entry->medication->getToBeCopiedIntoMedicationManagement();
?>

<tr data-key="<?=$row_count?>"
    style="display: <?= $entry->originallyStopped ? 'none' : ''?>"
    <?php if (!is_null($entry->medication_id)) :
        ?>data-allergy-ids="<?php echo implode(",", array_map(function ($e) {
            return $e->id;

        }, $entry->medication->allergies)); ?>"<?php
    endif; ?>
    class="divider col-gap js-first-row <?=$field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : ''?>" >
    <td class="drug-details" rowspan="2">
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" value="<?=$entry->prescription_item_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[originallyStopped]" value="<?= (int)$entry->originallyStopped ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[usage_type]" value="<?=$entry->usage_type ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[to_be_copied]" class="js-to-be-copied" value="<?php echo (int)$to_be_copied; ?>" />
                <input type="hidden" name="<?= $field_prefix ?>[bound_key]" class="js-bound-key" value="<?= $entry->bound_key ?>">
            <span class="js-prepended_markup">
							<?= $entry->getMedicationDisplay() ?>
            <?php if (!is_null($entry->medication_id)) {
                if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
                    echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to '.implode(',', $patient->getPatientDrugAllergy($entry->medication_id)).'"></i>';
                }
                $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
            } ?>
            </span>

        <input type="hidden" name="<?= $field_prefix ?>[medication_id]" value="<?= $entry->medication_id ?>"/>

    </td>
    <td class="dose-frequency-route">
			<div class="flex-meds-inputs">
            <input type="hidden" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[frequency_id]" value="<?= $entry->frequency_id ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[route_id]" value="<?= $entry->route_id ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[laterality]" value="<?= $entry->laterality ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>"  />
            <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay() ?>" />

            <?= $entry->getAdministrationDisplay() ?>
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
	<tr data-key="<?= $row_count ?>" class="no-line col-gap js-second-row">
    <td class="nowrap">
			<div class="flex-meds-inputs">
				<span>
            <input type="hidden" name="<?= $field_prefix ?>[start_date]" class="js-start-date"
                         value="<?= $entry->start_date ? $entry->start_date : date('Y-m-d') ?>"/>
            <i class="oe-i start small pad"></i>
            <span class="oe-date"><?= Helper::convertMySQL2NHS($entry->start_date) ?>
						</span>
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


			<span id="<?= $model_name . "_entries_" . $row_count . "_stop_reason_id_error" ?>" class="js-stop-reason-select cols-5 "
						style="<?=  is_null($entry->end_date) ? "display:none" : "" ?>">
            <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-?-', 'class' => 'js-stop-reason')) ?>
        </span>
			<div class="js-stop-reason-text" style="<?= is_null($entry->end_date) ? "" : "display:none" ?>">
				<?= !is_null($entry->stop_reason_id) ? $entry->stopReason->name : ''; ?>
			</div>
			</div>
    </td>

</tr>
