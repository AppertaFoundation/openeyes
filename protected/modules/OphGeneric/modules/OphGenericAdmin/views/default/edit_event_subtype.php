<?php
/**
 * (C) Copyright Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$model_name = 'EventSubtype';
?>

<div class="cols-11">
    <?php $this->renderPartial('//elements/form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'edit-event-subtype-form',
            'enableAjaxValidation' => false,
        ]
    );
    ?>
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-1">
            <col class="cols-4">
        </colgroup>
        <tbody>
            <tr>
                <td>Name</td>
                <td><?= $event_subtype->display_name ?></td>
            </tr>
            <tr>
                <td>Sub type icon</td>
                <td>
                    <fieldset>
                        <div class="cols-11">
                            <?php
                            $sub_type_event_icons = EventIcon::model()->findAll();

                            foreach ($sub_type_event_icons as $key => $icon) { ?>
                                <label class="inline highlight" for="<?= $model_name . '_icon_id_' . $key?>">
                                    <input type="radio" id="<?= $model_name . '_icon_id_' . $key ?>"
                                           <?= $event_subtype->icon_name === $icon->name ? 'checked="checked"' : '' ?>
                                           name="<?=$model_name?>[icon_id]" value="<?= $icon->id ?>">
                                    <i class="oe-i-e <?= $icon->name ?>"></i>
                                </label>
                            <?php } ?>
                        </div>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td>Manual Entry Elements</td>
                <td>
                    <table class="standard sortable">
                        <colgroup>
                            <col class="cols-1">
                            <col class="cols-7">
                            <col class="cols-1">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Display order</th>
                                <th>Element type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="js-element-type-list" data-test="assessment-widgets">
                            <?php foreach ($event_subtype->element_type_entries as $index => $entry) { ?>
                            <tr data-test="assessment-widget-row">
                                <td>
                                    <?= \CHtml::hiddenField("EventSubtype[element_type_entries][$index][id]", $entry->id) ?>
                                    <?= \CHtml::hiddenField("EventSubtype[element_type_entries][$index][element_type_id]", $entry->element_type->id) ?>
                                    <?= \CHtml::hiddenField("EventSubtype[element_type_entries][$index][display_order]", $entry->display_order) ?>
                                    &uarr;&darr;
                                </td>
                                <td><?= $entry->element_type->name ?></td>
                                <td>
                                    <button class="button js-remove-entry">Remove</button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <?= \CHtml::dropDownList('element_type_id', '', CHtml::listData($element_types, 'id', 'name'), ['empty' => '- Select -', 'data-test' => 'choose-widgets-select']) ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Enable manual creation</td>
                <td><?= \CHtml::activeCheckBox($event_subtype, 'manual_entry', ['data-test' => 'manual-entry-checkbox']) ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <?=
                    \CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large primary event-action',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    )
                    ?>
                    <?=
                    \CHtml::submitButton(
                        'Cancel',
                        [
                            'data-uri' => '/OphGeneric/admin/Default/listEventSubTypes',
                            'class' => 'warning button large primary event-action',
                            'name' => 'cancel',
                            'id' => 'et_cancel',
                        ]
                    )
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php $this->endWidget(); ?>
</div>
<script type="text/template" id="event-subtype-element-entry">
    <tr>
        <td>
            <input type="hidden" name="EventSubtype[element_type_entries][{{index}}][id]" value="{{entryId}}" />
            <input type="hidden" name="EventSubtype[element_type_entries][{{index}}][element_type_id]" value="{{elementTypeId}}" />
            <input type="hidden" name="EventSubtype[element_type_entries][{{index}}][display_order]" value="" />
            &uarr;&darr;
        </td>
        <td>{{name}}</td>
        <td>
            <button class="button js-remove-entry">Remove</button>
        </td>
    </tr>
</script>
<script>
$(document).ready(function() {
    let newIndex = $('.js-element-type-list').children().length;

    function hideElementTypeChoice(elementTypeId) {
        const option = $(`#element_type_id option[value="${elementTypeId}"]`);

        option.css('display', 'none');
        option.prop('selected', '');

        return option;
    }

    function showElementTypeChoice(elementTypeId, entryId) {
        const option = $(`#element_type_id option[value="${elementTypeId}"]`);

        option.data('entry-id', entryId);
        option.css('display', '');
    }

    function setDisplayOrders(rows) {
        $(rows).each(function(index, tr) {
            index++;
            $(tr).find("[name$='[display_order]']").val(index);
        });

        $('.js-remove-entry').off('click').on('click', function(e) {
            e.preventDefault();

            const entry = $(this).parent().parent('tr');

            const entryId = entry.find('input[name$="[id]"]').val();
            const elementTypeId = entry.find('input[name$="[element_type_id]"]').val();

            showElementTypeChoice(elementTypeId, entryId);

            entry.remove();
        });
    }

    $('#element_type_id').on('change', function() {
        const newElementTypeId = $(this).val();

        if (newElementTypeId) {
            const template = $('#event-subtype-element-entry').text();
            const option = hideElementTypeChoice(newElementTypeId);
            const newName = option.text();
            const existingEntryId = option.data('entry-id');

            const new_entry = Mustache.render(template, { index: newIndex, elementTypeId: newElementTypeId, name: newName, entryId: existingEntryId });

            newIndex = newIndex + 1;

            $('.js-element-type-list').append(new_entry);

            setDisplayOrders($('.js-element-type-list'));
        }
    });

    $('.sortable tbody').sortable({
        stop: function(e, ui) {
            setDisplayOrders('.sortable tbody tr');
        }
    });

    const existing = $('.js-element-type-list input[name$="[element_type_id]"]').map(function() { return $(this).val(); }).get();

    for (id of existing) {
        $(`#element_type_id option[value="${id}"]`).css('display', 'none');
    }

    setDisplayOrders('.sortable tbody tr');
});
</script>
