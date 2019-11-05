<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-5">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Type</td>
            <td class="cols-full">
                <?= \CHtml::activeTextArea(
                    $model,
                    'type',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Field Type</td>
            <td>
                <?= CHtml::activeDropDownList($model, 'field_type_id', CHtml::listData(
                    OphInLabResults_Field_Type::model()->findAll(),
                    'id',
                    'name'
                )) ?>
            </td>
        </tr>

        <tr>
            <td>Default Units</td>
            <td>
                <?= \CHtml::activeTextArea(
                    $model,
                    'default_units',
                    ['class' => 'cols-full autosize',
                        'style' => 'overflow: hidden; ']
                ); ?>
            </td>
        </tr>
        <tr class="js-field-type-numeric-fields"
            style="<?= $model->fieldType->name != "Numeric Field" ? "display:none" : "" ?>">
            <td>Custom warning message</td>
            <td>
                <?= \CHtml::activeTextArea(
                    $model,
                    'custom_warning_message',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr class="js-field-type-numeric-fields"
            style="<?= $model->fieldType->name != "Numeric Field" ? "display:none" : "" ?>">
            <td>Min Range</td>
            <td>
                <?= \CHtml::activeNumberField(
                    $model,
                    'min_range',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr class="js-field-type-numeric-fields"
            style="<?= $model->fieldType->name != "Numeric Field" ? "display:none" : "" ?>">
            <td>Max Range</td>
            <td>
                <?= \CHtml::activeNumberField(
                    $model,
                    'max_range',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr class="js-field-type-numeric-fields"
            style="<?= $model->fieldType->name != "Numeric Field" ? "display:none" : "" ?>">
            <td>Normal Min</td>
            <td>
                <?= \CHtml::activeNumberField(
                    $model,
                    'normal_min',
                    []
                ); ?>
            </td>
        </tr>
        <tr class="js-field-type-numeric-fields"
            style="<?= $model->fieldType->name != "Numeric Field" ? "display:none" : "" ?>">
            <td>Normal Max</td>
            <td>
                <?= \CHtml::activeNumberField(
                    $model,
                    'normal_max',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Show on Whiteboard</td>
            <td>
                <?= \CHtml::activeCheckBox(
                    $model,
                    'show_on_whiteboard',
                    []
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="cols-5 js-drop-down-fields" style="<?= $model->fieldType->name != "Drop-down Field" ? "display:none" : "" ?>">
    <div class="row-divider">
        <h2>Field Options</h2>
    </div>
    <div class="element-fields flex-layout full-width">
    <table class="js-field-options-table standard cols-full">
        <tbody class="js-field-options">
        <?php if (isset($model->id)) {
            foreach (OphInLabResults_Type_Options::model()->findAll('type = ? ', [$model->id]) as $option) { ?>
                <tr>
                    <td>
                        <input type="hidden" name="type_options[options_id][]" value="<?= $option->id ?>">
                        <input type="text" name="type_options[value][]" value="<?= $option->value ?>">
                    </td>
                    <td>
                        <i class="oe-i trash"></i>
                    </td>
                </tr>
            <?php }
        } ?>
        </tbody>
    </table>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green js-add-select-search" id="add-new-option" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#add-new-option').on('click', function () {
            $('.js-field-options').append("<tr>\n" +
                "                    <td>\n" +
                "                        <input type=\"hidden\" name=\"type_options[options_id][]\">" +
                "                        <input type=\"text\" name=\"type_options[value][]\">" +
                "                    </td>\n" +
                "                    <td>\n" +
                "                        <i class=\"oe-i trash\"></i>\n" +
                "                    </td>\n" +
                "                </tr>");
        });

        $('.js-field-options-table').on('click','i.trash', function(){
            $(this).closest('tr').remove();
        });

        $('#OphInLabResults_Type_field_type_id').change(function (event) {
            let selectedOption = $(this).find("option:selected").text();
            if (selectedOption === "Numeric Field") {
                $('.js-field-type-numeric-fields').show();
                $('.js-drop-down-fields').hide();
            } else if(selectedOption === "Drop-down Field"){
               $('.js-drop-down-fields').show();
                $('.js-field-type-numeric-fields').hide();
            } else {
                $('.js-field-type-numeric-fields').hide();
                $('.js-drop-down-fields').hide()
            }

        })
    })
</script>