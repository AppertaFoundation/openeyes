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
    <h3 class="element-title highlight"><?php echo $element->elementType->name ?></h3>
    <div class="flex-layout">
        <table class="cols-6">
            <tbody>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('plate_position_id')) ?>:</th>
                    <td><?= $element->plate_position->name ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('plate_limbus')) ?>:</th>
                    <td><?= $element->plate_limbus ?> mm</td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('tube_position_id')) ?>:</th>
                    <td><?= $element->tube_position->name ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('stent')) ?>:</th>
                    <td><?= $element->stent ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('slit')) ?>:</th>
                    <td><?= $element->slit ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('visco_in_ac')) ?>:</th>
                    <td><?= $element->visco_in_ac ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('flow_tested')) ?>:</th>
                    <td><?= $element->flow_tested ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('description')) ?>:</th>
                    <td><?= Yii::app()->format->Ntext($element->description) ?></td>
                </tr>
            </tbody>
        </table>
       
        <div class="cols-6">
            <div class="data-group">
                <div class="details">
                    <?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                        'idSuffix' => 'GlaucomaTube',
                        'mode' => 'view',
                        'width' => 200,
                        'height' => 200,
                        'model' => $element,
                        'attribute' => 'eyedraw',
                        'scale' => 0.72,
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
