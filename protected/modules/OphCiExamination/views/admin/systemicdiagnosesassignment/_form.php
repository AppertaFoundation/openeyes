<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
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
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php echo CHtml::errorSummary(array_merge(array($model), $model->entries), null, null, array("class"=>"alert-box alert with-icon")); ?>

<?php

    echo $form->textField($model, "name");
    echo "<br>";

    $this->widget('application.widgets.SubspecialtyFirmPicker', [
        'model' => $model
    ]);

    echo "<br>";

    $disorder = CHtml::listData([0 => \Disorder::model()->findByPk(103)], 'id', 'term');
    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
?>

<div id="risks" class="data-group">
        <?php
        $columns = array(
            array(
                'header' => 'Diagnosis',
                'name' => 'Diagnosis',
                'type' => 'raw',
                'value' => function($data, $row) use ($disorder){
                    return CHtml::textField("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][disorder_id]", null,
                        [
                            'class' => 'diagnoses-search-autocomplete',
                            'data-saved-diagnoses' => $data->disorder ? json_encode([
                                'id' => $data->id,
                                'name' => $data->disorder->term,
                                'disorder_id' => $data->disorder->id,

                            ], JSON_HEX_QUOT | JSON_HEX_APOS) : ''
                        ]);
                }
            ),
            array(
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function($data, $row) use ($gender_options){
                    echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][gender]", $data->gender, $gender_options, array('empty' => '-- select --'));
                }
            ),
            array(
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function($data, $row){
                    return CHtml::numberField("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][age_min]", $data->age_min, array("style"=>"width:55px;"));
                }
            ),
            array(
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function($data, $row){
                    return CHtml::numberField("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[$row][age_max]", $data->age_max, array("style"=>"width:55px;"));
                }
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function($data, $row){
                    return CHtml::link('remove', '#', array('class' => 'remove_risk_entry'));
                }
            ),

        );

        $dataProvider = new \CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationSystemicDiagnosesSetEntry');
        $dataProvider->setData($model->entries);
        $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression'=>'array("data-row" => $row, "data-key" => $row)',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
            'id'=>'OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry_diagnoses_table'

        ));
        ?>
        <button id="add_new_risk" type="button" class="small primary right">Add</button>
</div>

<?php $js_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js') . '/OpenEyes.UI.DiagnosesSearch.js', false, -1);?>
<script type="text/javascript" src="<?=$js_path;?>"></script>

<script type="text/template" id="new_risk_entry" class="hidden">
    <tr data-row="{{row}}" data-key="{{row}}">
        <td>
            <?php
                echo CHtml::textField("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][disorder_id]",null,['class' => 'diagnoses-search-autocomplete']);
            ?>
        </td>
        <td>
            <?php
                echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][gender]", null, $gender_options, array('empty' => '-- select --'));
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number" name="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][age_min]" id="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number" name="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry[{{row}}][age_max]" id="OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_risk_entry">remove</a>
        </td>
    </tr>
</script>

<script>

    function initDiagnosesSearchController($row){
        diagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
            'inputField': $row.find('.diagnoses-search-autocomplete'),
            'fieldPrefix': 'OEModule_OphCiExamination_models_OphCiExaminationSystemicDiagnosesSetEntry',
            singleTemplate :
                "<span class='medication-display' style='display:none'>" + "<a href='javascript:void(0)' class='diagnosis-rename'><i class='fa fa-times-circle' aria-hidden='true' title='Change diagnosis'></i></a> " +
                "<span class='diagnosis-name'></span></span>" +
                "<select class='commonly-used-diagnosis cols-full'></select>" +
                "{{{input_field}}}" +
                "<input type='hidden' name='{{field_prefix}}[" + $row.attr("data-row") + "][id]' class='savedDiagnosisId' value=''>" +
                "<input type='hidden' name='{{field_prefix}}[" + $row.attr("data-row") + "][disorder_id]' class='savedDiagnosis' value=''>"
        });
        $row.find('.diagnoses-search-autocomplete').data('diagnosesSearchController', diagnosesSearchController );
    }


    $(document).ready(function() {

        var $table = $('table.generic-admin'),
            $empty_tr = $table.find('.empty').closest('tr'),
            diagnosesSearchController;

        $('#add_new_risk').on('click', function (e) {
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