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
$primary_reason = $element->{$side . '_reason_for_surgery_id'} ?
    $element->{$side . 'ReasonForSurgery'}->name :
    '<span class="none">Please select a primary reason for surgery</span>';

$refractive_attribute = $side . '_target_postop_refraction';
$refractive_category_attribute = $side . '_refraction_category';

$refractive_target = $element->$refractive_attribute
  ? $element->getFormattedTargetRefraction($side)
  : '<span class="none">Not recorded</span>';

$discussed = $element->getCorrectionDiscussed($side);
$guarded_prognosis = (string)$element->{$side . '_guarded_prognosis'} === '0' ? 'No guarded prognosis' : 'Guarded prognosis';
?>

<div class="<?= $side . '-eye ' . ($side === 'left' ? 'right' : 'left') ?> js-element-eye" data-side="<?= $side ?>">
  <div class="active-form" style="<?= $element->hasEye($side)? '': 'display: none;'?>">
    <div class="remove-side">
      <i class="oe-i remove-circle small"></i>
    </div>
    <table class="cols-full last-left">
      <colgroup>
        <col class="cols-3">
      </colgroup>
      <tbody>
        <tr>
          <td>
            <span class="oe-eye-lat-icons">
              <?php \Yii::app()->controller->widget('EyeLateralityWidget', array('laterality' => $side)); ?>
            </span>
          </td>
          <td>
            <fieldset id="csm-side-order-<?= $side ?>">
                <?= \CHtml::activeRadioButtonList(
                    $element,
                    $side . '_eye_id',
                    [
                    \OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::FIRST_EYE => '1st Eye',
                    \OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::SECOND_EYE => '2nd Eye',
                    ],
                    [
                    'separator' => ' ',
                    'labelOptions' => [
                    'class' => 'inline highlight',
                    ],
                    'class' => 'js-csm-eye-radio',
                    'data-side' => $side,
                    ]
                ) ?>
            </fieldset>
          </td>
        </tr>
      </tbody>
    </table>
    <hr class="divider">
    <table class="cols-full last-left">
      <tbody>
        <tr>
          <!-- PRIMARY REASON -->
            <?=\CHtml::activeHiddenField($element, $side . '_reason_for_surgery_id', [
            'id' => $side . '_primary_reason_hidden',
          ])?>
          <td colspan="2" id="<?= $side . '_reason_entry' ?>"><?= $primary_reason ?></td>
        </tr>
        <tr>
          <!-- GUARDED PROGNOSIS -->
            <?=\CHtml::activeHiddenField($element, $side . '_guarded_prognosis', [
            'id' => $side . '_guarded_prognosis_hidden',
          ])?>
          <td id="<?= $side . '_guarded_prognosis_entry' ?>"><?= $guarded_prognosis ?></td>
          <!-- REFRACTIVE CATEGORY -->
            <?=\CHtml::activeHiddenField(
                $element,
                $refractive_category_attribute,
                [
                'id' => $side . '_refraction_category_hidden',
                'class' => 'fixed-width-small'
                ]
            )?>
          <!-- REFRACTIVE TARGET -->
            <?=\CHtml::activeHiddenField(
                $element,
                $refractive_attribute,
                [
                'id' => $side . '_refraction_hidden',
                'class' => 'fixed-width-small'
                ]
            )?>
          <td style="<?= $element->{$side . '_correction_discussed'} == '1' ? '' : 'display:none'?>">
              <span style="float:right;">Refractive target: <span id="<?= $side . '_refraction_entry' ?>" ><?= $refractive_target ?></span></span>
          </td>
          <!-- CORRECTION DISCUSSED -->
            <?=\CHtml::activeHiddenField($element, $side . '_correction_discussed', [
            'id' => $side . '_discussed_hidden',
          ])?>
          <td style="float:right;" id="<?= $side . '_discussed_entry' ?>"><?= $discussed ?></td>
        </tr>
      </tbody>
    </table>
    <div class="flex-layout flex-right">
        <div class="cols-10">
            <div id="csm-<?= $side ?>-comments" class="cols-full comment-group flex-layout flex-left js-comment-container"
                 style="<?= !$element->{$side . '_notes'} ? 'display: none;' : '' ?>"
                 data-comment-button="#csm-<?= $side ?>-comment-button"
            >
                <?=\CHtml::activeTextArea(
                    $element,
                    $side . '_notes',
                    [
                        'rows' => 1,
                        'placeholder' => 'Comments',
                        'class' => 'cols-full js-comment-field',
                        'style' => 'overflow-wrap: break-word; height: 24px;',
                    ]
                )?>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
        </div>
      <div class="add-data-action flex-item-bottom">
        <button id="csm-<?= $side ?>-comment-button"
                class="button js-add-comments"
                data-comment-container="#csm-<?= $side ?>-comments"
                type="button" style="<?= $element->{$side . '_notes'} ? 'visibility: hidden;' : '' ?>"
        >
          <i class="oe-i comments small-icon"></i>
        </button>
        <button class="button hint green js-csm-<?=$side?>-add-btn">
          <i class="oe-i plus pro-theme"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="inactive-form" style="<?= $element->hasEye($side)? 'display: none;': ''?>">
    <div class="add-side">
      <a href="#">Add <?= $side ?> side</a>
    </div>
  </div>
</div>
