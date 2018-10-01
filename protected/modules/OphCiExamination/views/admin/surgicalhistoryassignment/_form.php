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
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php echo CHtml::errorSummary(
    array_merge(array($model), $model->entries),
    null,
    null,
    array("class" => "alert-box alert with-icon")
); ?>

<div class="cols-7">
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
                <?php echo CHtml::activeTelField(
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
        $columns = array(
            array(
                'header' => 'Operation',
                'name' => 'Operation',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return
                        '<div>' .
                        CHtml::dropDownList(null, '',
                            CHtml::listData(CommonPreviousOperation::model()->findAll(
                                array('order' => 'display_order asc')), 'id', 'name'),
                            array('empty' => '- Select -', 'class' => 'common_prev_op_select')) . '<br />' .
                        CHtml::textField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][operation]", $data->operation, array(
                            'placeholder' => 'Select from above or type',
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'common-operation',
                        )) .
                        '</div>';
                }
            ),
            array(
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function ($data, $row) use ($gender_options) {
                    echo CHtml::dropDownList("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][gender]", $data->gender, $gender_options, array('empty' => '-- select --'));
                }
            ),
            array(
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][age_min]", $data->age_min, array("style" => "width:55px;"));
                }
            ),
            array(
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][age_max]", $data->age_max, array("style" => "width:55px;"));
                }
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::link('remove', '#', array('class' => 'remove_shs_entry'));
                }
            ),

        );
        $dataProvider = new \CActiveDataProvider(\OEModule\OphCiExamination\models\SurgicalHistorySetEntry::class);
        $dataProvider->setData($model->entries);
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
        ));
        ?>
    </div>

    <?php echo CHtml::button(
        'Add Entry',
        [
            'class' => 'button large',
            'type' => 'button',
            'id' => 'add_new_entry'
        ]
    ); ?>

    <?php echo CHtml::button(
        'Save',
        [
            'class' => 'button large',
            'type' => 'submit',
            'name' => 'save',
            'id' => 'et_save'
        ]
    ); ?>

    <?php echo CHtml::button(
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
            <div>
                <?php
                echo CHtml::dropDownList(null, '',
                        CHtml::listData(CommonPreviousOperation::model()->findAll(
                            array('order' => 'display_order asc')), 'id', 'name'),
                        array('empty' => '- Select -', 'class' => 'common_prev_op_select')) . '<br />' .
                    CHtml::textField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][operation]", '', array(
                        'placeholder' => 'Select from above or type',
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'class' => 'common-operation',
                    ));
                ?>
            </div>
        </td>
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][gender]", null, $gender_options, array('empty' => '-- select --'));
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

<script type="text/javascript">

    $(document).ready(function () {

        var $table = $('table.generic-admin');

        $(document).on("change", ".common_prev_op_select", function (e) {
            var textVal = $(e.target).find("option:selected").text();
            var $textInput = $(e.target).parent('div').find('.common-operation');
            $textInput.val(textVal);
            $(e.target).val('');
        });

        $('#add_new_entry').on('click', function (e) {
            var data = {}, $row;
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

<script>
    $(document).ready(function () {
        $('#et_cancel').click(function () {
            window.location.href = '/OphCiExamination/oeadmin/SurgicalHistoryAssignment/';
        });
    });
</script>