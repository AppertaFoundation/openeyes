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
    <table class="cols-8">
        <tbody>
            <tr>
                <th><?= CHtml::encode($element->getAttributeLabel('application_type_id')) ?>:</th>
                <td><?= $element->application_type->name ?></td>
            </tr>
            <tr>
                <th><?= CHtml::encode($element->getAttributeLabel('concentration_id')) ?>:</th>
                <td><?= $element->concentration->value ?></td>
            </tr>
            <?php if ($element->application_type_id == OphTrOperationnote_Antimetabolite_Application_Type::SPONGE): ?>
            <tr>
                <th><?= CHtml::encode($element->getAttributeLabel('duration')) ?>:</th>
                <td><?= $element->duration ?></td>
            </tr>
            <tr>
                <th><?= CHtml::encode($element->getAttributeLabel('number')) ?>:</th>
                <td><?= $element->number ?></td>
            </tr>
            <tr>
                <th><?= CHtml::encode($element->getAttributeLabel('washed')) ?>:</th>
                <td><?= $element->washed ? 'Yes' : 'No' ?></td>
            </tr>
            <?php else: ?>
            <tr>
                <th><?= CHtml::encode($element->getAttributeLabel('volume_id')) ?>:</th>
                <td><?= $element->volume->value ?></td>
            </tr>
            <tr>
                <th><?= CHtml::encode($element->getAttributeLabel('dose')) ?>:</th>
                <td><?= $element->dose ?></td>
            </tr>
            <?php endif ?>
        </tbody>
    </table>
</section>
