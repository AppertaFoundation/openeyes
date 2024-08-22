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
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
$event_date = null;
if ($event = $element->event) {
    $event_date = $event->created_date;
}
$hide_fluid = true;
if (@$_POST[CHtml::modelName($element)]) {
    if ($_POST[CHtml::modelName($element)][$side . '_dry'] == '0') {
        $hide_fluid = false;
    }
} else {
    if ($element->{$side . '_dry'} === '0') {
        $hide_fluid = false;
    }
}
?>
<table class="cols-full">
  <tbody>
  <tr>
    <td class="flex-layout flex-top" style="height: auto;">
      <label><?php echo $element->getAttributeLabel($side . '_method_id'); ?>:</label>
    </td>
    <td>
        <?php echo $form->dropDownList(
            $element,
            $side . '_method_id',
            '\OEModule\OphCiExamination\models\OphCiExamination_OCT_Method',
            array('nowrapper' => true),
            false,
            array('label' => 9, 'field' => 3)
        ) ?>
    </td>
  </tr>
  <tr>
    <td class="flex-layout flex-top" style="height: auto;">
      <label><?php echo $element->getAttributeLabel($side . '_crt'); ?>:</label>
    </td>
    <td>
        <?php echo $form->textField(
            $element,
            $side . '_crt',
            array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'), 'nowrapper' => true),
            null,
            array()
        ) ?>
      <span class="field-info">&micro;m</span>
      <i class="oe-i small-icon" style="visibility: hidden;"></i>
    </td>
  </tr>
  <tr>
    <td class="flex-layout flex-top" style="height: auto;">
      <label><?php echo $element->getAttributeLabel($side . '_sft'); ?>:</label>
    </td>
    <td>
        <?php $tooltip_content = null;
        if ($past_sft = $exam_api->getOCTSFTHistoryForSide($this->patient, $side, $event_date)) {
            $tooltip_content = "Previous SFT Measurements: <br />";
            foreach ($past_sft as $previous) {
                $tooltip_content .= Helper::convertDate2NHS($previous['date']) . ' - ' . $previous['sft'] . '<br /> ';
            }
        }
        echo $form->textField(
            $element,
            $side . '_sft',
            array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'), 'nowrapper' => true),
            null,
            array()
        ) ?>
      <span class="field-info">&micro;m</span>
      <i class="oe-i info small-icon js-has-tooltip"
         style="<?php if (!$tooltip_content) :
                ?>visibility: hidden;<?php
                endif; ?>"
         data-tooltip-content="<?php echo $tooltip_content; ?>">
      </i>
    </td>
  </tr>
  <tr>
    <td class="flex-layout flex-top" style="height: auto;">
      <label><?php echo $element->getAttributeLabel($side . '_thickness_increase'); ?>:</label>
    </td>
    <td>
        <?php echo $form->radioBoolean(
            $element,
            $side . '_thickness_increase',
            array('nowrapper' => true),
            array('label' => 9, 'field' => 3)
        ) ?>
    </td>
  </tr>
  <tr>
    <td class="flex-layout flex-top" style="height: auto;">
      <label><?php echo $element->getAttributeLabel($side . '_dry'); ?>:</label>
    </td>
    <td>
        <?php echo $form->radioBoolean(
            $element,
            $side . '_dry',
            array('nowrapper' => true),
            array('label' => 9, 'field' => 3)
        ) ?>
    </td>
  </tr>

  <tr id="<?=\CHtml::modelName($element) . '_' . $side; ?>_fluid_fields"
      style="<?php if ($hide_fluid) {
            ?>display: none;<?php
             } ?>"
  >
    <td class="flex-layout flex-top" style="height: auto;">
      <label><?php echo $element->getAttributeLabel($side . '_fluidtypes'); ?>:</label>
    </td>
    <td>
        <?php
        $html_options = array(
            'style' => 'margin-bottom: 10px; width: 240px;',
            'options' => array(),
            'empty' => 'Select',
            'div_id' => CHtml::modelName($element) . '_' . $side . '_fluidtypes',
            'label' => 'Findings',
            'nowrapper' => true,
        );
        $fts = \OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidType::model()->activeOrPk($element->fluidTypeValues)->findAll();
        foreach ($fts as $ft) {
            $html_options['options'][(string)$ft->id] = array('data-order' => $ft->display_order);
        }
        echo $form->multiSelectList(
            $element,
            CHtml::modelName($element) . '[' . $side . '_fluidtypes]',
            $side . '_fluidtypes',
            'id',
            CHtml::listData($fts, 'id', 'name'),
            array(),
            $html_options,
            false,
            false,
            null,
            false,
            false,
            array('label' => 9, 'field' => 3)
        );
        ?>
    </td>
  </tr>
  <tr id="tr_Element_OphCiExamination_OCT_<?= $side ?>_fluidstatus_id"
      style="<?php if ($hide_fluid) {
            ?>display: none;<?php
             } ?>"
  >
    <td class="flex-layout flex-top" style="height: auto;">
      <label><?php echo $element->getAttributeLabel($side . '_fluidstatus_id'); ?>:</label>
    </td>
    <td>
        <?php echo $form->dropDownList(
            $element,
            $side . '_fluidstatus_id',
            '\OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidStatus',
            array('empty' => 'Select', 'nowrapper' => true,),
            false,
            array('label' => 9, 'field' => 3)
        ); ?>
    </td>
  </tr>
  <tr></tr>
  </tbody>
</table>

<div class="flex-layout flex-right comment-group">
      <span class="js-comment-container cols-full flex-layout"
            id="<?= CHtml::modelName($element) . '_' . $side . '_comment_container' ?>"
            style="<?php if (!$element->{$side . '_comments'}) :
                ?>display: none;<?php
                   endif; ?>"
            data-comment-button="#<?= CHtml::modelName($element) . '_' . $side . '_comment_button' ?>">
            <?php echo $form->textArea(
                $element,
                $side . '_comments',
                array('nowrapper' => true),
                false,
                array(
                    'rows' => 1,
                    'class' => 'js-comment-field',
                    'placeholder' => $element->getAttributeLabel($side . '_comments'),
                )
            ) ?>
        <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
      </span>
  <button
      id="<?= CHtml::modelName($element) . '_' . $side . '_comment_button' ?>"
      type="button"
      class="button js-add-comments"
      style="<?php if ($element->{$side . '_comments'}) :
            ?>visibility: hidden;<?php
             endif; ?>"
      data-comment-container="#<?= CHtml::modelName($element) . '_' . $side . '_comment_container' ?>">
    <i class="oe-i comments small-icon"></i>
  </button>
</div>
