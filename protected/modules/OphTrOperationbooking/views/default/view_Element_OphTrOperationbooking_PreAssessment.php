<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table class="cols-6">
    <tbody>
    <?php if ($element->type) : ?>
        <tr>
            <td>
                <?= $element->getAttributeLabel('type_id') ?>
            </td>
            <td>
                <div class="data-value">
                    <?= CHtml::encode($element->type->name) ?>
                </div>
            </td>
            <td></td>
        </tr>
    <?php endif; ?>
    <?php if ((int)$element->type->use_location === 1 && isset($element->location)) : ?>
        <tr>
            <td>
                <?= $element->getAttributeLabel('location_id') ?>
            </td>
            <td>
                <div class="data-value">
                    <?= CHtml::encode($element->location->name) ?>
                </div>
            </td>
            <td></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
