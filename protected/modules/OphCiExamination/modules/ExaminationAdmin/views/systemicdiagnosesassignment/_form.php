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

    <div class="row divider">
        <h2><?=$title ?></h2>
    </div>

    <table class="standard cols-full">
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
    $disorder = CHtml::listData([0 => \Disorder::model()->findByPk(103)], 'id', 'term');
    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
    ?>

    <div id="risks" class="data-group">
        <?php
        $columns = [
            [
                'header' => 'Diagnosis',
                'name' => 'Diagnosis',
                'type' => 'raw',
                'value' => function ($data, $row) use ($disorder) {
                    return CHtml::textField(
                        "OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][disorder_id]",
                        null,
                        [
                            'class' => 'diagnoses-search-autocomplete',
                            'data-saved-diagnoses' => $data->disorder ? json_encode([
                                'id' => $data->id,
                                'name' => $data->disorder->term,
                                'disorder_id' => $data->disorder->id,

                            ], JSON_HEX_QUOT | JSON_HEX_APOS) : ''
                        ]
                    );
                }
            ],
            [
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function ($data, $row) use ($gender_options) {
                    echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][gender]", $data->gender, $gender_options, ['empty' => '-- select --']);
                }
            ],
            [
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][age_min]", $data->age_min, ["style" => "width:55px;"]);
                }
            ],
            [
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][age_max]", $data->age_max, ["style" => "width:55px;"]);
                }
            ],
            [
                'header' => '',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::link('remove', '#', ['class' => 'remove_risk_entry']);
                }
            ],

        ];

        $dataProvider = new \CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationSystemicDiagnosesSetEntry');
        $dataProvider->setData($model->entries);
        $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression' => '["data-row" => $row, "data-key" => $row]',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
            'id' => 'OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry_diagnoses_table'

        ]);
        ?>

        <?=\CHtml::button(
            'Add Diagnosis',
            [
                'class' => 'button large',
                'type' => 'button',
                'id' => 'add_new_diagnosis'
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
                'data-uri' => '/OphCiExamination/admin/SystemicDiagAssignment/index',
            ]
        ); ?>
    </div>
</div>

<?php $js_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js') . '/OpenEyes.UI.DiagnosesSearch.js', true, -1); ?>
<script src="<?=$js_path; ?>"></script>

<script type="text/template" id="new_risk_entry" class="hidden">
    <tr data-row="{{row}}" data-key="{{row}}">
        <td>
            <?php
            echo CHtml::textField("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][disorder_id]", null, ['class' => 'diagnoses-search-autocomplete']);
            ?>
        </td>
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][gender]", null, $gender_options, ['empty' => '-- select --']);
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][age_min]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][age_max]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_risk_entry">remove</a>
        </td>
    </tr>
</script>

<script>

    function initDiagnosesSearchController($row) {
        diagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
            'inputField': $row.find('.diagnoses-search-autocomplete'),
            'fieldPrefix': 'OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry',
            singleTemplate:
                "<span class='medication-display' style='display:none'>" + "<a href='javascript:void(0)' class='diagnosis-rename'><i class='fa fa-times-circle' aria-hidden='true' title='Change diagnosis'></i></a> " +
                "<span class='diagnosis-name'></span></span>" +
                "<select class='commonly-used-diagnosis cols-full'></select>" +
                "{{{input_field}}}" +
                "<input type='hidden' name='{{field_prefix}}[" + $row.attr("data-row") + "][id]' class='savedDiagnosisId' value=''>" +
                "<input type='hidden' name='{{field_prefix}}[" + $row.attr("data-row") + "][disorder_id]' class='savedDiagnosis' value=''>"
        });
        $row.find('.diagnoses-search-autocomplete').data('diagnosesSearchController', diagnosesSearchController);
    }


    $(document).ready(function () {
        var $table = $('table.generic-admin'),
            $empty_tr = $table.find('.empty').closest('tr'),
            diagnosesSearchController;

        $('#add_new_diagnosis').on('click', function (e) {
            var data = {},
                $row;

            $empty_tr.hide();
            data['row'] = OpenEyes.Util.getNextDataKey($table.find('tbody tr'), 'row');
            $row = Mustache.render(
                $('#new_risk_entry').text(),
                data
            );
            $table.find('tbody').append($row);
            $row = $table.find('tbody tr:last');

            initDiagnosesSearchController($row)
        });

        $($table).on('click', '.remove_risk_entry', function (e) {
            $(this).closest('tr').remove();
        });

        $.each($table.find('tr'), function (i, tr) {
            var $tr = $(tr);
            initDiagnosesSearchController($tr);
        });
    });
</script>
