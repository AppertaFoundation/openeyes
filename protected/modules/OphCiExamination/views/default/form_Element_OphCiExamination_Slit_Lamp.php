<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
<div class="element-fields row">
    <div class="large-2 column">
        <label><?php echo $element->getAttributeLabel('allergic_conjunctivitis_id')?>:</label>
    </div>
    <div class="large-2 column">
        <?php
        $allSlitLampConditions = \OEModule\OphCiExamination\models\OphCiExamination_Slit_Lamp_Conditions::model()->findAll(array('order' => 'display_order'));
        echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[allergic_conjunctivitis_id]',
            $element->allergic_conjunctivitis_id,
            CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
    </div>
    <div class="large-2 column"></div>
    <div class="large-2 column">
        <label><?php echo $element->getAttributeLabel('blepharitis_id')?>:</label>
    </div>
    <div class="large-2 column">
        <?php
        echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[blepharitis_id]',
            $element->blepharitis_id,
            CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
    </div>
    <div class="large-2 column"></div>
    <div class="large-2 column">
        <label><?php echo $element->getAttributeLabel('dry_eye_id')?>:</label>
    </div>
    <div class="large-2 column">
        <?php
        echo CHtml::dropDownList('OEModule_OphCiExamination_models_Element_OphCiExamination_Slit_Lamp[dry_eye_id]',
            $element->dry_eye_id,
            CHtml::listData($allSlitLampConditions, 'id', 'name'), array('class' => 'MultiSelectList')); ?>
    </div>
    <div class="large-2 column"></div>
</div>