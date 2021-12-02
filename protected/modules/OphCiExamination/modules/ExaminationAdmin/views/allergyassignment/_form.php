<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?=\CHtml::errorSummary(
    array_merge([$model], $model->entries),
    null,
    null,
    ["class" => "alert-box alert with-icon"]
); ?>

<div class="cols-full">
    <table class="standard cols-full">
        <h2><?=$title ?></h2>
        <hr class="divider">
        <colgroup>
            <col class="cols-3">
            <col class="cols-full">
        </colgroup>
        <tbody>
        <tr>
            <td>Name</td>
            <td class="cols-full">
                <?=\CHtml::activeTelField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <?php
        $this->widget('application.widgets.SubspecialtyFirmPicker', [
            'model' => $model
        ]);
        ?>
        </tbody>
    </table>


    <?php
    $examination_allergy_listdata = CHtml::listData(
        OEModule\OphCiExamination\models\OphCiExaminationAllergy::model()->findAll(),
        'id',
        'name'
    );
    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
    ?>

    <div id="allergy" class="data-group">
        <?php
        $columns = [
            [
                'header' => 'Allergy Name',
                'name' => 'Allergy Name',
                'type' => 'raw',
                'value' => function ($data, $row) use ($examination_allergy_listdata) {
                    return
                        CHtml::hiddenField(
                            "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][id]",
                            $data->id
                        ) .
                        CHtml::dropDownList(
                            "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][ophciexamination_allergy_id]",
                            $data->ophciexamination_allergy_id,
                            $examination_allergy_listdata,
                            ['empty' => '- select --']
                        );
                }
            ],
            [
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function ($data, $row) use ($gender_options) {
                    echo CHtml::dropDownList(
                        "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][gender]",
                        $data->gender,
                        $gender_options,
                        ['empty' => '-- select --']
                    );
                }
            ],
            [
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField(
                        "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][age_min]",
                        $data->age_min,
                        ["style" => "width:55px;"]
                    );
                }
            ],
            [
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField(
                        "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][age_max]",
                        $data->age_max,
                        ["style" => "width:55px;"]
                    );
                }
            ],
            [
                'header' => '',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::link('remove', '#', ['class' => 'remove_allergy_entry']);
                }
            ],
        ];
        $dataProvider = new \CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationAllergySetEntry');
        $dataProvider->setData($model->entries);

        ?>
        <?php $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            //'template' => '{items}',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression' => '["data-row"=>$row]',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
            'rowHtmlOptionsExpression' => '["data-row" => $row]',
        ]); ?>
    </div>
    <?=\CHtml::button(
        'Add Allergy',
        [
            'class' => 'button large',
            'type' => 'button',
            'id' => 'add_new_allergy'
        ]
    ); ?>

    <?=\CHtml::submitButton(
        'Save',
        [
            'class' => 'button large',
            'name' => 'save',
            'id' => 'et_save'
        ]
    ); ?>

    <?=\CHtml::button(
        'Cancel',
        [
            'class' => 'button large',
            'type' => 'button',
            'name' => 'cancel',
            'id' => 'et_cancel',
            'data-uri' => '/OphCiExamination/admin/AllergyAssignment/index',
        ]
    ); ?>
</div>


<script type="text/template" id="new_allergy_entry" class="hidden">
    <tr data-row="{{row}}">
        <td>
            <?php
            echo CHtml::dropDownList(
                "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][ophciexamination_allergy_id]",
                null,
                $examination_allergy_listdata,
                ["empty" => '-- select --']
            );
            ?>
        </td>
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][gender]", null, $gender_options, ['empty' => '-- select --']);
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][age_min]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][age_max]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_allergy_entry">remove</a>
        </td>
    </tr>
</script>

<script>
    $(document).ready(function () {
        var $table = $('table.generic-admin');

        $('#add_new_allergy').on('click', function (e) {
            var data = {}, $row
            $table = $('table.generic-admin');

            data['row'] = OpenEyes.Util.getNextDataKey($table.find('tbody tr'), 'row');
            $row = Mustache.render(
                $('#new_allergy_entry').text(),
                data
            );
            $table.find('tbody').append($row);
            $table.find('td.empty').closest('tr').hide();
        });

        $($table).on('click', '.remove_allergy_entry', function (e) {
            $(this).closest('tr').remove();
            if ($table.find('tbody tr').length <= 1) {
                $table.find('td.empty').closest('tr').show();
            }
        });

        $(this).on('change', '#subspecialty-id', function (e) {
            var subspecialty_id = $(this).val();

            if (subspecialty_id === '') {
                $('#firm-id option').remove();
                $('#firm-id').append($('<option>').text("All Contexts"));
                $('#firm-id').attr('disabled', 'disabled');
            } else {
                $.ajax({
                    'type': 'GET',
                    'url': baseUrl + '/PatientTicketing/default/getFirmsForSubspecialty?subspecialty_id=' + subspecialty_id,
                    'success': function (html) {
                        $('#firm-id').replaceWith(html);
                        $('#firm-id').addClass('cols-full');
                    }
                });
            }
        });
    });

</script>
