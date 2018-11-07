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
                    <th><?= \CHtml::encode($element->getAttributeLabel('conjunctival_flap_type_id')) ?>:</th>
                    <td><?= $element->conjunctival_flap_type->name ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('stay_suture')) ?>:</th>
                    <td><?= \CHtml::encode($element->stay_suture ? 'Yes' : 'No') ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('site_id')) ?>:</th>
                    <td><?= \CHtml::encode($element->site->name) ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('size_id')) ?>:</th>
                    <td><?= $element->size->name ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('sclerostomy_type_id')) ?>:</th>
                    <td><?= $element->sclerostomy_type->name ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('viscoelastic_type_id')) ?>:</th>
                    <td><?= $element->viscoelastic_type->name ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('viscoelastic_flow_id')) ?>:</th>
                    <td><?= $element->viscoelastic_removed ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('viscoelastic_removed')) ?>:</th>
                    <td><?= $element->viscoelastic_flow->name ?></td>
                </tr>
                <tr>
                    <th>Details</th>
                    <td>
                        <ul>
                            <?php foreach (explode(chr(10), CHtml::encode($element->report)) as $line) { ?>
                                <li><?php echo $line ?></li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <th>Difficulties</th>
                    <td>
                        <?php if (!$element->difficulties) { ?>
                                None
                        <?php } else { ?>
                            <?php foreach ($element->difficulties as $difficulty) { ?>
                                <?php if ($difficulty->name == 'Other') { ?>
                                    <?php echo str_replace("\n", '<br/>', $element->difficulty_other) ?>
                                <?php } else { ?>
                                    <?php echo $difficulty->name ?><br>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
                 <tr>
                    <th>Complications</th>
                    <td>
                        <?php if (!$element->complications) { ?>
                            None
                        <?php } else { ?>
                            <ul>
                                <?php foreach ($element->complications as $complication) { ?>
                                    <li>
                                        <?php if ($complication->name == 'Other') { ?>
                                            <?php echo $element->complication_other ?>
                                        <?php } else { ?>
                                            <?php echo $complication->name ?>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="cols-6">
            <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                'idSuffix' => 'Trabeculectomy',
                'side' => $element->eye->getShortName(),
                'mode' => 'view',
                'width' => 250,
                'height' => 250,
                'scale' => 0.72,
                'model' => $element,
                'attribute' => 'eyedraw',
            ))
            ?>
        </div>
    </div>
</section>
