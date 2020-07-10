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
<div class="element-fields full-width flex-layout flex-top col-gap">
    <?php echo $form->hiddenInput($element, 'consultant_id') ?>
  <div class="cols-6">
    <table class="cols-full">
      <tbody>
      <tr>
        <td>Consultant:</td>
        <td>
            <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
            <?=\CHtml::textField(
                'Consultant',
                $element->consultant ? $element->consultant->fullNameAndTitleAndQualifications : '',
                array(
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'readonly' => 'readonly',
                    'class' => 'cols-full' . ($element->hasErrors('consultant_id') ? ' highlighted-error error' : ''),
                )
            ) ?>
        </td>
        <td></td>
      </tr>
      </tbody>
    </table>
  </div>

  <div class="cols-6">
    <fieldset>
        <?php echo $form->checkBox($element, 'anaesthetic_leaflet', array('nowrapper' => true)) ?>
        <?php echo $form->checkBox($element, 'witness_required', array('nowrapper' => true)) ?>
        <?php $hideWitnessName = (!@$_POST['Element_OphTrConsent_Other']['witness_required'] && !$element->witness_name); ?>
        <?php echo $form->textField(
            $element,
            'witness_name',
            array(
                'autocomplete' => Yii::app()->params['html_autocomplete'],
                'size' => '30',
                'maxLength' => '255',
                'hide' => $hideWitnessName,
            ),
            array(),
            array_merge($form->layoutColumns, array('label' => 4, 'field' => 6))
        ); ?>
        <?php echo $form->checkBox($element, 'interpreter_required', array('nowrapper' => true)) ?>
        <?php $hideInterpreterName = (!@$_POST['Element_OphTrConsent_Other']['interpreter_required'] && !$element->interpreter_name); ?>
        <?php echo $form->textField(
            $element,
            'interpreter_name',
            array(
                'autocomplete' => Yii::app()->params['html_autocomplete'],
                'size' => '30',
                'maxLength' => '255',
                'hide' => $hideInterpreterName,
            ),
            array(),
            array_merge($form->layoutColumns, array('label' => 4, 'field' => 6))
        ) ?>
        <?php $hideGuardian = $element->isAdult(); ?>
        <?php echo $form->textField(
            $element,
            'parent_guardian',
            array(
                'autocomplete' => Yii::app()->params['html_autocomplete'],
                'size' => '30',
                'maxlength' => '255',
                'hide' => $hideGuardian,
            ),
            array(),
            array_merge($form->layoutColumns, array('label' => 4, 'field' => 6))
        ) ?>
        <?php echo $form->checkBox($element, 'include_supplementary_consent', array('nowrapper' => true)) ?>
    </fieldset>
  </div>
</div>
