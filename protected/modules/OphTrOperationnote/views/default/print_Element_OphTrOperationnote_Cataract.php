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

<section class="<?php echo $element->elementType->class_name ?> data-group">
    <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
    <div class="data-group flex-layout">
        <div class="cols-6">
            <table>
                <tbody>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('incision_site_id')) ?>:</th>
                        <td><?php echo $element->incision_site->name ?></td>
                    </tr>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('length')) ?>:</th>
                        <td><?php echo $element->length ?></td>
                    </tr>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('meridian')) ?>:</th>
                        <td><?php echo $element->meridian ?></td>
                    </tr>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('incision_type_id')) ?>:</th>
                        <td><?php echo $element->incision_type->name ?></td>
                    </tr>
                </tbody>
            </table>
           

            <div class="data-group">
                <h4>Details</h4>
                <div class="details pronounced">
                    <ul>
                        <?php foreach (explode(chr(10), CHtml::encode($element->report)) as $line) { ?>
                            <li><?php echo $line ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="flex-layout">
                <div class="cols-6 text-right">
                    Devices Used:
                </div>
                <div class="cols-6">
                    <?php if (!$element->operative_devices) { ?>
                        None
                    <?php } else { ?>
                        <?php foreach ($element->operative_devices as $device) { ?>
                            <?php echo $device->name ?><br>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <div class="data-group">
                <h4>Per Operative Complications</h4>
                <div class="details">
                    <?php if (!$element->complications && !$element->complication_notes) { ?>
                        <div class="data-value">None</div>
                    <?php } else { ?>
                        <ul>
                            <?php foreach ($element->complications as $complication) { ?>
                                <li><?php echo $complication->name ?></li>
                            <?php } ?>
                        </ul>
                        <?= \CHtml::encode($element->complication_notes) ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="cols-6">
            <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                'idSuffix' => 'Cataract',
                'side' => $element->eye->getShortName(),
                'mode' => 'view',
                'width' => 150,
                'height' => 150,
                'model' => $element,
                'attribute' => 'eyedraw',
            ));
            ?>
              
            <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                'idSuffix' => 'Position',
                'side' => $element->eye->getShortName(),
                'mode' => 'view',
                'width' => 135,
                'height' => 135,
                'model' => $element,
                'attribute' => 'eyedraw2',
            ));
            ?>
            <?= \CHtml::encode($element->report2) ?>
          
            <table class="cols-12">
                <tbody>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('iol_type_id')) ?>:</th>
                        <td><?php echo $element->iol_type ? $element->iol_type->display_name : 'None'; ?></td>
                    </tr>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('iol_power')) ?>:</th>
                        <td><?php echo $element->iol_power; ?></td>
                    </tr>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('predicted_refraction')) ?>:</th>
                        <td><?php echo $element->predicted_refraction; ?></td>
                    </tr>
                    <tr>
                        <th><?= \CHtml::encode($element->getAttributeLabel('pcr_risk')) ?>:</th>
                        <td><?php echo $element->pcr_risk; ?></td>
                    </tr>
                </tbody>
            </table>
         
        </div>
    </div>
</section>
