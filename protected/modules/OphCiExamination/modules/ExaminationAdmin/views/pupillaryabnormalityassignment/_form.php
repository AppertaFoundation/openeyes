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
                <?=\CHtml::activeTextField(
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
    $examination_abnormality_listdata = CHtml::listData(
        OEModule\OphCiExamination\models\OphCiExamination_PupillaryAbnormalities_Abnormality::model()->findAll('active = 1'),
        'id',
        'name'
    );
    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
    ?>

    <div id="pupillaryabnormality" class="data-group">
        <?php
        $columns = [
            [
                'header' => 'Pupillary Abnormality Name',
                'name' => 'Pupillary Abnormality Name',
                'type' => 'raw',
                'value' => function ($data, $row) use ($examination_abnormality_listdata) {
                    return
                        CHtml::hiddenField(
                            "OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[$row][id]",
                            $data->id
                        ) .
                        CHtml::dropDownList(
                            "OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[$row][ophciexamination_abnormality_id]",
                            $data->ophciexamination_abnormality_id,
                            $examination_abnormality_listdata,
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
                        "OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[$row][gender]",
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
                        "OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[$row][age_min]",
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
                        "OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[$row][age_max]",
                        $data->age_max,
                        ["style" => "width:55px;"]
                    );
                }
            ],
            [
                'header' => '',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::link('remove', '#', ['class' => 'remove_abnormality_entry']);
                }
            ],
        ];
        $data_provider = new \CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationPupillaryAbnormalitySetEntry');
        $data_provider->setData($model->entries);

        ?>
        <?php $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => $data_provider,
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
        'Add Pupillary Abnormality',
        [
            'class' => 'button large',
            'type' => 'button',
            'id' => 'add_new_abnormality'
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
            'data-uri' => '/OphCiExamination/admin/PupillaryAbnormalityAssignment/index',
        ]
    ); ?>
</div>


<script type="text/template" id="new_abnormality_entry" class="hidden">
    <tr data-row="{{row}}">
        <td>
            <?php
            echo CHtml::dropDownList(
                "OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[{{row}}][ophciexamination_abnormality_id]",
                null,
                $examination_abnormality_listdata,
                ["empty" => '-- select --']
            );
            ?>
        </td>
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[{{row}}][gender]", null, $gender_options, ['empty' => '-- select --']);
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[{{row}}][age_min]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry[{{row}}][age_max]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationPupillaryAbnormalitySetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_abnormality_entry">remove</a>
        </td>
    </tr>
</script>

<script>
    $(document).ready(function () {
        let $table = $('table.generic-admin');

        $('#add_new_abnormality').on('click', function (e) {
            let data = {}, $row;

            data['row'] = OpenEyes.Util.getNextDataKey($table.find('tbody tr'), 'row');
            $row = Mustache.render(
                $('#new_abnormality_entry').text(),
                data
            );
            $table.find('tbody').append($row);
            $table.find('td.empty').closest('tr').hide();
        });

        $($table).on('click', '.remove_abnormality_entry', function (e) {
            $(this).closest('tr').remove();
            if ($table.find('tbody tr').length <= 1) {
                $table.find('td.empty').closest('tr').show();
            }
        });

        $(this).on('change', '#subspecialty-id', function (e) {
            let subspecialty_id = $(this).val();

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
