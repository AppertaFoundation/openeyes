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

<div class="element-fields element-eyes row">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <div class="element-eye right-eye column side left<?php if (!$element->hasRight()) {
        ?> inactive<?php
    }?>" data-side="right">
        <div class="active-form">
            <a href="#" class="icon-remove-side remove-side">Remove side</a>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('right_allergic_conjunctivitis_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    $allSlitLampConditions = \OEModule\OphCiExamination\models\OphCiExamination_Slit_Lamp_Conditions::model()->findAll(array('order' => 'display_order'));
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[right_allergic_conjunctivitis_id]',
                        $element->right_allergic_conjunctivitis_id,
                        CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('right_blepharitis_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[right_blepharitis_id]',
                        $element->right_blepharitis_id,
                        CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('right_dry_eye_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[right_dry_eye_id]',
                        $element->right_dry_eye_id,
                        CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('right_cornea_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    $allSlitLampCornea = \OEModule\OphCiExamination\models\OphCiExamination_Slit_Lamp_Cornea::model()->findAll(array('order' => 'display_order'));
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[right_cornea_id]',
                        $element->right_cornea_id,
                        CHtml::listData($allSlitLampCornea, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>

        </div>
        <div class="inactive-form">
            <div class="add-side">
                <a href="#">
                    Add right side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
    <div class="element-eye left-eye column side right<?php if (!$element->hasLeft()) {
        ?> inactive<?php
    }?>" data-side="left">
        <div class="active-form">
            <a href="#" class="icon-remove-side remove-side">Remove side</a>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('left_allergic_conjunctivitis_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    $allSlitLampConditions = \OEModule\OphCiExamination\models\OphCiExamination_Slit_Lamp_Conditions::model()->findAll(array('order' => 'display_order'));
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[left_allergic_conjunctivitis_id]',
                        $element->left_allergic_conjunctivitis_id,
                        CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('left_blepharitis_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[left_blepharitis_id]',
                        $element->left_blepharitis_id,
                        CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('left_dry_eye_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[left_dry_eye_id]',
                        $element->left_dry_eye_id,
                        CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>

            <div class="row field-row">
                <div class="large-4 column">
                    <label><?php echo $element->getAttributeLabel('left_cornea_id')?>:</label>
                </div>
                <div class="large-4 column">
                    <?php
                    $allSlitLampCornea = \OEModule\OphCiExamination\models\OphCiExamination_Slit_Lamp_Cornea::model()->findAll(array('order' => 'display_order'));
                    echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[left_cornea_id]',
                        $element->left_cornea_id,
                        CHtml::listData($allSlitLampCornea, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
                </div>
                <div class="large-4 column">
                </div>
            </div>
        </div>
        <div class="inactive-form">
            <div class="add-side">
                <a href="#">
                    Add left side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
</div>
