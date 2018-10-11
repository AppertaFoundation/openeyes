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
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">

    <div class="row divider">
        <h2>Examination Event Logs</h2>
    </div>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'focus' => '#contactname',
    )) ?>

    <table class="standard">
        <tbody>
        <?php
        $fields = ['id', 'name', 'aliases', 'tallman', 'dose_unit', 'default_dose'];
        foreach ($fields as $key => $field) { ?>
            <tr>
            <td><?php echo $model->getAttributeLabel($field); ?></td>
            <td><?php echo CHtml::activeTextField(
                $model,
                $field,
                ['class' => 'cols-full']
            ); ?> </td>
            </tr>
        <?php } ?>
        <tr>
            <td><?php echo $model->getAttributeLabel('form_id'); ?></td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $model,
                    'form_id',
                    CHtml::listData(DrugForm::model()->findAll(), 'id', 'name'),
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $model->getAttributeLabel('default_route_id'); ?></td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $model,
                    'default_route_id',
                    CHtml::listData(DrugRoute::model()->findAll(), 'id', 'name'),
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $model->getAttributeLabel('default_frequency_id'); ?></td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $model,
                    'default_frequency_id',
                    CHtml::listData(DrugFrequency::model()->findAll(), 'id', 'name'),
                    [
                        'empty' => '-- Please select --',
                        'class' => 'cols-full'
                    ]
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $model->getAttributeLabel('default_duration_id'); ?></td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $model,
                    'default_duration_id',
                    CHtml::listData(DrugFrequency::model()->findAll(), 'id', 'name'),
                    [
                        'empty' => '-- Please select --',
                        'class' => 'cols-full'
                    ]
                ); ?>
            </td>
        </tr>

        <tr>
            <td>Allergy Warnings</td>
            <td>
                <ul class="MultiSelectList multi-select-selections" id="alergy_display"></ul>
                <div class="flex-layout flex-right">
                    <button class="button hint green" id="add-prescription-btn" type="button"><i
                            class="oe-i plus pro-theme"></i></button>
                </div>
            </td>
        </tr>

        <tr>
            <td>Tags</td>
            <td>
                <?php echo $form->multiSelectList(
                    $model,
                    'tags',
                    'tags',
                    'id',
                    CHtml::listData(Tag::model()->findAll(array('order' => 'name')), 'id', 'name'),
                    null,
                    array('empty' => '- Select -', 'label' => 'Tags', 'nowrapper' => true, 'class' => 'cols-full')
                ) ?>
            </td>
        </tr>

        <tr class="col-gap">
            <td>Active</td>
            <td><?= \CHtml::activeCheckBox($model, 'active') ?></td>
        </tr>
        <tr>
            <td><?php echo $model->getAttributeLabel('national_code'); ?></td>
            <td><?php echo CHtml::activeTextField(
                $model,
                'national_code',
                ['class' => 'cols-full']
            ); ?> </td>
        </tr>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="2">
                <?php
                echo CHtml::submitButton('Save', [
                    'class' => 'button large',
                    'name' => 'save',
                    'id' => 'et_save',
                ]); ?>
                <?php
                echo CHtml::submitButton('Cancel', [
                    'class' => 'button large',
                    'data-uri' => '/oeadmin/drug/list',
                    'name' => 'cancel',
                    'id' => 'et_cancel',
                ]);
                ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>



<script type="text/javascript">

    /**
     * add an allergy to the ul list
     * @param allergy_name
     * @param allergy_id
     */
    function addAllergy(allergy_name, allergy_id) {
        console.log(allergy_id);
        $('#alergy_display').append(
            '<input type="hidden" name="' + '<?= CHtml::modelName($model) ?>' + '[allergies][]" value="' + allergy_id + '">' +
            '<li><span class="text">' + allergy_name +
            '</span><span data-text="Vitamin" class="multi-select-remove remove-one cols-full"><i class="oe-i remove-circle small"></i></span><input type="hidden" name="tags[]" value="' +
            allergy_id + '"></li>');
    }

    $(document).ready(function () {
        <?php $allergies = Allergy::model()->active()->findAll(array('order' => 'name')); ?>

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-prescription-btn'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($allergy) {
                    return [
                        'label' => $allergy['name'],
                        'id' => $allergy['id'] ,
                    ];
                }, $allergies)
            ) ?>, {'multiSelect': true})],
            searchOptions: {
                searchSource: 'allergy/autocomplete',
            },
            onReturn: function (adderDialog, selectedItems) {
                for (var i = 0; i < selectedItems.length; i++) {
                    addAllergy(selectedItems[i].label, selectedItems[i].id);
                }
                return true;
            }
        });
    })
</script>
