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

use OEModule\OphCiExamination\models\OphCiExamination_Scan_Quality;
use OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope;

$all_scan_quality = OphCiExamination_Scan_Quality::model()->findAll(['order' => 'display_order']);
$all_specular_microscope = OphCiExamination_Specular_Microscope::model()->findAll(['order' => 'display_order']);
?>

<?php $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
<div class="element-both-eyes">
  <div class="flex-t">
      <div class="cols-11">
          <div class="flex">
              <label><?=$element->getAttributeLabel('specular_microscope_id') ?>:</label>
              <?= \CHtml::dropDownList(
                  'OEModule_OphCiExamination_models_Element_OphCiExamination_Specular_Microscopy[specular_microscope_id]',
                  $element->specular_microscope_id,
                  \CHtml::listData($all_specular_microscope, 'id', 'name'),
                  array('class' => 'MultiSelectList')
              ); ?>
              <label><?=$element->getAttributeLabel('scan_quality_id') ?>:</label>
              <?= \CHtml::dropDownList(
                  'OEModule_OphCiExamination_models_Element_OphCiExamination_Specular_Microscopy[scan_quality_id]',
                  $element->scan_quality_id,
                  \CHtml::listData($all_scan_quality, 'id', 'name'),
                  ['class' => 'MultiSelectList']
              ); ?>
          </div>
      </div>

  </div>
</div>
<div class="element-fields element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?>" data-side="<?= $eye_side ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? "display: none;" : "" ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <table class="cols-full">
            <tbody>
            <tr>
              <td>
                <label><?=$element->getAttributeLabel($eye_side . '_endothelial_cell_density_value') ?>:</label>
              </td>
              <td>
                  <?php $form->textField($element, $eye_side . "_endothelial_cell_density_value",
                      ['nowrapper' => true, 'size' => 12, 'maxlength' => 4, "data-test" => "$eye_side-endothelial-cell-density-value"]
                  ) ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?=$element->getAttributeLabel($eye_side . '_coefficient_variation_value') ?>:</label>
              </td>
              <td>
                  <?php $form->textField($element, $eye_side . "_coefficient_variation_value",
                      ['nowrapper' => true, 'size' => 12, 'maxlength' => 6, "data-test" => "$eye_side-coefficient-variation-value"]
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