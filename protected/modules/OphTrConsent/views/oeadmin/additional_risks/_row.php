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

<tr id="<?= $data['key']; ?>">
    <td class="reorder">&uarr;&darr;
        <?= CHtml::activeHiddenField(
            $data['additional_risk'],
            "[" . $data['key'] . "]display_order",
            ['class' => "js-display-order", 'value' => $data['key']]
        ); ?>
    </td>
    <td>
        <?= \CHtml::activeHiddenField($data['additional_risk'], "[" . $data['key'] . "]id"); ?>
        <?= \CHtml::activeTextArea(
            $data['additional_risk'],
            "[" . $data['key'] . "]name",
            [
                'class' => 'cols-full',
                'autocomplete' => Yii::app()->params['html_autocomplete']
            ]); ?>
    </td>
        <?php
        $this->widget('application.widgets.MultiSelectDropDownList', [
            'options' => [
                'label' => 'Subspecialty:',
                'dropDown' => [
                    'name' => null,
                    'id' => '$subspecialties',
                    'data' => \CHtml::listData($subspecialty, 'id', 'name'),
                    'htmlOptions' => ['empty' => 'All Subspecialties', 'class' => 'cols-full'],
                    'selectedItemsInputName' => "subspecialty-ids[" . $data['key'] . "][]",
                    'selectedItems' => array_map(function ($sub) {
                        return $sub->subspecialty_id;
                    },
                    $data['additional_risk']->subspecialties),
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
        <?= \CHtml::activeCheckBox(
            $data['additional_risk'],
            "[" . $data['key'] . "]active"
        ) ?>
    </td>
</tr>

