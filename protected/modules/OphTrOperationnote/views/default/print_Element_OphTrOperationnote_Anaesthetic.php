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
    <table class="borders">
        <tbody>
            <tr>
                    <th><?= \CHtml::encode($element->getAttributeLabel('anaesthetic_type_id')) ?></th>
                <?php if (count($element->anaesthetic_type) > 1 || ( count($element->anaesthetic_type) == 1 && !$element->hasAnaestheticType("GA") && !$element->hasAnaestheticType("NoA"))) { ?>
                    <th><?= \CHtml::encode($element->getAttributeLabel('anaesthetic_delivery_id')) ?></th>
                    <th><?= \CHtml::encode($element->getAttributeLabel('anaesthetist_id')) ?></th>
                    <th><?= \CHtml::encode($element->getAttributeLabel('agents')) ?></th>
                    <th><?= \CHtml::encode($element->getAttributeLabel('complications')) ?></th>
                <?php } ?>
            </tr>
            <tr>
                    <td><?= $element->getAnaestheticTypeDisplay() ?></td>
                <?php if (count($element->anaesthetic_type) > 1 || ( count($element->anaesthetic_type) == 1 && !$element->hasAnaestheticType("GA") && !$element->hasAnaestheticType("NoA"))) { ?>
                    <td>
                        <?php
                            $text = '';
                            foreach ($element->anaesthetic_delivery as $anaesthetic_delivery) {
                                if (!empty($text)) {
                                    $text .= ', ';
                                }
                                $text .= $anaesthetic_delivery->name;
                            }
                            echo $text ? $text : 'None';
                        ?>
                    </td>
                    <td>
                        <?php echo $element->anaesthetist ? $element->anaesthetist->name : 'None' ?>
                    </td>
                    <td>
                        <?php if (!$element->anaesthetic_agents) { ?>
                            None
                        <?php } else { ?>
                            <?php foreach ($element->anaesthetic_agents as $agent) { ?>
                                <?php echo $agent->name ?><br/>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if (!$element->anaesthetic_complications) { ?>
                            None
                        <?php } else { ?>
                            <?php foreach ($element->anaesthetic_complications as $complication) { ?>
                                <?php echo $complication->name ?><br/>
                            <?php } ?>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <th class="cols-3"><?= \CHtml::encode($element->getAttributeLabel('anaesthetic_comment')) ?></th>
                <td class="cols-9"><?php echo Yii::app()->format->Ntext($element->anaesthetic_comment) ?></td>
            </tr>
        </tbody>
    </table>
</section>