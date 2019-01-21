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

      <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?>
          <?php if (!$element->hasEye($eye_side)): ?>inactive<?php endif; ?>"
           data-side="<?= $eye_side ?>">
        <div class="active-form data-group flex-layout"
             style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>

          <div class="cols-9">
            <table class="cols-full">
              <thead>
              <tr>
                <th><?php echo $element->getAttributeLabel($eye_side . '_sphere') ?></th>
                <th>
                    <?php echo $element->getAttributeLabel($eye_side . '_cylinder') ?>
                </th>
                <th>
                    <?php echo $element->getAttributeLabel($eye_side . '_axis') ?>
                </th>
                <th></th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td class="cols-2">
                    <?= CHtml::activeTextField($element, $eye_side . '_sphere', array('class' => 'cols-11')) ?>
                </td>
                <td class="cols-2">
                    <?= CHtml::activeTextField($element, $eye_side . '_cylinder', array('class' => 'cols-11')) ?>
                </td>
                <td class="cols-2">
                    <?=\CHtml::activeTextField($element, $eye_side . '_axis',
                        array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'axis cols-11')) ?>
                </td>
                <td class="cols-4">
                    <?=\CHtml::activeDropDownList($element, $eye_side . '_type_id',
                        OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->getOptions(),
                        array('class' => 'refractionType cols-full')) ?>
                </td>
              </tr>
              </tbody>
            </table>

            <div class="refraction-type-other field-row-pad-top"
                <?php if ($element->{$eye_side . '_type'} && $element->{$eye_side . '_type'}->name !== 'Other'): ?>
                  style="display:none"
                <?php endif ?>>
                <?=\CHtml::activeTextField($element, $eye_side . '_type_other',
                    array(
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'placeholder' => 'Other',
                        'class' => 'refraction-type-other-field cols-full',
                    )) ?>
            </div>

            <div id="refraction-<?= $eye_side ?>-comments" class="flex-layout flex-left comment-group js-comment-container"
                 style="<?= !$element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>" data-comment-button="#refraction-<?= $eye_side ?>-comment-button">
                <?=\CHtml::activeTextArea($element, $eye_side . '_notes',
                    array(
                        'rows' => 1,
                        'placeholder' => $element->getAttributeLabel($eye_side . '_notes'),
                        'class' => 'cols-full js-comment-field',
                        'style' => 'overflow-wrap: break-word; height: 24px;',
                    )) ?>
              <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
          </div>

          <div class="add-data-actions flex-item-bottom">

            <button id="refraction-<?= $eye_side ?>-comment-button"
                    class="button js-add-comments" data-comment-container="#refraction-<?= $eye_side ?>-comments"
                    type="button" style="<?= $element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>">
              <i class="oe-i comments small-icon"></i>
            </button>

            <button class="button hint green" type="button" id="add-to-refraction-btn-<?= $eye_side ?>">
              <i class="oe-i plus pro-theme"></i>
            </button>
            <div id="add-to-refraction-<?= $eye_side ?>" class="oe-add-select-search auto-width" style="display: none;">
              <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
              <button class="button hint green add-icon-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
              </button>
              <table class="select-options">
                <thead>
                <tr>
                  <th><?php echo $element->getAttributeLabel($eye_side . '_sphere') ?></th>
                  <th><?php echo $element->getAttributeLabel($eye_side . 'cylinder') ?></th>
                  <th><?php echo $element->getAttributeLabel($eye_side . '_axis') ?></th>
                  <th><?php echo $element->getAttributeLabel($eye_side . '_type') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                  <td>
                    <div class="flex-layout flex-top flex-left">
                      <ul class="add-options sphere-sign">
                        <li data-str="+">
                          <span class="auto-width"><i class="oe-i plus active"></i></span>
                        </li>
                        <li data-str="-">
                          <span class="auto-width"><i class="oe-i minus active"></i></span>
                        </li>
                      </ul>

                      <ul class="add-options sphere-integer">
                          <?php
                          $sign_id = ($element->{$eye_side . '_sphere'} > 0) ? 1 : 2;
                          foreach (\OEModule\OphCiExamination\models\OphCiExamination_Refraction_Sphere_Integer::model()->findAll('sign_id=' . $sign_id) as $integer): ?>
                            <li data-str="<?= $integer->value ?>"><?= $integer->value ?></li>
                          <?php endforeach; ?>
                      </ul>
                      <ul class="add-options sphere-fraction">
                          <?php foreach (OEModule\OphCiExamination\models\OphCiExamination_Refraction_Fraction::model()->findAll() as $fraction): ?>
                            <li data-str="<?= $fraction->value ?>">
                              <span class="auto-width"><?= $fraction->value ?></span>
                            </li>
                          <?php endforeach; ?>
                      </ul>
                    </div>
                  </td>
                  <td>
                    <div class="flex-layout flex-top flex-left">
                      <ul class="add-options cylinder-sign">
                        <li data-str="+">
                          <span class="auto-width"><i class="oe-i plus active"></i></span>
                        </li>
                        <li data-str="-" class="">
                          <span class="auto-width"><i class="oe-i minus active"></i></span>
                        </li>
                      </ul>
                      <ul class="add-options cylinder-integer">
                          <?php
                          $sign_id = ($element->{$eye_side . '_cylinder'} > 0) ? 1 : 2;
                          foreach (\OEModule\OphCiExamination\models\OphCiExamination_Refraction_Cylinder_Integer::model()->findAll('sign_id=' . $sign_id) as $integer): ?>
                            <li data-str="<?= $integer->value ?>"><?= $integer->value ?></li>
                          <?php endforeach; ?>
                      </ul>
                      <ul class="add-options cylinder-fraction">
                          <?php foreach (OEModule\OphCiExamination\models\OphCiExamination_Refraction_Fraction::model()->findAll() as $fraction): ?>
                            <li data-str="<?= $fraction->value ?>">
                              <span class="auto-width"><?= $fraction->value ?></span>
                            </li>
                          <?php endforeach; ?>
                      </ul>
                    </div>
                  </td>
                  <td>
                    <div class="flex-layout flex-top flex-left">
                      <ul class="add-options axis">
                          <?php foreach (range(1, 180) as $axis): ?>
                            <li data-str="<?= $axis ?>"><?= $axis ?></li>
                          <?php endforeach; ?>
                      </ul>
                    </div>
                  </td>
                  <td>
                    <div class="flex-layout flex-top flex-left">
                      <ul class="add-options refraction-type" data-multi="false" data-clickadd="false">
                          <?php foreach (OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type::model()->getOptions() as $id => $type) : ?>
                            <li data-str="<?= $id ?>"><span class="restrict-width"><?= $type ?></span></li>
                          <?php endforeach; ?>
                      </ul>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>
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
      <script type="text/javascript">
        function applyRefractionSettings<?= $eye_side ?>() {
          var $popup = $('#add-to-refraction-<?= $eye_side ?>');

          applySegmentedFieldSettings(
            $popup,
            'sphere',
            $('#OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_<?= $eye_side ?>_sphere')
          );

          applySegmentedFieldSettings(
            $popup,
            'cylinder',
            $('#OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_<?= $eye_side ?>_cylinder')
          );

          var axis = $popup.find('.add-options.axis').find('li.selected').data('str');
          if (axis !== null && typeof axis !== 'undefined') {
            $('#OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_<?= $eye_side ?>_axis').val(axis);
          }

          var refraction_type = $popup.find('.add-options.refraction-type').find('li.selected').data('str');
          if (refraction_type !== null) {
              $type = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_<?= $eye_side ?>_type_id');
              $type.val(refraction_type);
              $type.change();
          }
        }

        $(function () {
          setUpAdder(
            $('#add-to-refraction-<?= $eye_side ?>'),
            'single',
            applyRefractionSettings<?= $eye_side ?>,
            $('#add-to-refraction-btn-<?= $eye_side ?>'),
            $('#add-to-refraction-<?= $eye_side ?>').find('.add-icon-btn'),
            $('#add-to-refraction-<?= $eye_side ?>').find('.close-icon-btn, .add-icon-btn'),
          );
        });
      </script>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
  function applySegmentedFieldSettings($popup, type, result_field) {
    var sign = $popup.find('.add-options.' + type + '-sign').find('li.selected').data('str');
    var integer = $popup.find('.add-options.' + type + '-integer').find('li.selected').data('str');
    var fraction = $popup.find('.add-options.' + type + '-fraction').find('li.selected').data('str');

    if (sign != null && integer != null && fraction != null) {
      result_field.val(sign + integer + fraction);
    }
  }
</script>