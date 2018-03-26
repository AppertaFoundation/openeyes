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
<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side): ?>
      <div class="element-eye <?= $eye_side ?>-eye column side <?= $page_side ?>
        <?php if (!$element->hasEye($eye_side)): ?>inactive<?php endif; ?>"
           data-side="<?= $eye_side ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>

          <fieldset class="row field-row">
            <legend class="cols-3 column">
                <?php echo $element->getAttributeLabel($eye_side . '_sphere') ?>:
            </legend>
            <div class="cols-9 column">
                <?php Yii::app()->getController()->renderPartial('_segmented_field', array(
                    'element' => $element,
                    'side' => $eye_side,
                    'field' => 'sphere',
                    'model' => 'OphCiExamination_Refraction_Sphere_Integer',
                ), false, false) ?>
            </div>
          </fieldset>
          <fieldset class="row field-row">
            <legend class="cols-3 column">
                <?php echo $element->getAttributeLabel($eye_side . '_cylinder') ?>:
            </legend>
            <div class="cols-9 column">
                <?php Yii::app()->getController()->renderPartial('_segmented_field', array(
                    'element' => $element,
                    'side' => $eye_side,
                    'field' => 'cylinder',
                    'model' => 'OphCiExamination_Refraction_Cylinder_Integer',
                ), false, false) ?>
            </div>
          </fieldset>
          <div class="row field-row">
            <div class="cols-3 column">
              <label for="<?php echo get_class($element) . '_' . $eye_side . '_axis'; ?>">
                  <?php echo $element->getAttributeLabel($eye_side . '_axis') ?>:
              </label>
            </div>
            <div class="cols-6 column end">
                <?php echo CHtml::activeTextField($element, $eye_side . '_axis',
                    array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'axis')) ?>
            </div>
          </div>
          <div class="refraction-type-container">
            <div class="row field-row">
              <div class="cols-3 column">
                <label for="<?php echo get_class($element) . '_' . $eye_side . '_type_id'; ?>">
                    <?php echo $element->getAttributeLabel($eye_side . '_type_id') ?>:
                </label>
              </div>
              <div class="cols-6 column end">
                <div>
                    <?php echo CHtml::activeDropDownList($element, $eye_side . '_type_id',
                        OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->getOptions(),
                        array('class' => 'refractionType')) ?>
                </div>
              </div>
            </div>
            <div class="row field-row refraction-type-other"
                <?php if ($element->{$eye_side . '_type'} && $element->{$eye_side . '_type'}->name != 'Other'): ?>
                  style="display:none"
                <?php endif ?>>
              <div class="cols-3 column">
                <label>Other:</label>
              </div>
              <div class="cols-6 column end">
                  <?php echo CHtml::activeTextField($element, $eye_side . '_type_other',
                      array(
                          'autocomplete' => Yii::app()->params['html_autocomplete'],
                          'class' => 'refraction-type-other-field',
                      )) ?>
              </div>
            </div>
            <div class="row field-row">
              <div class="cols-9 column end">
                  <?php echo CHtml::activeTextArea($element, $eye_side . '_notes',
                      array('rows' => 1, 'placeholder' => $element->getAttributeLabel($eye_side . '_notes'))) ?>
              </div>
            </div>
          </div>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>
