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

use OEModule\OphCiPhasing\models\OphCiPhasing_Instrument;

$key = 0;
?>
<div class="element-fields element-eyes data-group"
     data-element-type-id="<?php echo $element->elementType->id ?>"
     data-element-type-class="<?php echo $element->elementType->class_name ?>"
     data-element-type-name="<?php echo $element->elementType->name ?>"
     data-element-display-order="<?php echo $element->elementType->display_order ?>">
  <input type="hidden" name="intraocularpressure_readings_valid" value="1"/>
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
  <div class="js-element-eye right-eye column left"
       data-side="right" data-test="phasing-data-column">
    <div class="active-form<?php if (!$element->hasRight()) {
        ?> hidden<?php
                           } ?>">
      <i class="oe-i remove-circle remove-side small"></i>
        <?php echo $form->dropDownList($element, 'right_instrument_id', OphCiPhasing_Instrument::class, array(), false, array('label' => 2, 'field' => 4)) ?>
        <?php echo $form->radioBoolean($element, 'right_dilated', array(), array('label' => 2, 'field' => 10)) ?>
      <fieldset class="data-group">
        <legend class="cols-2 column">
          Readings:
        </legend>
        <div class="cols-10 column">
          <table class="blank">
            <thead>
            <tr>
              <th>Time (HH:MM)</th>
              <th>mm Hg</th>
              <th>
                <div class="hidden">Actions</div>
              </th>
            </tr>
            </thead>
            <tbody class="readings-right">
            <?php
            if ($element->right_readings) {
                foreach ($element->right_readings as $index => $reading) {
                    $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                        'key' => $key,
                        'reading' => $reading,
                        'side' => $reading->side,
                        'no_remove' => ($index == 0),
                    ));
                    ++$key;
                }
            } else {
                $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                    'key' => $key,
                    'side' => 0,
                    'no_remove' => true,
                ));
                ++$key;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
              <td colspan="3" style="text-align: left;">
                <button class="button green small addReading">Add</button>
              </td>
            </tr>
            </tfoot>
          </table>
        </div>
      </fieldset>
        <?php echo $form->textArea($element, 'right_comments', array(), false, array('class' => 'autosize', 'placeholder' => 'Enter comments ...'), array('label' => 2, 'field' => 10)) ?>
    </div>
    <div class="inactive-form" <?= $element->hasRight() ? 'style="display: none;"' : '';?>>
      <div class="add-side">
        <a href="#">
          Add right side <i class="oe-i plus-circle small"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="js-element-eye left-eye column right"
       data-side="left" data-test="phasing-data-column">
    <div class="active-form<?php if (!$element->hasLeft()) {
        ?> hidden<?php
                           } ?>">
      <i class="oe-i remove-circle remove-side small"></i>
        <?php echo $form->dropDownList($element, 'left_instrument_id', OphCiPhasing_Instrument::class, array(), false, array('label' => 2, 'field' => 4)) ?>
        <?php echo $form->radioBoolean($element, 'left_dilated', array(), array('label' => 2, 'field' => 10)) ?>
      <fieldset class="data-group">
        <legend class="cols-2 column">
          Readings:
        </legend>
        <div class="cols-10 column">
          <table class="blank">
            <thead>
            <tr>
              <th>Time (HH:MM)</th>
              <th>mm Hg</th>
              <th>
                <div class="hidden">Actions</div>
              </th>
            </tr>
            </thead>
            <tbody class="readings-left">
            <?php

            if ($element->left_readings) {
                foreach ($element->left_readings as $index => $reading) {
                    $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                        'key' => $key,
                        'reading' => $reading,
                        'side' => $reading->side,
                        'no_remove' => ($index == 0),
                    ));
                    ++$key;
                }
            } else {
                $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                    'key' => $key,
                    'side' => 1,
                    'no_remove' => true,
                ));
                ++$key;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
              <td colspan="3" style="text-align: left;">
                <button class="button green small addReading">Add</button>
              </td>
            </tr>
            </tfoot>
          </table>
        </div>
      </fieldset>
        <?php echo $form->textArea($element, 'left_comments', array(), false, array('class' => 'autosize', 'placeholder' => 'Enter comments ...'), array('label' => 2, 'field' => 10)) ?>
    </div>
    <div class="inactive-form" <?= $element->hasLeft() ? 'style="display: none;"' : '';?>>
      <div class="add-side">
        <a href="#">
          Add left side <i class="oe-i plus-circle small"></i>
        </a>
      </div>
    </div>
  </div>
</div>
<script id="intraocularpressure_reading_template" type="text/html">
    <?php
    $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
        'key' => '{{key}}',
        'side' => '{{side}}',
    ));
    ?>
</script>
