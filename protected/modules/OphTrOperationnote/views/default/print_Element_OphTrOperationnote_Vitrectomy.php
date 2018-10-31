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
                    <th><?= \CHtml::encode($element->getAttributeLabel('gauge_id')) ?>:</th>
                    <td><?= $element->gauge->value ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('pvd_induced')) ?>:</th>
                    <td><?= $element->pvd_induced ? 'Yes' : 'No' ?></td>
                </tr>
                <?php if ($element->comments) { ?>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('comments')); ?>:</th>
                    <td><?= Yii::app()->format->Ntext($element->comments) ?></td>
            <?php } ?>
            </tbody>
        </table>
        <div class="cols-6 column">
            <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                'idSuffix' => 'Vitrectomy',
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
