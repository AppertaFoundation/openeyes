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

use OEModule\OphCiExamination\models\HistoryMedicationsStopReason; ?>

<?php

/** @var \OEModule\OphCiExamination\models\MedicationManagementEntry $entry */

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
		data-allergy-ids="<?php if(!is_null($entry->medication_id)){
			echo implode(",", array_map(function($e){ return $e->id; }, $entry->medication->allergies));
		}
		else {
			echo "{{& allergy_ids}}";
		}?>"
		class="<?=$field_prefix ?>_row <?= ($is_new || /*$entry->group*/ "new" == 'new') ? " new" : ""?><?= $entry->hidden == 1 ? ' hidden' : '' ?>"
	>

		<td>
			<button class="js-add-taper" type="button" title="Add taper" disabled="disabled" style="<?=$prescribe_hide_style?>">
				<i class="oe-i child-arrow small"></i>
			</button>
			<span class="js-prepended_markup">
            <?php
						if(!is_null($entry->medication_id)) {
							if (isset($patient) && $patient->hasDrugAllergy($entry->medication_id)) {
								echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to '.implode(',',$patient->getPatientDrugAllergy($entry->medication_id)).'"></i>';
							}

							$this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
						}
						else {
							echo "{{& allergy_warning}}";
							echo "{{& prepended_markup}}";
						}
						?>
          </span>
			<span class="js-medication-display">
          <?= is_null($entry->medication_id) ? "{{medication_name}}" : $entry->getMedicationDisplay() ?>
      </span>
			<?php if ($entry->originallyStopped) { ?>
				<i class="oe-i stop small pad"></i>
			<?php } ?>

			<?php /* <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int)$entry->is_copied_from_previous_event; ?>" /> */ ?>
			<input type="hidden" class="rgroup" name="<?= $field_prefix ?>[group]" value="<?= $row_type; ?>" />
			<input type="hidden" class="medication_id" name="<?= $field_prefix ?>[medication_id]" value="<?= $is_new ? "{{medication_id}}" : $entry->medication_id ?>" />
			<input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay() ?>" class="medication-name" />
			<input type="hidden" name="<?= $field_prefix ?>[usage_type]" value="<?= isset($entry->usage_type) ? $entry->usage_type : $usage_type ?>" />
			<input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
			<input type="hidden" name="<?= $field_prefix ?>[hidden]" class="js-hidden" value="<?=$entry->hidden ?>" />
			<input type="hidden" name="<?= $field_prefix ?>[prescription_item_id]" value="<?=$entry->prescription_item_id ?>" />
			<input type="hidden" name="<?= $field_prefix ?>[locked]" value="<?= $locked ?>" class="js-locked" />
		</td>
		<td class="dose-frequency-route">
			<div id="<?= $model_name."_entries_".$row_count."_dfrl_error" ?>">
				<input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" />
				<div class="flex-layout">
					<div class="alternative-display inline">
						<div class="alternative-display-element textual">

								<div class="textual-display">
									<span class="js-textual-display-dose"><?= $entry->dose.' '.$entry->dose_unit_term; ?></span>&nbsp;
									<span class="js-textual-display-frequency"><?= $entry->frequency; ?></span>&nbsp;
									<span class="js-textual-display-route-laterality"><?= ($entry->laterality ? $entry->medicationLaterality->name : ''); ?> <?= (is_null($entry->route_id) ? "" : $entry->route); ?></span>
								</div>
						</div>
						<div class="alternative-display-element" style="display: none;">
							<input class="cols-1 js-dose" style="width: 14%; display: inline-block;"  type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
							<span class="js-dose-unit-term cols-2"><?php echo $entry->dose_unit_term; ?></span>
							<input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" />
							<?php echo CHtml::dropDownList($field_prefix.'[dose_unit_term]', null, $unit_options, array('empty' => '-Unit-', 'disabled'=>'disabled', 'class' => 'js-unit-dropdown cols-2', 'style' => 'display:none')); ?>
							<?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Frequency-', 'class' => 'js-frequency cols-3')) ?>
							<?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Route-', 'class'=>'js-route cols-3')) ?>
							<?php echo CHtml::dropDownList($field_prefix . '[laterality]',
								$entry->laterality,
								$laterality_options,
								array('empty' => '-Laterality-', 'class'=>'admin-route-options laterality cols-3', 'style'=>$entry->routeOptions()?'':'display:none' )); ?>
						</div>
					</div>
				</div>
			</div>
		</td>
		<td>
			<fieldset>
				<input type="hidden" name="<?= $field_prefix ?>[start_date]"
							 value="<?= $entry->start_date ? $entry->start_date : date('Y-m-d') ?>"/>
				<i class="oe-i start small pad"></i>
				<?php if($entry->start_date) {
					echo $entry->getStartDateDisplay();
				} ?>
			</fieldset>
		</td>
		<td class="end-date-column">
			<div class="alternative-display inline">
				<div class="alternative-display-element textual">
						<?php if(!is_null($entry->end_date)) {?>
							<?=Helper::formatFuzzyDate($end_sel_year.'-'.$end_sel_month.'-'.$end_sel_day) ?>
						<?php } ?>
			</div>
		</td>
		<td>
			<?php if(isset($entry->stop_reason_id)) {
			echo HistoryMedicationsStopReason::model()->findByPk($entry->stop_reason_id)->name;
			} ?>
		</td>
		<td>
			<?=DrugDuration::model()->findByPK($entry->duration)->name?>
		</td>
		<td>
			<?= $entry->dispenseCondition->name ?>
		</td>
		<td>
			<?= $entry->dispenseLocation->name ?>
		</td>
		<td>
				<i class="oe-i no-permissions medium-icon js-has-tooltip" data-tooltip-content="You do not have permissions"></i>
				<input type="hidden" name="<?= $field_prefix ?>[prescribe]" value="<?php echo (int)$entry->prescribe; ?>" />
		</td>
		<td>
			<?php $tooltip_content_comes_from_history = "This item comes from medication history. " .
				"If you wish to delete it, it must be deleted from the Medication History element. " .
				"Alternatively, mark this item as stopped."; ?>
			<span data-tooltip-content-comes-from-history="<?= $tooltip_content_comes_from_history ?>">
							read only
 			</span>
		</td>
	</tr>
<?php

if(!empty($entry->tapers)) {
	$tcount = 0;
	foreach ($entry->tapers as $taper) {
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
		$tcount++;
	}
}

?>