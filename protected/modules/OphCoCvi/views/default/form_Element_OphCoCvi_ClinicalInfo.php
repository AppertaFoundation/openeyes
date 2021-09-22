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
if ($this->checkClinicalEditAccess()) { ?>
    <div class="element-fields full-width">
      <div class="data-group">
        <table class="cols-6 last-left">
          <colgroup>
            <col class="cols-5">
            <col class="cols-7">
          </colgroup>
          <tbody>
            <tr>
              <td>
                  <?php echo $element->getAttributeLabel('examination_date')?>
              </td>
              <td>
                  <?php echo $form->datePicker(
                      $element,
                      'examination_date',
                      array('maxDate' => 'today'),
                      array('style' => 'width: 110px;', 'nowrapper'=> true)
                  ) ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex-layout flex-top col-gap">
        <div class="cols-6 data-group">
          <table class="cols-full last-left">
            <colgroup>
              <col class="cols-5">
              <col class="cols-7">
            </colgroup>
            <tbody>
            <tr>
              <td>
                <label><?= $element->getAttributeLabel('is_considered_blind');?></label>
              </td>
              <td>
                  <?php echo $form->radioButtons(
                      $element,
                      'is_considered_blind',
                      array(
                      0 => $element::$NOT_BLIND_STATUS,
                      1 => $element::$BLIND_STATUS,
                      ),
                      $element->is_considered_blind,
                      false,
                      false,
                      false,
                      false,
                      array('nowrapper' => true)
                  ); ?>
              </td>
            </tr>
            <tr>
              <td>
                <label><?= $element->getAttributeLabel('sight_varies_by_light_levels');?></label>
              </td>
              <td>
                  <?php echo $form->radioBoolean($element, 'sight_varies_by_light_levels', array('nowrapper' => true)) ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="cols-6 data-group">
          <table class="cols-full last-left">
            <colgroup>
              <col class="cols-5">
              <col class="cols-7">
            </colgroup>
            <tbody>
              <tr>
                <td>
                  <label><?= $element->getAttributeLabel('low_vision_status_id');?></label>
                </td>
                <td>
                    <?php echo $form->dropDownList(
                        $element,
                        'low_vision_status_id',
                        CHtml::listData(
                            OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_LowVisionStatus::model()->findAll(array('order' => 'display_order asc')),
                            'id',
                            'name'
                        ),
                        array('empty' => 'Select','nowrapper' => true , 'class' => 'cols-full'),
                        false,
                        array('label' => 4, 'field' => 6)
                    ) ?>
                </td>
              </tr>
            <tr>
              <td>
                <label><?= $element->getAttributeLabel('field_of_vision_id');?></label>
              </td>
              <td>
                  <?php echo $form->dropDownList(
                      $element,
                      'field_of_vision_id',
                      CHtml::listData(
                          OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_FieldOfVision::model()->findAll(array('order' => 'display_order asc')),
                          'id',
                          'name'
                      ),
                      array('empty' => 'Select', 'nowrapper' => true , 'class' => 'cols-full'),
                      false,
                      array('label' => 4, 'field' => 6)
                  ) ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    </section>
        <section class="element full edit">
            <header class="element-header">
                <h3 class="element-title">Visual Acuity</h3>
            </header>
        <div class="indent-correct element-eyes element-fields">
          <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
          <div class="js-element-eye <?=$eye_side?>-eye <?=$page_side?>" data-side="<?= $eye_side?>">
            <div class="active-form data-group">
              <table class="cols-full">
                <tbody>
                <tr>
                  <td>
                      <?php $this->widget('EyeLateralityWidget', array('laterality' => $eye_side)) ?>
                  </td>
                  <td>
                      <?php echo $element->getAttributeLabel('unaided_'.$eye_side.'_va'); ?>
                  </td>
                  <td>
                      <?php echo $form->textField(
                          $element,
                          'unaided_'.$eye_side.'_va',
                          array('size' => 5, 'nowrapper' => true)
                      ); ?>
                  </td>
                </tr>
                <tr>
                  <td>
                      <?php $this->widget('EyeLateralityWidget', array('laterality' => $eye_side)) ?>
                  </td>
                  <td><?php echo $element->getAttributeLabel('best_corrected_'.$eye_side.'_va'); ?></td>
                  <td><?php echo $form->textField(
                      $element,
                      'best_corrected_'.$eye_side.'_va',
                      array('size' => 5, 'nowrapper' => true)
                      ); ?>
                  </td>
                </tr>
                <?php if ($eye_side=='right') { ?>
                <tr>
                  <td>
                      <?php $this->widget('EyeLateralityWidget', array('laterality' => 'both')) ?>
                  </td>
                  <td><?php echo $element->getAttributeLabel('best_corrected_binocular_va'); ?></td>
                  <td>
                      <?php echo $form->textField(
                          $element,
                          'best_corrected_binocular_va',
                          array('size' => '10', 'nowrapper' => true),
                          null,
                          array()
                      ) ?>
                  </td>
                </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php } ?>
        </div>
        </section>
      <section class="element full edit">
       <div class="data-group">
        <?php $this->renderPartial('form_Element_OphCoCvi_ClinicalInfo_Disorder_Assignment_Disorders', array(
            'element' => $element,
            'form' => $form,
        ))?>
        <?php echo $form->textArea($element, 'diagnoses_not_covered', [], false, ['class' => 'cols-full'], ['label' => 3]) ?>
      </div>
          <?php
            /* Not closing the section tag as it's going to be closed from the element_container form */ ?>
<?php } else {
    $this->renderPartial('view_Element_OphCoCvi_ClinicalInfo', array('element' => $element));
} ?>
<script type="application/javascript">
    $(document).ready(function() {
        $("input[name^=main_cause_]").click(function() {
            ($(this).prop('checked') === true) ? $(this).prop('value', 1) : $(this).prop('value', 0);
        });
    });
</script>
