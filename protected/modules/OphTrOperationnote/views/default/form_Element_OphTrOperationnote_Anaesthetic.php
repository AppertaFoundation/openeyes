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
$is_hidden = function () use ($element) {
    if (count($element->anaesthetic_type_assignments) == 1 && ($element->anaesthetic_type_assignments[0]->anaesthetic_type->code == 'GA' || $element->anaesthetic_type_assignments[0]->anaesthetic_type->code == 'NoA')) {
        return true;
    }

    return false;
}; ?>

<div class="element-fields full-width flex-layout flex-top" id="OphTrOperationnote_Anaesthetic">
  <div class="cols-11 flex-layout flex-top col-gap">
    <div class="cols-7">
      <table class="last-left">
        <colgroup>
          <col class="cols-2">
        </colgroup>
        <tbody>
        <tr>
          <td>Type</td>
          <td>
              <?php echo $form->checkBoxes($element, 'AnaestheticType', 'anaesthetic_type', null,
                  false, false, false, false, array(), array('field' => 12)); ?>
          </td>
        </tr>
        <tr id="Element_OphTrOperationnote_Anaesthetic_AnaestheticDelivery_container"
            <?php if ($is_hidden()): ?>style="display: none;"<?php endif; ?>>
          <td>LA Delivery Methods</td>
          <td>
            <div>
                <?php echo $form->checkBoxes($element, 'AnaestheticDelivery', 'anaesthetic_delivery', '',
                    false, false, false, false); ?>
            </div>
          </td>
        </tr>
        <tr id="Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_container"
            <?php if ($is_hidden()): ?>style="display: none;"<?php endif; ?>>
          <td>
            Given by:
          </td>
          <td>
            <fieldset id="<?php echo CHtml::modelName($element) . '_anaesthetist_id' ?>">
                <?php echo $form->radioButtons($element, 'anaesthetist_id', 'Anaesthetist', $element->anaesthetist_id,
                    false, false, false, false, array('nowrapper' => true)); ?>

            </fieldset>
          </td>
        </tr>
        <?php if ($element->getSetting('fife')): ?>
          <tr>
            <td>
                <?php echo $element->getAttributeLabel('anaesthetic_witness_id') ?>
            </td>
            <td>
                <?php echo $form->dropDownList($element, 'anaesthetic_witness_id',
                    CHtml::listData($element->surgeons, 'id', 'FullName'),
                    array('empty' => '- Please select -', 'nowrapper' => true),
                    $element->witness_hidden, array('field' => 3)); ?>

            </td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="cols-5">
      <table>
        <colgroup>
          <col class="cols-2">
        </colgroup>
        <tbody>
        <tr>
          <td>Agents</td>
          <td>
              <?php echo $form->multiSelectList(
                  $element,
                  'AnaestheticAgent',
                  'anaesthetic_agents',
                  'id',
                  $this->getAnaesthetic_agent_list($element),
                  null,
                  array('empty' => '- Anaesthetic agents -', 'label' => 'Agents', 'nowrapper' => true),
                  false,
                  false,
                  null,
                  false,
                  false,
                  array('field' => 3)
              ) ?>
          </td>
        </tr>
        <tr>
          <td>Complications</td>
          <td>
              <?php echo $form->multiSelectList(
                  $element,
                  'OphTrOperationnote_AnaestheticComplications',
                  'anaesthetic_complications',
                  'id',
                  CHtml::listData(OphTrOperationnote_AnaestheticComplications::model()->activeOrPk($element->anaestheticComplicationValues)->findAll(),
                      'id', 'name'),
                  array(),
                  array('empty' => '- Complications -', 'label' => 'Complications', 'nowrapper' => true),
                  false,
                  false,
                  null,
                  false,
                  false,
                  array('field' => 3)
              ) ?>
          </td>
        </tr>
        <tr id="Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_container"
            <?php if (!$element->anaesthetic_comment): ?>style="display: none;"<?php endif ?>
            class="comment-group js-comment-container"
            data-comment-button="#Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_button">
          <td>
            Comments
          </td>
          <td>
              <?php echo $form->textArea($element, 'anaesthetic_comment',
                  array('nowrapper' => true), false,
                  array(
                      'rows' => 4,
                      'cols' => 40,
                      'class' => 'js-comment-field',
                  )) ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="flex-item-bottom">
    <button id="Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_button"
            class="button js-add-comments"
            type="button"
            data-comment-container="#Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment_container"
            <?php if ($element->anaesthetic_comment): ?>style="visibility: hidden;"<?php endif; ?>
    >
      <i class="oe-i comments small-icon"></i>
    </button>
  </div>
</div>
