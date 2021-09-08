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

<tr id="<?= $data['id']; ?>">
    <td class="reorder">&uarr;&darr;
        <?= CHtml::activeHiddenField(
            $data,
            "display_order",
            ['class' => "js-display-order", 'value' => $data['display_order']]
        ); ?>
    </td>
    <td>
        <?=\CHtml::activeHiddenField(
            $data,
            "id"
        ); ?>
        <?=\CHtml::activeHiddenField(
            $data,
            "element_id"
        ); ?>
        <label class="cols-full"><?= CHtml::encode($data->element->name); ?></label>
    </td>
    <td>
        <button type="button">
            <a href="javascript:void(0)" onclick="layouts.deleteAssessment('<?= $data['id']; ?>')" class="delete">delete</a>
        </button>
    </td>
</tr>

