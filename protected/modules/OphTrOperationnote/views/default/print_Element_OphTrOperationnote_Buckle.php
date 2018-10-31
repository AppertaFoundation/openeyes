<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<section class="<?php echo $element->elementType->class_name ?>">
    <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
    <div class="flex-layout">
        <table class="cols-6">
            <tbody>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('drainage_type_id')) ?>:</th>
                    <td><?= $element->drainage_type->name ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('drain_haem')) ?>:</th>
                    <td><?= $element->drain_haem ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('deep_suture')) ?>:</th>
                    <td><?= $element->deep_suture ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('report')) ?>:</th>
                    <td><?= \CHtml::encode($element->report) ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('comments')) ?>:</th>
                    <td><?= Yii::app()->format->Ntext($element->comments) ?></td>
            </tbody>
        </table>
        <div class="cols-6 ">
            <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                'idSuffix' => 'Buckle',
                'side' => $element->eye->getShortName(),
                'mode' => 'view',
                'width' => 200,
                'height' => 200,
                'model' => $element,
                'attribute' => 'eyedraw',
            ));
            ?>
        </div>
    </div>
</section>
