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
$no_treatment = $element->{$eye . '_no_treatment'};
$no_treatment_reason = $element->{$eye . '_no_treatment_reason'};
$show_no_treatment_reason_other = false;
if ($no_treatment_reason && $no_treatment_reason->other) {
    $show_no_treatment_reason_other = true;
}
$layoutColumns = array(
    'label' => 3,
    'field' => 9,
);
$el_model_name = CHtml::modelName($element);
?>

<table class="cols-full">
  <tbody class="cols-full">
  <tr class="jsNoTreatment <?php echo $el_model_name ?>_no_treatment">
    <td><label>Treatment:</label></td>
    <td><?php echo $form->checkBox($element, $eye . '_no_treatment', array('nowrapper' => true)) ?></td>
  </tr>
  <tr
      class="row field-row <?php echo $el_model_name ?>_no_treatment_reason_id"
      id="div_<?php echo $el_model_name . '_' . $eye ?>_no_treatment_reason_id"
      style="<?= (!$no_treatment)? "display: none;":""?>"
  >
    <td>
      <label for="<?php echo $el_model_name . '_' . $eye . '_no_treatment_reason_id'; ?>">
          <?php echo $element->getAttributeLabel($eye . '_no_treatment_reason_id') ?>:
      </label>
    </td>
    <td>
        <?=$form->dropDownlist(
            $element,
            $eye . '_no_treatment_reason_id',
            CHtml::listData($no_treatment_reasons, 'id', 'name'),
            $no_treatment_reasons_opts,
            array('nowrapper' => true)
        )?>
    </td>
  </tr>
  <tr class="row field-row <?php echo $el_model_name ?>_no_treatment_reason_other"
      id="div_<?php echo $el_model_name . '_' . $eye ?>_no_treatment_reason_other"
      style="<?=(!$show_no_treatment_reason_other) ? "display: none;":"" ?>"
  >
    <td>
      <label for="<?php echo $el_model_name . '_' . $eye . '_no_treatment_reason_other'; ?>">
          <?php echo $element->getAttributeLabel($eye . '_no_treatment_reason_other') ?>:
      </label>
    </td>
    <td>
        <?php echo $form->textArea($element, $eye . '_no_treatment_reason_other', array('nowrapper' => true)) ?>
    </td>
  </tr>
  <tr></tr>
  </tbody>
</table>
<table class="cols-full">
  <tbody>
  <tr id="div_<?php echo $el_model_name . '_' . $eye ?>_treatment_fields diagnosis_id"

  >
    <td class="flex-top flex-layout" style="height: auto">
      <div >
        <label>Diagnosis:</label>
      </div>
      <div >
          <?php
          $form->widget(
              'application.widgets.DiagnosisSelection',
              array(
                  'field' => $eye . '_diagnosis1_id',
                  'element' => $element,
                  'layout' => 'search',
                  'options' => CHtml::listData($l1_disorders, 'id', 'term'),
                  'default' => false,
                  'nowrapper' => true,
                  'dropdownOptions' => array('empty' => '- Please select -', 'options' => $l1_opts),
                  'layoutColumns' => array('field' => 12),
              )
          ) ?>
      </div>
    </td>
  </tr>
  <tr>
    <td class="<?=(!array_key_exists($element->{$eye . '_diagnosis1_id'}, $l2_disorders))?'hidden':''?> flex-layout flex-top"
        id="<?php echo $eye ?>_diagnosis2_wrapper" style="height: auto"
    >
      <div>
        <label>Associated with:</label>
      </div>
      <div>
          <?php
          $l2_attrs = array('empty' => '- Please select -');
          $l2_opts = array();
          if (array_key_exists($element->{$eye . '_diagnosis1_id'}, $l2_disorders)) {
              $l2_opts = $l2_disorders[$element->{$eye . '_diagnosis1_id'}];
              // this is used in the javascript for checking the second level list is correct.
              $l2_attrs['data-parent_id'] = $element->{$eye . '_diagnosis1_id'};
          } ?>

          <?php $form->widget('application.widgets.DiagnosisSelection', array(
              'field' => $eye . '_diagnosis2_id',
              'element' => $element,
              'options' => CHtml::listData($l2_opts, 'id', 'term'),
              'layout' => 'search',
              'default' => false,
              'dropdownOptions' => $l2_attrs,
              'label' => true,
              'nowrapper' => true,
              'layoutColumns' => array(
                  'field' => 12
              ),
          ))
          ?>

      </div>
    </td>
  </tr>
  <?php $questions = $element->getInjectionQuestionsForSide($eye); ?>
  <tr id="<?=Chtml::modelName($element).'_'.$eye.'_Questions_Parent'?>"
      style="<?= empty($questions)?"display:none;":''?>"
  >
    <td>
      <?php $this->renderPartial(
          $element->form_view . '_questions',
          array(
              'side' => $eye,
              'element' => $element,
              'form' => $form,
              'questions' => $questions
          )
      )?>
    </td><td></td>
  </tr>
  <?php if ($treatments = $element->getInjectionTreatments($eye)):?>
    <tr>
      <td>
          <?= $form->dropDownList(
              $element,
              $eye . '_treatment_id',
              CHtml::listData($treatments, 'id', 'name'),
              array('empty' => '- Please select -'),
              false,
              array('stretch' => true)
          );?>
      </td><td></td>
    </tr>
  <?php endif; ?>
  <tr>
    <td class="flex-layout flex-top" style="height: auto">
      <div class="cols-2">
        <label>Risks</label>
      </div>
      <div class="cols-10">
          <?php
          $html_options = array(
              'options' => array(),
              'empty' => '- Please select -',
              'div_id' => $el_model_name . '_' . $eye . '_risks',
              'nowrapper' => true
          );
          $risks = $element->getRisksForSide($eye);
          foreach ($risks as $risk) {
              $html_options['options'][(string)$risk->id] = array('data-order' => $risk->display_order);
          }
          echo $form->multiSelectList(
              $element,
              $el_model_name . '[' . $eye . '_risks]',
              $eye . '_risks',
              'id',
              CHtml::listData($risks, 'id', 'name'),
              array(),
              $html_options,
              false,
              false,
              null,
              false,
              false,
              array('label' => 3, 'field' => 6)
          );?>
      </div>

    </td><td></td>
  </tr>
  <tr>
    <td>
        <?= $form->textArea(
            $element,
            $eye . '_comments',
            array(),
            false,
            array('placeholder' => 'Enter comments'),
            array('label' => 3, 'field' => 9)
        ) ?>
    </td><td></td>
  </tr>
  </tbody>
</table>