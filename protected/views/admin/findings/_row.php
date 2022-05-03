<?php

/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<tr id="<?= $data['key']; ?>">
    <td class="reorder">&uarr;&darr;
        <?= CHtml::activeHiddenField(
            $data['finding'],
            "[" . $data['key'] . "]display_order",
            ['class' => "js-display-order"]
        ); ?>
    </td>
    <td>
        <?=\CHtml::activeHiddenField($data['finding'], "[" . $data['key'] . "]id");?>
        <?=\CHtml::activeTextField(
            $data['finding'],
            "[" . $data['key'] . "]name",
            [
                'class' => 'cols-full',
                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
            ]
        ); ?>
    </td>
    <?php
    $this->widget('application.widgets.MultiSelectDropDownList', [
        'options' => [
            'label' => 'Subspecialty:',
            'dropDown' => [
                'name' => null,
                'id' => 'subspecialties',
                'data' => \CHtml::listData($subspecialty, 'id', 'name'),
                'htmlOptions' => ['empty' => 'All Subspecialties'],
                'selectedItemsInputName' => "subspecialty-ids[" . $data['key'] . "][]",
                'selectedItems' => array_map(function ($sub) {
                    return $sub->id;
                }, $data['finding']->subspecialties),
            ],],
        'template' =>
            "<td class='js-multiselect-dropdown-wrapper'>
                {DropDown}
                <div class='list-filters js-multiselect-dropdown-list-wrapper'>
                    {List}
                </div>
            </td>"
    ]);
    ?>
    <td>
        <?=\CHtml::activeCheckBox(
            $data['finding'],
            "[" . $data['key'] . "]requires_description"
        ) ?>
    </td>
    <td>
        <?=\CHtml::activeCheckBox(
            $data['finding'],
            "[" . $data['key'] . "]active"
        ) ?>
    </td>
</tr>
