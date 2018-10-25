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
    <table>
        <tbody>
            <?php if ($element->membrane_blue) { ?>
            <tr>
                <th><?= \CHtml::encode($element->getAttributeLabel('membrane_blue')) ?>:</th>
                <td>Yes</td>
            </tr>
            <?php } ?>
            <?php if ($element->brilliant_blue) { ?>
            <tr>
                <th><?= \CHtml::encode($element->getAttributeLabel('brilliant_blue')) ?>:</th>
                <td>Yes</td>
            </tr>
            <?php } ?>
            <?php if ($element->other_dye) { ?>
            <tr>
                <th><?= \CHtml::encode($element->getAttributeLabel('other_dye')) ?>:</th>
                <td><?= \CHtml::encode($element->other_dye) ?></td>
            </tr>
            <?php } ?>
            
            <?php if ($element->comments) { ?>
            <tr>
                <th><?= \CHtml::encode($element->getAttributeLabel('comments')) ?>:</th>
                <td><?= Yii::app()->format->Ntext($element->comments) ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
