<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<h2> <?= $model->id ? 'Edit' : 'Create' ?> sub type</h2>

<?php if (isset($errors) && !empty($errors)) { ?>
    <div class="alert-box alert with-icon">
        <p>Please fix the following input errors:</p>
        <ul>
            <?php foreach ($errors as $field => $errs) {
                foreach ($errs as $err) { ?>
                    <li>
                        <?= $err ?>
                    </li>
                <?php }
            } ?>
        </ul>
    </div>
<?php } ?>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'subtypeform',
    'enableAjaxValidation' => false,
    'focus' => '#name',
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
));

$model_name = CHtml::modelName($model); ?>
<div class="cols-11">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-1">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr class="hidden">
            <td>ID</td>
            <td>
                <?= CHtml::activeTelField(
                    $model,
                    'id',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Name</td>
            <td>
                <?= CHtml::activeTextField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Display Order</td>
            <td>
                <?= CHtml::activeTextField(
                    $model,
                    'display_order',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Reply required</td>
            <td>
                <?= CHtml::activeRadioButtonList(
                    $model,
                    'reply_required',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <?= CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large primary event-action',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ); ?>
                    <?= CHtml::submitButton(
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
<?php $this->endWidget(); ?>