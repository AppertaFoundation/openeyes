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
<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
<div class="element-fields flex-layout full-width">
    <div class="large-2 column">
        <label><?php echo $element->getAttributeLabel('diagnosis_id')?>:</label>
    </div>
    <div class="large-2 column">
        <?php
        $allDiagnoses = \OEModule\OphCiExamination\models\OphCiExamination_CXL_Outcome_Diagnosis::model()->findAll(array('order' => 'display_order'));
        echo CHtml::dropDownList(
            'OEModule_OphCiExamination_models_Element_OphCiExamination_CXL_Outcome[diagnosis_id]',
            $element->diagnosis_id,
            CHtml::listData($allDiagnoses, 'id', 'name'),
            array('class' => 'MultiSelectList')
        ); ?>
    </div>
    <div class="large-2 column">
        <label><?php echo $element->getAttributeLabel('outcome_id')?>:</label>
    </div>
    <div class="large-2 column">
        <?php
        $allOutcomes = \OEModule\OphCiExamination\models\OphCiExamination_CXL_Outcome::model()->findAll(array('order' => 'display_order'));
        echo CHtml::dropDownList(
            'OEModule_OphCiExamination_models_Element_OphCiExamination_CXL_Outcome[outcome_id]',
            $element->outcome_id,
            CHtml::listData($allOutcomes, 'id', 'name'),
            array('class' => 'MultiSelectList')
        ); ?>
    </div>
    <div class="large-2 column"></div>
</div>