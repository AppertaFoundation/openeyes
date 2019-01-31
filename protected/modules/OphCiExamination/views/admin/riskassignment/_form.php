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

<div class="cols-5">
    <table class="standard cols-full">
        <h2><?php echo $title ?></h2>
        <hr class="divider">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
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
    echo "<br>";

    $examination_risk_listdata = CHtml::listData(OEModule\OphCiExamination\models\OphCiExaminationRisk::model()->findAll(), 'id', 'name');
    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
    ?>

    <div id="risks" class="data-group">
        <?php
        $columns = array(
            array(
                'header' => 'Risk Name',
                'name' => 'Risk Name',
                'type' => 'raw',
                'value' => function ($data, $row) use ($examination_risk_listdata) {
                    return
                        CHtml::hiddenField("OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[$row][id]", $data->id) .
                        CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[$row][ophciexamination_risk_id]", $data->ophciexamination_risk_id, $examination_risk_listdata, array('empty' => '- select --'));
                }
            ),
            array(
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function ($data, $row) use ($gender_options) {
                    echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[$row][gender]", $data->gender, $gender_options, array('empty' => '-- select --'));
                }
            ),
            array(
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[$row][age_min]", $data->age_min, array("style" => "width:55px;"));
                }
            ),
            array(
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[$row][age_max]", $data->age_max, array("style" => "width:55px;"));
                }
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::link('remove', '#', array('class' => 'remove_risk_entry'));
                }
            ),

        );
        $dataProvider = new \CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationRiskSetEntry');
        $dataProvider->setData($model->ophciexamination_risks_entry);
        $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            //'template' => '{items}',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression' => 'array("data-row"=>$row)',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
            'rowHtmlOptionsExpression' => 'array("data-row" => $row)',
        ));
        ?>
    </div>

    <?=\CHtml::button(
        'Add Risk',
        [
            'class' => 'button large',
            'type' => 'button',
            'id' => 'add_new_risk'
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
            'id' => 'et_cancel'
        ]
    ); ?>
</div>


<script type="text/template" id="new_risk_entry" class="hidden">
    <tr data-row="{{row}}">
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[{{row}}][ophciexamination_risk_id]",
                null, $examination_risk_listdata, array("empty" => '-- select --'));
            ?>
        </td>
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[{{row}}][gender]", null, $gender_options, array('empty' => '-- select --'));
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[{{row}}][age_min]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry[{{row}}][age_max]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationRiskSetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_risk_entry">remove</a>
        </td>
    </tr>
</script>

<script>

    $(document).ready(function () {

        var $table = $('table.generic-admin');

        $('#add_new_risk').on('click', function (e) {
            var data = {}, $row
            $table = $('table.generic-admin');

            data['row'] = OpenEyes.Util.getNextDataKey($table.find('tbody tr'), 'row');
            $row = Mustache.render(
                $('#new_risk_entry').text(),
                data
            );
            $table.find('tbody').append($row);
            $table.find('td.empty').closest('tr').hide();
        });

        $($table).on('click', '.remove_risk_entry', function (e) {
            $(this).closest('tr').remove();
            if ($table.find('tbody tr').length <= 1) {
                $table.find('td.empty').closest('tr').show();
            }
        });
    });

</script>

<script>
    $(document).ready(function () {
        $('#et_cancel').click(function () {
            window.location.href = '/OphCiExamination/oeadmin/RisksAssignment/';
        });
    });
</script>

