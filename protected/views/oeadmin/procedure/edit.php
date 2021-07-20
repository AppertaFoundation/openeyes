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

<div class="cols-5">

    <h2>Edit Procedure</h2>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

    <form method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-3">
                <col class="cols-4">
            </colgroup>
            <tbody>
            <?php
            $personal_fields = ['term', 'short_format', 'default_duration', 'snomed_code', 'snomed_term', 'aliases'];
            foreach ($personal_fields as $field) : ?>
                <tr>
                    <td><?php echo $procedure->getAttributeLabel($field); ?></td>
                    <td>
                        <?=\CHtml::activeTextField(
                            $procedure,
                            $field,
                            [
                                'autocomplete' => Yii::app()->params['html_autocomplete'],
                                'class' => 'cols-full'
                            ]
                        ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>Unbooked</td>
                <td>
                    <?=\CHtml::activeCheckBox($procedure, 'unbooked'); ?>
                </td>
            </tr>
            <tr>
                <td>Active</td>
                <td>
                    <?=\CHtml::activeCheckBox($procedure, 'active'); ?>
                </td>
            </tr>
            <tr>
                <td>Clinic / Outpatient procedure?</td>
                <td>
                    <?= CHtml::activeCheckBox($procedure, 'is_clinic_proc') ?>
                </td>
            </tr>
            <tr>
                <td>OPCS Code</td>
                <?php
                $this->widget('application.widgets.MultiSelectDropDownList', [
                    'options' => [
                        'label' => 'OPCS_Code',
                        'dropDown' => [
                            'name' => null,
                            'id' => '$opcs_code',
                            'data' => \CHtml::listData($opcs_code, 'id', function ($opcs) {
                                return $opcs->name . ', ' . $opcs->description;
                            }),
                            'htmlOptions' => ['empty' => 'Add a OPCS Code', 'class' => 'cols-full'],
                            'selectedItemsInputName' => "opcs_codes[]",
                            'selectedItems' => array_map(function ($sub) {
                                return $sub->id;
                            }, $procedure->opcsCodes),
                        ],],
                    'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div></td>"
                ]);
                ?>
            </tr>
            <tr>
                <td>Benefit</td>
                <?php
                $this->widget('application.widgets.MultiSelectDropDownList', [
                    'options' => [
                        'label' => 'benefit',
                        'dropDown' => [
                            'name' => null,
                            'id' => '$benefits',
                            'data' => \CHtml::listData($benefits, 'id', 'name'),
                            'htmlOptions' => ['empty' => 'Add a Benefit', 'class' => 'cols-full'],
                            'selectedItemsInputName' => "benefits[]",
                            'selectedItems' => array_map(function ($sub) {
                                return $sub->id;
                            }, $procedure->benefits),
                        ],],
                    'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div></td>"
                ]);
                ?>
            </tr>
            <tr>
                <td>Complication</td>
                <?php
                $this->widget('application.widgets.MultiSelectDropDownList', [
                    'options' => [
                        'label' => 'complications',
                        'dropDown' => [
                            'name' => null,
                            'id' => '$complications',
                            'data' => \CHtml::listData($complications, 'id', 'name'),
                            'htmlOptions' => ['empty' => 'Add Complication', 'class' => 'cols-full'],
                            'selectedItemsInputName' => "complications[]",
                            'selectedItems' => array_map(function ($sub) {
                                return $sub->id;
                            }, $procedure->complications),
                        ],],
                    'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div></td>"
                ]);
                ?>
            </tr>
            <tr>
                <td>Whiteboard Risk/s</td>
                <?php
                    $this->widget('application.widgets.MultiSelectDropDownList', [
                        'options' => [
                            'label' => 'risks',
                            'dropDown' => [
                                'name' => null,
                                'id' => '$risks',
                                'data' => \CHtml::listData($risks, 'id', 'name'),
                                'htmlOptions' => ['empty' => 'Add a Risk', 'class' => 'cols-full'],
                                'selectedItemsInputName' => 'risks[]',
                                'selectedItems' => array_map(function ($sub) {
                                    return $sub->id;
                                }, $procedure->risks),
                            ],],
                        'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div></td>"
                    ]);
                    ?>
            </tr>
            <tr>
                <td>Operation Note Element</td>
                <?php
                $this->widget('application.widgets.MultiSelectDropDownList', [
                    'options' => [
                        'label' => 'notes',
                        'dropDown' => [
                            'name' => null,
                            'id' => '$notes',
                            'data' => \CHtml::listData($notes, 'id', 'name'),
                            'htmlOptions' => ['empty' => 'Add a Operation Note Element', 'class' => 'cols-full'],
                            'selectedItemsInputName' => "notes[]",
                            'selectedItems' => array_map(function ($sub) {
                                return $sub->id;
                            }, $procedure->operationNotes),
                        ],],
                    'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div></td>"
                ]);
                ?>
            </tr>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="8">
                    <?=\CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ); ?>
                    <?=\CHtml::submitButton(
                        'Cancel',
                        [
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/procedure/list',
                            'name' => 'cancel',
                            'id' => 'et_cancel'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>