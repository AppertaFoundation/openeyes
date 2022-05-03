<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
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
    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
    ?>

    <div id="risks" class="field-row">
        <?php
        $columns = [
            [
                'header' => 'Operation',
                'name' => 'Operation',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return
                        '<div>' .
                        CHtml::dropDownList(
                            null,
                            '',
                            CHtml::listData(CommonPreviousOperation::model()->findAll(
                                ['order' => 'display_order asc']
                            ), 'id', 'name'),
                            ['empty' => '- Select -', 'class' => 'common_prev_op_select']
                        ) . '<br />' .
                        CHtml::textField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][operation]", $data->operation, [
                            'placeholder' => 'Select from above or type',
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'common-operation',
                        ]) .
                        '</div>';
                }
            ],
            [
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function ($data, $row) use ($gender_options) {
                    echo CHtml::dropDownList("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][gender]", $data->gender, $gender_options, ['empty' => '-- select --']);
                }
            ],
            [
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][age_min]", $data->age_min, ["style" => "width:55px;"]);
                }
            ],
            [
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][age_max]", $data->age_max, ["style" => "width:55px;"]);
                }
            ],
            [
                'header' => '',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::link('remove', '#', ['class' => 'remove_shs_entry']);
                }
            ],

        ];
        $dataProvider = new \CActiveDataProvider(\OEModule\OphCiExamination\models\SurgicalHistorySetEntry::class);
        $dataProvider->setData($model->entries);
        $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression' => '["data-row"=>$row]',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
        ]);
        ?>
    </div>

    <?=\CHtml::button(
        'Add Entry',
        [
            'class' => 'button large',
            'type' => 'button',
            'id' => 'add_new_entry'
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
            'data-uri' => '/OphCiExamination/admin/SurgicalHistoryAssignment/index'
        ]
    ); ?>
</div>

<script type="text/template" id="new_risk_entry" class="hidden">
    <tr data-row="{{row}}">
        <td>
            <div>
                <?php
                $list_data = CHtml::listData(
                    CommonPreviousOperation::model()->findAll(['order' => 'display_order asc']),
                    'id',
                    'name'
                );
                echo CHtml::dropDownList(null, '', $list_data, ['empty' => '- Select -', 'class' => 'common_prev_op_select']);
                echo '<br />' .
                CHtml::textField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][operation]", '', [
                        'placeholder' => 'Select from above or type',
                        'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'class' => 'common-operation',
                    ]);
                ?>
            </div>
        </td>
        <td>
            <?php
                echo CHtml::dropDownList("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][gender]", null, $gender_options, ['empty' => '-- select --']);
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][age_min]"
                   id="OEModule_OphCiExamination_models_SurgicalHistorySetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][age_max]"
                   id="OEModule_OphCiExamination_models_SurgicalHistorySetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_shs_entry">remove</a>
        </td>
    </tr>
</script>

<script>

    $(document).ready(function () {

        var $table = $('table.generic-admin');

        $(document).on("change", ".common_prev_op_select", function (e) {
            let textVal = $(e.target).find("option:selected").text();
            let $textInput = $(e.target).parent('div').find('.common-operation');
            $textInput.val(textVal);
            $(e.target).val('');
        });

        $('#add_new_entry').on('click', function (e) {
            let data = {}, $row;
            $table = $('table.generic-admin');

            data['row'] = OpenEyes.Util.getNextDataKey($table.find('tbody tr'), 'row');
            $row = Mustache.render(
                $('#new_risk_entry').text(),
                data
            );
            $table.find('tbody').append($row);
            $table.find('td.empty').closest('tr').hide();

        });

        $($table).on('click', '.remove_shs_entry', function (e) {
            $(this).closest('tr').remove();
            if ($table.find('tbody tr').length <= 1) {
                $table.find('td.empty').closest('tr').show();
            }
        });
    });

</script>
