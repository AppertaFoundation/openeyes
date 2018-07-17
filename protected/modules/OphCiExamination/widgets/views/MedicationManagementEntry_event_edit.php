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
    $start_date = $entry->start_date;
}
else {
    $start_date = date('Ymd');
}

$start_sel_year = substr($start_date, 0, 4);
$start_sel_month = substr($start_date, 4, 2);
$start_sel_day = substr($start_date, 6, 2);

if (isset($entry->end_date) && !is_null($entry->end_date)) {

    $end_sel_year = substr($entry->end_date, 0, 4);
    $end_sel_month = substr($entry->end_date, 4, 2);
    $end_sel_day = substr($entry->end_date, 6, 2);

} else {
    $end_sel_day = $end_sel_month = null;
    $end_sel_year = date('Y');
}
$continue =  isset($entry->continue) ? $entry->continue : false;
$prescribe = isset($entry->prescribe) ? $entry->prescribe : ($row_type == "prescribed");
$stop = isset($entry->stop) ? $entry->stop : ($row_type == "closed");
$is_new = isset($is_new) ? $is_new : false;
?>

<tr
    data-key="<?=$row_count?>"
    data-event-medication-use-id="<?php echo $entry->id; ?>"
    class="<?=$field_prefix ?>_row <?= ($is_new || /*$entry->group*/ "new" == 'new') ? " new" : ""?><?= $row_type == 'closed' ? ' fade ignore' : '' ?>"
>

    <td>
      <div class="medication-display alternative-display-inline">
        <div class="medication-name alternative-display-element textual">
            <a class="textual-display" href="javascript:void(0);" onclick="switch_alternative(this);">
                <?php if($row_type == 'closed'): ?><i class="oe-i stop small"></i><?php endif; ?>
                <?= $entry->getMedicationDisplay() ?>
            </a>
        </div>
          <div class="alternative-display-element" <?php if(!$direct_edit){ echo 'style="display: none;"'; }?>>
                <input type="text" class="js-medication-search-autocomplete" id="<?= $field_prefix ?>_medication_autocomplete" placeholder="Type to search" />
          </div>
      </div>
      <?php /* if ($entry->originallyStopped) { ?>
        <i class="oe-i stop small pad"></i>
      <?php } */ ?>

        <?php /* <input type="hidden" name="<?= $field_prefix ?>[is_copied_from_previous_event]" value="<?= (int)$entry->is_copied_from_previous_event; ?>" /> */ ?>
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
                        <a class="textual-display" href="javascript:void(0);" onclick="switch_alternative(this);">
                            <span class="textual-display-dose"><?= $entry->dose.' '.$entry->dose_unit_term; ?></span>&nbsp;
                            <span class="textual-display-frequency"><?= $entry->frequency; ?></span>&nbsp;
                            <span class="textual-display-route-laterality"><?= ($entry->laterality ? $entry->refMedicationLaterality->name : ''); ?> <?= (is_null($entry->route_id) ? "" : $entry->route); ?></span>
                        </a>
                    </div>
                    <div class="alternative-display-element" <?php if(!$direct_edit){ echo 'style="display: none;"'; }?>>
                        <input class="cols-1 dose" style="width: 14%; display: inline-block;"  type="text" name="<?= $field_prefix ?>[dose]" value="<?= $entry->dose ?>" placeholder="Dose" />
                        <span class="dose-unit-term cols-1"><?php echo $entry->dose_unit_term; ?></span>
                        <?= CHtml::dropDownList($field_prefix . '[frequency_id]', $entry->frequency_id, $frequency_options, array('empty' => '-Select-', 'class' => 'frequency cols-3')) ?>
                        <?= CHtml::dropDownList($field_prefix . '[route_id]', $entry->route_id, $route_options, array('empty' => '-Select-', 'class'=>'route cols-3')) ?>
                        <?php echo CHtml::dropDownList($field_prefix . '[laterality]',
                            $entry->laterality,
                            $laterality_options,
                            array('empty' => '-Select-', 'class'=>'admin-route-options laterality cols-3', 'style'=>$entry->routeOptions()?'':'display:none' )); ?>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$entry->id ?>" />
        <div class="alternative-display inline">
            <div class="alternative-display-element textual" <?php if($direct_edit){ echo 'hidden'; }?>>
                <a class="textual-display" href="javascript:void(0);" onclick="switch_alternative(this);">
                    <?=Helper::formatFuzzyDate($start_sel_year.'-'.$start_sel_month.'-'.$start_sel_day) ?>
                </a>
            </div>
            <fieldset class="field-row fuzzy-date alternative-display-element <?php if(!$direct_edit){ echo 'hidden'; }?>">
                <?php if (!$entry->start_date||$removable) { ?>
                    <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?= $start_date ?>" />
                    <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $start_sel_day, 'sel_month' => $start_sel_month, 'sel_year' => $start_sel_year)) ?>
                <?php } else { ?>

                    <?=Helper::formatFuzzyDate($start_sel_year.'-'.$start_sel_month.'-'.$start_sel_day) ?>
                    <input type="hidden" name="<?= $field_prefix ?>[start_date]" value="<?= $entry->start_date ?>" />

                <?php } ?>
            </fieldset>
        </div>
    </td>
    <td>
        <input type="hidden" name="<?= $field_prefix ?>[end_date]" value="<?= $entry->end_date ?>" />
        <div class="alternative-display inline">
            <div class="alternative-display-element textual">
                <a class="textual-display meds-stop-btn" href="javascript:void(0);" onclick="switch_alternative(this);">
                <?php if(!is_null($entry->end_date)): ?>
                    <?=Helper::formatFuzzyDate($end_sel_year.'-'.$end_sel_month.'-'.$end_sel_day) ?><?php echo !is_null($entry->stop_reason_id) ? ' ('.$entry->stopReason->name.')' : ''; ?>
                <?php else: ?>
                    stop
                <?php endif; ?>
                </a>
            </div>
            <div class="alternative-display-element"  <?php if(is_null($entry->end_date)) {echo 'style="display: none;"'; } ?>>
                <span class="fuzzy-date end_date_wrapper" >
                    <?php $this->render('application.views.patient._fuzzy_date_fields', array('sel_day' => $end_sel_day, 'sel_month' => $end_sel_month, 'sel_year' => $end_sel_year)) ?>
                </span>
                <div>
                    <?= CHtml::dropDownList($field_prefix . '[stop_reason_id]', $entry->stop_reason_id, $stop_reason_options, array('empty' => '-?-', 'class'=>'cols-full stop-reason'.(!$stop ? ' hidden' : ''))) ?>
                </div>
            </div>
        </div>
    </td>

    <td>
        <span class="icon-switch btn-continue">
            <input type="checkbox" name="<?= $field_prefix ?>[continue]" <?php echo $entry->continue == 1 ? "checked" : ""; ?> />
        </span>
    </td>

    <td>
         <span class="icon-switch btn-prescribe">
            <input type="checkbox" name="<?= $field_prefix ?>[prescribe]" <?php echo $entry->prescribe == 1 ? "checked" : ""; ?> />
        </span>

    </td>
    <td>
        <?php if ($removable) { ?>
            <button class="button small warning remove" type="button">Remove</button>
        <?php } ?>
    </td>
</tr>
