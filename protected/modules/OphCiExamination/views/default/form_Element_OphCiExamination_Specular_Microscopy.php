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
<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
<div class="element-fields flex-layout full-width">
  <div>
    <label><?php echo $element->getAttributeLabel('specular_microscope_id') ?>:</label>
        <?php
        $allSpecularMicroscope = \OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope::model()->findAll(array('order' => 'display_order'));
        echo CHtml::dropDownList(
            'OEModule_OphCiExamination_models_Element_OphCiExamination_Specular_Microscopy[specular_microscope_id]',
            $element->specular_microscope_id,
            CHtml::listData($allSpecularMicroscope, 'id', 'name'),
            array('class' => 'MultiSelectList')
        ); ?>
    <label><?php echo $element->getAttributeLabel('scan_quality_id') ?>:</label>
        <?php
        $allScanQuality = \OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality::model()->findAll(array('order' => 'display_order'));
        echo CHtml::dropDownList(
            'OEModule_OphCiExamination_models_Element_OphCiExamination_Specular_Microscopy[scan_quality_id]',
            $element->scan_quality_id,
            CHtml::listData($allScanQuality, 'id', 'name'),
            array('class' => 'MultiSelectList')
        ); ?>
  </div>
</div>
<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?>" data-side="<?= $eye_side ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? "display: none;" : "" ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <table class="cols-full">
            <tbody>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye_side . '_endothelial_cell_density_value') ?>:</label>
              </td>
              <td>
                  <?= $form->textField(
                      $element,
                      $eye_side . "_endothelial_cell_density_value",
                      array('nowrapper' => true, 'size' => 12, 'maxlength' => 4)
                  ) ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?php echo $element->getAttributeLabel($eye_side . '_coefficient_variation_value') ?>:</label>
              </td>
              <td>
                  <?= $form->textField(
                      $element,
                      $eye_side . "_coefficient_variation_value",
                      array('nowrapper' => true, 'size' => 12, 'maxlength' => 6)
                  ) ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? "display: none;" : "" ?>">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>