<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<section class="element full priority	view-<?php echo $element->elementType->name ?>"
data-element-type-id="<?php echo $element->elementType->id ?>"
data-element-type-class="<?php echo $element->elementType->class_name ?>"
data-element-type-name="<?php echo $element->elementType->name ?>"
data-element-display-order="<?php echo $element->getDisplayOrder('view') ?>">

<?php if (!preg_match('/\[\-(.*)\-\]/', $element->elementType->name)) { ?>
  <header class=" element-header">
    <h3 class="element-title"><?php echo $element->getViewTitle() ?></h3>
  </header>
<?php } ?>
  <div class="element-data full-width flex-layout flex-top col-gap">
    <div class="cols-3">
      <table class="label-value">
        <tbody>
        <tr>
          <td>
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('consultant_id')) ?>:</div>
          </td>
          <td>
            <div class="data-value"><?php echo $element->consultant ? $element->consultant->last_name : 'None' ?></div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('examination_date')) ?>:</div>
          </td>
          <td>
            <div class="data-value"><?php echo CHtml::encode($element->NHSDate('examination_date')) ?></div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="cols-4">
      <table class="label-value">
        <tbody>
        <tr>
          <td>
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('is_considered_blind')) ?>:
            </div>
          </td>
          <td>
            <div class="data-value"><?php echo $element->displayconsideredblind; ?></div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('sight_varies_by_light_levels')) ?>:</div>
          </td>
          <td>
            <div class="data-value"><?php echo $element->displaylightlevels; ?></div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="cols-5">
      <table>
        <tbody>
        <tr>
          <td>
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('low_vision_status_id')) ?>:</div>
          </td>
          <td>
            <div class="data-value"><?php echo $element->low_vision_status ? $element->low_vision_status->name : 'None' ?></div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('field_of_vision_id')) ?>:</div>
          </td>
          <td>
            <div class="data-value"><?php echo $element->field_of_vision ? $element->field_of_vision->name : 'None' ?></div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
<section class="element full priority eye-divider view-visual-acuity">
    <header class="element-header">
      <h3 class="element-title">Visual Acuity</h3>
    </header>
    <div class="indent-correct element-data element-eyes row">
        <?php foreach(['left' => 'right', 'right' => 'left'] as $page_side => $eye_side){ ?>
          <div class="element-eye <?= $eye_side ?>-eye column">
            <div class="data-row">
              <ul class="dslash-list large-text">
                <li><?php echo CHtml::encode($element->{'unaided_'.$eye_side.'_va'}.' Unaided'); ?></li>
                <li><?php echo CHtml::encode($element->{'best_corrected_'.$eye_side.'_va'}.' Best') ?></li>
                <li><?php echo CHtml::encode($element->best_corrected_binocular_va.' Binocula') ?></li>
              </ul>
            </div>
          </div>
        <?php } ?>
    </div>
</section>
<section class="element full priority">
      <header class="element-header">
        <h3 class="element-title"><?php echo CHtml::encode($element->getAttributeLabel('disorders')) ?>:</h3>
      </header>
      <?php $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders', array(
          'element' => $element,
      ))?>
    <div class="row data-row">
      <div class="cols-4">
        <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('diagnoses_not_covered')) ?>:</div>
      </div>
      <div class="cols-8">
        <div class="data-value"><?php echo CHtml::encode($element->diagnoses_not_covered) ?></div>
      </div>
    </div>
</section>
