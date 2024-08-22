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

<div class="cols-9">
    <h2>
        <?php
        if ($procedure->term) {
            echo "Edit Extra Procedure: " . $procedure->term;
        } else {
            echo "Add Extra Procedure";
        }
        ?>
        
    </h2>

    <?= $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

    <form method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-1">
                <col class="cols-4">
            </colgroup>
            <tbody>
                <?php
                $personal_fields = ['term', 'short_format', 'snomed_code', 'snomed_term', 'aliases'];

                foreach ($personal_fields as $field) : ?>
                    <tr>
                        <td><?= $procedure->getAttributeLabel($field); ?></td>
                        <td>
                            <?= \CHtml::activeTextField(
                                $procedure,
                                $field,
                                [
                                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                                    'class' => 'cols-full'
                                ]
                            ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
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
                            ],
                        ],
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
                            ],
                        ],
                        'template' => "<td class='js-multiselect-dropdown-wrapper'>{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div></td>"
                    ]);
                    ?>
                </tr>

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
                        <?= \CHtml::submitButton(
                            'Save',
                            [
                                'class' => 'button large',
                                'name' => 'save',
                                'id' => 'et_save'
                            ]
                        ); ?>
                        <?= \CHtml::submitButton(
                            'Cancel',
                            [
                                'class' => 'button large',
                                'data-uri' => '/OphTrConsent/oeadmin/ExtraProcedures/list',
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
