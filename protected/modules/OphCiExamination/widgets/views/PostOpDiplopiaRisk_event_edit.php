<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
* @var \OEModule\OphCiExamination\models\PostOpDiplopiaRisk $element
*/
$model_name = CHtml::modelName($element);
?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_element">
    <div class="cols-7">
        <?php echo $form->textArea($element, 'comments', array('class' => 'cols-full autosize', 'nowrapper' => true), false, array('rows' => 1, 'placeholder' => $element->getAttributeLabel('comments'), 'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;')) ?>
    </div>
    <div class="cols-4">
        <label class="inline">
            <?= $element->getAttributeLabel('at_risk'); ?>
        </label>
        <label class="inline highlight">
            <?=\CHtml::radioButton($model_name . '[at_risk]', $element->at_risk == 1, [
                'value' => '1',
                'id' => "{$model_name}_at_risk_1"
            ]); ?>
            yes
        </label>
        <label class="inline highlight">
            <?=\CHtml::radioButton($model_name . '[at_risk]', $element->at_risk === 0 || $element->at_risk === '0', [
                'value' => '0',
                'id' => "{$model_name}_at_risk_0"
            ]); ?>
            no
        </label>
    </div>
</div>