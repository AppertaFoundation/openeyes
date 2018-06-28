<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<script type="text/javascript" src="<?=$this->getJsPublishedPath("SocialHistory.js")?>"></script>
<div class="element-fields flex-layout full-width">
  <table class="cols-10 last-left">
    <colgroup>
      <col class="cols-2">
      <col class="cols-4">
      <col class="cols-2">
      <col class="cols-4">
    </colgroup>
    <tbody>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('occupation_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'occupation_id',
                CHtml::listData($element->occupation_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 4, 'full_dropdown' => true)
            );
            ?>
            <?= $form->textField(
                $element,
                'type_of_job',
                array(
                    'hide' => ($element->occupation_id !== 7),//Hide if the type is not other
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'style' => 'width: 100%',
                    'nowrapper' => true
                ),
                null,
                array('label' => 4, 'field' => 5)
            );
            ?>
        </td>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('driving_statuses'))?>
        </td>
        <td>
          <?= $form->multiSelectList(
                $element,
                CHtml::modelName($element) . '[driving_statuses]',
                'driving_statuses',
                'id',
                CHtml::listData($element->driving_statuses_options, 'id', 'name'),
                array(),
                array('empty' => '- Select -', 'nowrapper' => true),//'label' => $element->getAttributeLabel('driving_statuses')),
                false,
                false,
                null,
                false,
                false, // various attributes we don't care about
                array('stretch' => true)
            ); ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('smoking_status_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'smoking_status_id',
                CHtml::listData($element->smoking_status_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 4, 'full_dropdown' => true)
            );
            ?>
        </td>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('accommodation_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'accommodation_id',
                CHtml::listData($element->accommodation_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 5, 'full_dropdown' => true)
            );
            ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('alcohol_intake'))?>
        </td>
        <td>
            <?= $form->textField(
                $element,
                'alcohol_intake',
                array(
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'append-text' => 'units/week',
                    'style' => 'width: 100%;',
                    'nowrapper' => true
                ),
                null,
                array('label' => 4, 'field' => 4, 'append-text' => 4, 'stretch' => false)
            );
            ?>
        </td>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('carer_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'carer_id',
                CHtml::listData($element->carer_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 8, 'full_dropdown' => true)
            );
            ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('substance_misuse_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'substance_misuse_id',
                CHtml::listData($element->substance_misuse_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 5, 'full_dropdown' => true)
            );?>
        </td>
        <td colspan="2">
            <?= $form->textArea(
                $element,
                'comments',
                array('rows' => '1', 'cols' => '80', 'class' => 'autosize','nowrapper' => true),
                false,
                array('placeholder' => 'Enter comments here'),
                array('label' => 4, 'field' => 8)
            );
            ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>
