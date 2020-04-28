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
<div class="<?= $side . '-eye' ?>" data-side="<?= $side ?>">
  <?php $sides = ['left' => [1,3], 'right' => [2,3]]; ?>
  <?php if (in_array($element->eye_id, $sides[$side])) : ?>
  <div class="data-value">
    <table>
      <tbody>
        <tr>
          <td colspan="2" class="large-text">
            <span class="oe-eye-lat-icons">
              <?php \Yii::app()->controller->widget('EyeLateralityWidget', array('laterality' => $side)); ?>
            </span>
            <span class="tabspace"></span>
            <?= $element->{$side . '_eye_id'} === (string)\OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::FIRST_EYE ? '1st Eye' : '2nd Eye' ?>
          </td>
        </tr>
        <tr>
          <?php $primary_reason_attribute = $side . 'ReasonForSurgery'; ?>
          <td colspan="2" class="priority-text"><?= $element->$primary_reason_attribute ? $element->$primary_reason_attribute->name : '<span class="non">N/A</span>' ?></td>
        </tr>
        <tr>
          <td>
            <?= (string)$element->{$side . '_guarded_prognosis'} === '0' ? '' : 'Guarded prognosis' ?>
          </td>
            <?php if ($element->{$side . '_correction_discussed'} == '1') : ?>
              <td class="large-text">
                  <small class="fade">Refractive target</small>
                <?php $refractive_target_attribute = $side . '_target_postop_refraction'; ?>
                <?= $element->$refractive_target_attribute
                  ? $element->$refractive_target_attribute . ' D'
                  : '<span class="none">Not recorded</span>' ?>
            <?php else : ?>
              <td>
                <?= $element->getCorrectionDiscussed($side) ?>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
    <hr class="divider">
    <div>
        <?php if ($element->{$side . '_notes'}) { ?>
        <i class="oe-i comments-who medium pad-right js-has-tooltip" data-tooltip-content="<?= $element->usermodified ? $element->usermodified->fullName : '' ?>"></i>
        <span class="user-comment"><?= $element->{$side . '_notes'} ?></span>
        <?php } ?>
    </div>
  </div>
  <?php else : ?>
  <div class="data-value not-recorded">
    Not recorded
  </div>
  <?php endif; ?>
</div>
