<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div id="postop-complications" class="cols-7">
    <div id="draggablelist">
        <?= CHtml::beginForm(array('/OphCiExamination/admin/updatePostOpComplications'), 'post'); ?>

        <table id='draggablelist-items' class='standard data-group'>
            <tbody>
            <tr>
                <td><h3>Institution</h3></td>
                <td>
                    <?= CHtml::dropDownList(
                        'institution_id',
                        $institution_id,
                        Institution::model()->getTenantedList(true),
                        ['class' => 'cols-6']
                    ) ?>
                </td>
            </tr>
            <tr>
                <td><h3>Subspecialty</h3></td>
                <td>
                    <?= CHtml::dropDownList(
                        'subspecialty_id',
                        $subspecialty_id,
                        CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name', 'specialty.name'),
                        ['empty' => '- Select -', 'class' => 'cols-6']
                    ); ?>
                </td>
            </tr>
            <tr>
                <?= CHtml::hiddenField('item_ids'); ?>
                <td><h3>Currently assigned to</h3></td>
                <td>
                    <?php $this->renderPartial('_postOpComplications_table', array('id' => 'draggablelist-items-enabled', 'items' => $enabled_items)); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?= CHtml::submitButton('Save', ['class' => 'button large']); ?>
                    <?= CHtml::button('Cancel', ['class' => 'button large', 'type' => 'button', 'id' => 'draggablelist-cancel']); ?>
                </td>
            </tr>
            <tr class="available-items">
                <td><h3>Available items</h3></td>
                <td>
                    <?php $this->renderPartial('_postOpComplications_table', array('id' => 'draggablelist-items-available', 'items' => $available_items)); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <?= CHtml::endForm(); ?>
</div>
