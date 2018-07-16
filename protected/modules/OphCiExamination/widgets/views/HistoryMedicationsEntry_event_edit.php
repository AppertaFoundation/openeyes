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

if (isset($entry->start_date) && strtotime($entry->start_date)) {
    list($start_sel_year, $start_sel_month, $start_sel_day) = array_pad(explode('-', $entry->start_date), 3,0);
}else {
    $start_sel_day = $start_sel_month = null;
    $start_sel_year = date('Y');
}
if (isset($entry->end_date) && strtotime($entry->end_date)) {
    list($end_sel_year, $end_sel_month, $end_sel_day) = array_pad(explode('-', $entry->end_date), 3,0);
} else {
    $end_sel_day = $end_sel_month = null;
    $end_sel_year = date('Y');
}

$chk_continue =  isset($entry->chk_continue) ? $entry->chk_continue : false;
$chk_prescribe = isset($entry->chk_prescribe) ? $entry->chk_prescribe : ($row_type == "prescribed");
$chk_stop = isset($entry->chk_stop) ? $entry->chk_stop : ($row_type == "closed");
$is_new = isset($is_new) ? $is_new : false;

if(is_object($entry->refMedication) && !$entry->refMedication->will_copy) {
    $ignore = " ignore";
}
else {
    $ignore = "";
}
?>

<tr data-key="<?=$row_count?>" data-event-medication-use-id="<?php echo $entry->id; ?>" class="<?=$field_prefix ?>_row <?= $entry->originallyStopped ? 'originally-stopped' : ''?><?=$is_last ? " divider" : ""?><?= $row_type == 'closed' ? ' fade' : '' ?><?= $ignore ?>">

    <td>
      <span class="medication-display">
        <span class="medication-name">
            <?php if($row_type == 'closed'): ?><i class="oe-i stop small"></i><?php endif; ?>
            <?= $entry->getMedicationDisplay() ?>
        </span>
      </span>
      <?php if ($entry->originallyStopped) { ?>
        <i class="oe-i stop small pad"></i>
      <?php } ?>

        <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int)$entry->is_copied_from_previous_event; ?>" />
        <input type="hidden" class="rgroup" name="<?= $field_prefix ?>[group]" value="<?= $row_type; ?>" />
        <input type="hidden" class="ref_medication_id" name="<?= $field_prefix ?>[ref_medication_id]" value="<?= $entry->ref_medication_id ?>" />
        <input type="hidden" name="<?= $field_prefix ?>[medication_name]" value="<?= $entry->getMedicationDisplay() ?>" class="medication-name" />
        <input type="hidden" name="<?= $field_prefix ?>[usage_type]" value="<?= isset($entry->usage_type) ? $entry->usage_type : $usage_type ?>" />
    </td>
    <td class="dose-frequency-route">
        <div id="<?= $model_name."_entries_".$row_count."_dfrl_error" ?>">
            <input type="hidden" name="<?= $field_prefix ?>[dose_unit_term]" value="<?= $entry->dose_unit_term ?>" class="dose_unit_term" />
            <div class="flex-layout">
                <div class="alternative-display inline">
                    <div class="alternative-display-element textual" <?php if($direct_edit){ echo 'style="display: none;"'; }?>>
                        <a class="textual-display-dose textual-display" href="javascript:void(0);" onclick="switch_alternative(this);">
                            <?= $entry->dose.' '.$entry->dose_unit_term; ?>&nbsp;
                            <?= $entry->frequency; ?>&nbsp;
                            <?= ($entry->laterality ? $entry->refMedicationLaterality->name : ''); ?>&nbsp;
                            <?= (is_null($entry->route_id) ? "" : $entry->route); ?>

                        </a>
                    </div>
                    <div class="alternative-display-element" <?php if(!$direct_edit){ echo 'style="display: none;"'; }?>>
                        <input class="cols-1 dose" style="width: 14%; display: inline-block;" type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
                        <span class="dose-unit-term cols-1"><?php echo $entry->dose_unit_term; ?></span>
                        <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Select-', 'class' => 'frequency cols-4')) ?>
                        <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Select-', 'class'=>'route cols-2')) ?>
                        <?php echo CHtml::dropDownList($field_prefix . '[laterality]',
                            $entry->laterality,
                            $laterality_options,
                            array('empty' => '-Select-', 'class'=>'admin-route-options laterality cols-2', 'style'=>$entry->routeOptions()?'':'display:none' )); ?>
                    </div>
                </div>
                <?php if(!$is_new): ?><button type="button" class="alt-display-trigger small">Change</button><?php endif; ?>
            </div>
        </div>
    </td>
    <td>
        <?php if(!$direct_edit): ?>
            <?php if(!is_null($entry->start_date)): ?>
                <i class="oe-i start small pad"></i>&nbsp;<?=Helper::formatFuzzyDate($entry->start_date) ?>
            <?php endif; ?>
        <?php endif; ?>
        <fieldset class="row field-row fuzzy-date alternative-display-element <?php if(!$direct_edit){ echo 'hidden'; }?>">
            <?php if (!$entry->start_date||$removable) { ?>
            
                
                <?php

                $start_date = is_null($entry->start_date ? $entry->start_date : date('Y-m-d') );

                $start_sel_year = substr($start_date, 0, 4);
                $start_sel_month = substr($start_date, 4, 2);
                $tart_sel_day = substr($start_date, 6, 2);
                ?>

                <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $end_sel_day, 'sel_month' => $end_sel_month, 'sel_year' => $end_sel_year)) ?>
            <?php } else { ?>
                <i class="oe-i start small pad"></i>
                <?=Helper::formatFuzzyDate($entry->start_date) ?>
                <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date ?>" />

            <?php } ?>
            <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
            <input type="hidden" name="<?= $field_prefix ?>[originallyStopped]" value="<?=$entry->originallyStopped ?>" />
    </td>
    <td>
      <?php if($row_type == "closed" && !is_null($entry->end_date)): ?>
          <i class="oe-i stop small pad"></i>
          <?=Helper::formatFuzzyDate($entry->end_date) ?>
            <input type="hidden" name="<?= $field_prefix ?>[end_date]" class="end-date" value="<?= $entry->end_date ?>" />
      <?php else: ?>

          <span class="fuzzy-date end_date_wrapper <?php echo (!$chk_stop || $row_type == "closed" ? ' hidden' : ''); ?>">
              <?php
                $end_sel_year = substr($entry->end_date, 0, 4);
                $end_sel_month = substr($entry->end_date, 4, 2);
                $end_sel_day = substr($entry->end_date, 6, 2);
              ?>
              <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $end_sel_day, 'sel_month' => $end_sel_month, 'sel_year' => $end_sel_year)) ?>
              <?php /*
              <input id="<?= $model_name ?>_datepicker_2_<?=$row_count?>" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" style="width:80px" placeholder="yyyy-mm-dd">
                <i class="oe-i remove-circle small pad-left meds-stop-cancel-btn"></i>
            */ ?>
          </span>
          <?php if($row_type == "closed"): ?>
              <div>
                  <?php echo !is_null($entry->stop_reason_id) ? $entry->stopReason->name : ''; ?>
              </div>
          <?php else: ?>
              <div>
                  <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-?-', 'class'=>'cols-full stop-reason'.(!$chk_stop ? ' hidden' : ''))) ?>
              </div>
          <?php endif; ?>
       <a href="javascript:void(0);" class="meds-stop-btn">stopped?</a>
      <?php endif; ?>
  </td>

    <td class="edit-column">
        <?php if ($removable) { ?>
            <button class="button small warning remove" type="button">remove</button>
        <?php } ?>
    </td>
</tr>


