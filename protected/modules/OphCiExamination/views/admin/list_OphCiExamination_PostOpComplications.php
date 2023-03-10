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
            <tr data-test='subspeciality-wrapper'>
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
            <tr data-test='complications-wrapper'>
                <td><h3>Complications</h3></td>
                <?php
                    $this->widget('application.widgets.MultiSelectDropDownList', [
                        'options' => [
                            'label' => 'complication_ids',
                            'dropDown' => [
                                'name' => 'null',
                                'id' => '$complication_ids',
                                'data' => $complications,
                                'htmlOptions' => ['empty' => 'Add a Complication', 'class' => 'cols-full', 'data-test' => 'complications-select'],
                                'selectedItemsInputName' => "complication_ids[]",
                                'selectedItems' => array_map(function ($selected_complication) {
                                    return $selected_complication['id'];
                                }, $selected_complications)
                            ],
                        ],
                        'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper' data-test='selected-complications'>{List}</div></td>"
                    ]);
                    ?>
            </tr>
            <tr>
                <td colspan="2">
                    <?= CHtml::submitButton('Save', ['class' => 'button large', 'data-test' => 'complications-save']); ?>
                    <?= CHtml::button('Cancel', ['class' => 'button large', 'type' => 'button', 'id' => 'draggablelist-cancel']); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <?= CHtml::endForm(); ?>
</div>
