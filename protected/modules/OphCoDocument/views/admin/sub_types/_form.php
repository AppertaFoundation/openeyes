<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php $model_name = CHtml::modelName($model); ?>
<div class="cols-11">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-1">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr class="hidden">
            <td>Id</td>
            <td>
                <?=\CHtml::activeTextField(
                    $model,
                    'id',
                    ['hidden' => true]
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Name</td>
            <td>
                <?=\CHtml::activeTextField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Display Order</td>
            <td>
                <?=\CHtml::activeTextField(
                    $model,
                    'display_order',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Is Active</td>
            <td>
                <?=\CHtml::activeRadioButtonList(
                    $model,
                    'is_active',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Sub type icon</td>
            <td>
                <fieldset>
                    <div class="cols-11">
                        <?php
                        $sub_type_event_icons = EventIcon::model()->findAll();
                        foreach ($sub_type_event_icons as $key => $icon) { ?>
                            <label class="inline highlight" for="<?= $model_name . '_sub_type_event_icon_id_' . $key?>">
                                <input type="radio" id="<?= $model_name . '_sub_type_event_icon_id_' . $key ?>" <?= $model->sub_type_event_icon_id === $icon->id ? 'checked="checked"' : '' ?>
                                       name="<?=$model_name?>[sub_type_event_icon_id]" value="<?= $icon->id ?>">
                                <i class="oe-i-e <?= $icon->name ?>"></i>
                            </label>
                        <?php } ?>
                    </div>
                </fieldset>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?=\CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large primary event-action',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?=\CHtml::submitButton(
                    'Cancel',
                    [
                        'data-uri' => '/' . $this->module->id . '/' . $this->id,
                        'class' => 'warning button large primary event-action',
                        'name' => 'cancel',
                        'id' => 'et_cancel',
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
