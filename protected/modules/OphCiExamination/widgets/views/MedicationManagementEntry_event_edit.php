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
?>

<?php

/** @var \OEModule\OphCiExamination\models\MedicationManagementEntry $entry */

if (isset($entry->start_date) && !is_null($entry->start_date)) {
    $start_date = $entry->start_date_string_YYYYMMDD;
}
else {
    $start_date = date('Ymd');
}

$start_sel_year = substr($start_date, 0, 4);
$start_sel_month = substr($start_date, 4, 2);
$start_sel_day = substr($start_date, 6, 2);

if (isset($entry->end_date_string_YYYYMMDD) && !is_null($entry->end_date_string_YYYYMMDD)) {

    $end_date = $entry->end_date_string_YYYYMMDD;

    $end_sel_year = substr($end_date, 0, 4);
    $end_sel_month = substr($end_date, 4, 2);
    $end_sel_day = substr($end_date, 6, 2);

} else {
    $end_sel_day = date('d');
    $end_sel_month = date('m');
    $end_sel_year = date('Y');
}

$prescribe = isset($entry->prescribe) ? $entry->prescribe : ($row_type == "prescribed");
$stop = isset($entry->stop) ? $entry->stop : ($row_type == "closed");
$is_new = isset($is_new) ? $is_new : false;
?>

<tr
    data-key="<?=$row_count?>"
    data-event-medication-use-id="<?php echo $entry->id; ?>"
    class="<?=$field_prefix ?>_row <?= ($is_new || /*$entry->group*/ "new" == 'new') ? " new" : ""?><?= $entry->hidden == 1 ? ' hidden' : '' ?>"
>

    <td>
        <span class="js-prepended_markup">
          <?php if(!is_null($entry->medication_id)) {
              $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id));
          }
          else {
              echo "{{& prepended_markup}}";
          }?>
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
    </td>
    <td class="dose-frequency-route">
        <div id="<?= $model_name."_entries_".$row_count."_dfrl_error" ?>">
            <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" />
            <div class="flex-layout">
                <div class="alternative-display inline">
                    <div class="alternative-display-element textual" <?php if($direct_edit){ echo 'style="display: none;"'; }?>>
                        <a class="textual-display" href="javascript:void(0);" onclick="switch_alternative(this);">
                            <span class="textual-display-dose"><?= $entry->dose.' '.$entry->dose_unit_term; ?></span>&nbsp;
                            <span class="textual-display-frequency"><?= $entry->frequency; ?></span>&nbsp;
                            <span class="textual-display-route-laterality"><?= ($entry->laterality ? $entry->medicationLaterality->name : ''); ?> <?= (is_null($entry->route_id) ? "" : $entry->route); ?></span>
                        </a>
                    </div>
                    <div class="alternative-display-element" <?php if(!$direct_edit){ echo 'style="display: none;"'; }?>>
                        <input class="cols-1 js-dose" style="width: 14%; display: inline-block;"  type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
                        <span class="js-dose-unit-term cols-1"><?php echo $entry->dose_unit_term; ?></span>
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
                   value="<?= $entry->start_date_string_YYYYMMDD ? $entry->start_date_string_YYYYMMDD : date('Ymd') ?>"/>
            <i class="oe-i start small pad"></i>
            <?php if($is_new): ?>
                <input id="<?= $model_name ?>_datepicker_2_<?= $row_count ?>" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date ? $entry->start_date : date('Y-m-d') ?>"
                       style="width:80px" placeholder="yyyy-mm-dd"
                       autocomplete="off">
                <i class="js-has-tooltip oe-i info small pad right"
                   data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
            <?php else: ?>
                <?= Helper::convertMySQL2NHS($entry->start_date) ?>
            <?php endif; ?>
        </fieldset>
    </td>
    <td class="end-date-column">
        <div class="alternative-display inline">
            <div class="alternative-display-element textual">
                <a class="js-meds-stop-btn" data-row_count="<?= $row_count ?>" href="javascript:void(0);">
                    <?php if(!is_null($entry->end_date)): ?>
                        <?=Helper::formatFuzzyDate($end_sel_year.'-'.$end_sel_month.'-'.$end_sel_day) ?>
                        <?php echo !is_null($entry->stop_reason_id) ?
                            ' ('.$entry->stopReason->name.')' : ''; ?>
                    <?php else: ?>
                        stop
                    <?php endif; ?>
                </a>
            </div>
            <fieldset style="display: none;" class="js-datepicker-wrapper js-end-date-wrapper">
                <input id="<?= $model_name ?>_datepicker_3_<?= $row_count ?>" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" data-default="<?=date('Y-m-d') ?>"
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
    <td>
        <span class="icon-switch js-btn-prescribe <?php if(!$prescribe_access): ?>js-readonly<?php endif; ?>">
            <i  class="oe-i drug-rx js-prescribe js-has-tooltip" data-tooltip-content="Prescribe<?php if(!$prescribe_access): ?> (not allowed)<?php endif; ?>" <?php if($entry->prescribe){ echo 'style="opacity: 1"'; } ?>></i>
            <input type="hidden" name="<?= $field_prefix ?>[prescribe]" value="<?php echo (int)$entry->prescribe; ?>" />
        </span>
        <?php if ($removable) { ?>
            <i class="oe-i trash js-remove"></i>
        <?php } ?>
    </td>
</tr>
