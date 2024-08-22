<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
// Work out what to show in the form
$show_deferral = false;
$show_deferral_other = false;
$show_treatment = false;
$show_booking_hint = false;
$show_event_hint = false;
$status = null;
$deferralreason = null;

if (@$_POST[$model_name]) {
    $status = \OEModule\OphCiExamination\models\OphCiExamination_Management_Status::model()->findByPk(@$_POST[$model_name][$eye . '_laser_status_id']);

    if ($deferral_id = @$_POST[$model_name][$eye . '_laser_deferralreason_id']) {
        $deferralreason = \OEModule\OphCiExamination\models\OphCiExamination_Management_DeferralReason::model()->findByPk($deferral_id);
    }
} else {
    $status = $element->{$eye . '_laser_status'};
    $deferralreason = $element->{$eye . '_laser_deferralreason'};
}
if ($status) {
    if ($status->deferred) {
        $show_deferral = true;
    } elseif ($status->book) {
        $show_treatment = true;
        $show_booking_hint = true;
    } elseif ($status->event) {
        $show_treatment = true;
        $show_event_hint = true;
    }
}
if ($deferralreason && $deferralreason->other) {
    $show_deferral_other = true;
}

$model_name = CHtml::modelName($element);
?>
<table class="cols-full">
  <tbody>
    <tr>
      <td>
        <div id="div_<?= $model_name . '_' . $eye; ?>_laser" class="flex-layout">
          <div class="column">
            <label for="<?=$model_name . '_' . $eye . '_laser_status_id';?>">
                <?=$element->getAttributeLabel($eye . '_laser_status_id') ?>:
            </label>
          </div>
          <div class="column">
                <?= CHtml::activeDropDownList(
                    $element,
                    $eye . '_laser_status_id',
                    CHtml::listData($statuses, 'id', 'name'),
                    $status_options
                )?>
            <span id="<?=$eye?>_laser_booking_hint"
                  class="field-info hint"
                  style="<?= (!$show_booking_hint) ? "style=display:none;" : ""?>">
            </span>
          </div>
        </div>
      </td>
    </tr>
    <tr id="<?php echo $eye ?>_laser_event_hint" style="<?= (!$show_event_hint) ? "display:none;" : ""?>">
      <td style="text-align: left">
        <div class="cols-full row column">
          <div class="cols-full">
                <?php if (Yii::app()->hasModule('OphTrLaser')) :
                    $event = EventType::model()->find("class_name = 'OphTrLaser'");?>
                <span class="field-info hint">
                  Ensure a <?=$event->name?> event is added for this patient when procedure is completed
                </span>
                <?php endif?>
          </div>
        </div>
      </td>
    </tr>
    <tr id="div_<?php echo $model_name . '_' . $eye; ?>_laser_deferralreason"
        style="<?= (!$show_deferral) ? "display: none" : ""?>"
    >
      <td>
        <div class="data-group flex-layout flex-top">
          <div class="column">
            <label for="<?php echo $model_name . '_' . $eye . '_laser_deferralreason_id';?>">
                <?php echo $element->getAttributeLabel($eye . '_laser_deferralreason_id')?>:
            </label>
          </div>
          <div class="column end">
                <?=\CHtml::activeDropDownList($element, $eye . '_laser_deferralreason_id', CHtml::listData($deferrals, 'id', 'name'), $deferral_options)?>
            <div class="cols-full"
                 id="div_<?php echo $model_name . '_' . $eye; ?>_laser_deferralreason_other"
                 style="<?= (!$show_deferral_other) ? "display: none" : ""?>"
            >
                <?= $form->textArea(
                    $element,
                    $eye . '_laser_deferralreason_other',
                    array('rows' => '1', 'cols' => '40', 'class' => 'autosize', 'nowrapper' => true)
                )
                        ?>
            </div>
          </div>
        </div>

      </td>
    </tr>
  </tbody>
  <tbody id="<?php echo $model_name . '_' . $eye;?>_treatment_fields"
        style="<?= (!$show_treatment) ? "display: none" : ""?>" >
    <tr>
      <td>
        <div class="flex-layout cols-full">
          <div class="column">
            <label for="<?php echo $model_name . '_' . $eye . '_lasertype_id';?>">
                <?php echo $element->getAttributeLabel($eye . '_lasertype_id'); ?>:
            </label>
          </div>
          <div class="column end lasertype">
                <?=\CHtml::activeDropDownList(
                    $element,
                    $eye . '_lasertype_id',
                    CHtml::listData($lasertypes, 'id', 'name'),
                    array('options' => $lasertype_options, 'empty' => 'Select')
                )?>
          </div>
        </div>
      </td>
    </tr>
    <?php
    $show_other = false;
    if (@$_POST[$model_name]) {
        if (
            $lasertype = \OEModule\OphCiExamination\models\OphCiExamination_LaserManagement_LaserType::model()->findByPk(
                (int) @$_POST[$model_name][$eye . '_lasertype_id']
            )
        ) {
            $show_other = $lasertype->other;
        }
    } else {
        if ($lasertype = $element->{$eye . '_lasertype'}) {
            $show_other = $lasertype->other;
        }
    }
    ?>
    <tr class=" lasertype_other "
        style="<?= (!$show_other) ? 'display: none' : ''?>">
      <td class="flex-layout data-group">
            <div class="column">
              <label for="<?php echo $model_name . '_' . $eye . '_lasertype_other';?>">
                    <?php echo $element->getAttributeLabel($eye . '_lasertype_other'); ?>:
              </label>
            </div>
            <div class="column">
                <?= $form->textField(
                    $element,
                    $eye . '_lasertype_other',
                    array(
                        'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'max' => 120,
                        'nowrapper' => true
                    )
                )?>
            </div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="cols-full comments flex-layout flex-left">
          <div class="cols-3">
            <label for="<?=\CHtml::modelName($element) . "_" . $eye . "_comments"?>">
                <?=\CHtml::encode($element->getAttributeLabel($eye . "_comments")) . ':'?>
            </label>
          </div>
          <div class="cols-9">
                <?= $form->textArea(
                    $element,
                    $eye . '_comments',
                    array('rows' => 1, 'nowrapper' => true),
                    false,
                    array('placeholder' => $element->getAttributeLabel($eye . '_comments')),
                    array('field' => 10, 'label' => 2)
                )?>
          </div>
        </div>
      </td>
      <td></td>
    </tr>
  </tbody>
</table>
