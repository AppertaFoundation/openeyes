<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

<?php
$this->renderPartial('//base/_messages');
if (isset($errors)) {
    $this->renderPartial('/admin/_form_errors', $errors);
}
Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.GenericFormJSONConverter.js'), CClientScript::POS_HEAD);
?>
<form method="get">
    <table class="cols-7">
        <colgroup>
            <col class="cols-3">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr class="col-gap">            
            <td>&nbsp;<br/><?=\CHtml::dropDownList(
                    'institution_id',
                    $current_institution_id,
                    Institution::model()->getTenantedList(!Yii::app()->user->checkAccess('admin'))
                ) ?></td>
        </tr>
        </tbody>
    </table>
</form>

<form method="POST" action="/oeadmin/CommonSystemicDisorder/save?institution_id=<?= $current_institution_id; ?>">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <?php
    $columns = [
        [
            'header' => 'Order',
            'type' => 'raw',
            'value' => function ($data, $row) {
                $row++;
                return '<span>&uarr;&darr;</span>' .
                    CHtml::hiddenField("CommonSystemicDisorder[$row][id]", $data->id) .
                    CHtml::hiddenField("CommonSystemicDisorder[$row][display_order]", $data->display_order);
            },
            'cssClassExpression' => "'reorder'",
        ],
        [
            'header' => 'Disorder',
            'name' => 'disorder.term',
            'type' => 'raw',
            'htmlOptions' => array('width' => '180px'),
            'value' => function ($data, $row) {
                $term = null;
                $row++;
                if ($data->disorder) {
                    $term = $data->disorder->term;
                }
                return CHtml::textField((get_class($data) . "[$row][disorder_id]"), $term, array(
                    'class' => 'diagnoses-search-autocomplete',
                    'data-saved-diagnoses' => $data->disorder ? json_encode([
                        'id' => $data->id,
                        'name' => $data->disorder->term,
                        'disorder_id' => $data->disorder->id,
                    ], JSON_HEX_QUOT | JSON_HEX_APOS) : ''
                ));
            }],
        [
            'header' => 'Group',
            'name' => 'group.name',
            'type' => 'raw',
            'value' => function ($data, $row) use ($current_institution) {
                $row++;
                $options = CHtml::listData(CommonSystemicDisorderGroup::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, null, $current_institution), 'id', 'name');
                return CHtml::activeDropDownList($data, "[$row]group_id", $options, array('empty' => '-- select --'));
            }
        ],
        [
            'header' => 'Actions',
            'type' => 'raw',
            'value' => function ($data) {
                return "<button type='button'><a href='javascript:void(0)' class='delete'>delete</a></button>";
            }
        ],
    ];
    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'generic-admin standard sortable',
        'template' => '{items}',
        'emptyTagName' => 'span',
        'rowHtmlOptionsExpression' => 'array("data-row"=>($row+1))',
        'enableSorting' => false,
        'columns' => $columns
    ));
    ?>

    <div>
        <button class="button large" type="button" id="add_new">Add</button>
        &nbsp
        <button class="button large generic-admin-save" type="submit">Save</button>
    </div>
</form>

<script>
    const formStructure = {
        'name' : 'CommonSystemicDisorder',
        'tableSelector': '.generic-admin.standard.sortable',
        'rowSelector': 'tr',
        'rowIdentifier': 'row',
        'structure': {
            'CommonSystemicDisorder[ROW_IDENTIFIER][id]' : '',
            'display_order[ROW_IDENTIFIER]' : '',
            'assigned_institution[ROW_IDENTIFIER]' : '#assigned_institution_ROW_IDENTIFIER:checked',
            'CommonSystemicDisorder[ROW_IDENTIFIER][disorder_id]' : '#CommonSystemicDisorder_ROW_IDENTIFIER_disorder_id_actual',
            'CommonSystemicDisorder[ROW_IDENTIFIER][display_order]' : '#CommonSystemicDisorder_ROW_IDENTIFIER_display_order',
            'CommonSystemicDisorder[ROW_IDENTIFIER][group_id]' : '',
        }
    };
    const GenericFormJSONConverter = new OpenEyes.GenericFormJSONConverter(formStructure);
    let $table = $('.generic-admin');

    function initialiseRow($row) {
        let DiagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
            'inputField': $row.find('.diagnoses-search-autocomplete'),
            'fieldPrefix': 'CommonSystemicDisorder',
            'renderCommonlyUsedDiagnoses': false,
            'code': '',
            'singleTemplate':
                "<span class='medication-display' style='display:none'>" + "<a href='javascript:void(0)' class='diagnosis-rename'><i class='oe-i remove-circle small' aria-hidden='true' title='Change disorder'></i></a> " +
                "<span class='diagnosis-name'></span></span>" +
                "{{{input_field}}}" +
                "<input type='hidden' id='{{field_prefix}}_" + $row.data('row') + "_disorder_id_actual' " +
                "name='{{field_prefix}}[" + $row.data('row') + "][disorder_id]' class='savedDiagnosis' value=''>"
        });

        $row.on('click', 'a.delete', function () {
            $(this).closest('tr').remove();
        });
    }

    $(document).ready(function () {
        $table.find('tbody tr').each(function () {
            initialiseRow($(this));
        });

        $('#add_new').on('click', function () {
            let $tr = $('table.generic-admin tbody tr');
            let output = Mustache.render($('#common_systemic_disorder_template').text(), {
                'group_options' : common_systemic_disorder_group_options,
                "row_count" : OpenEyes.Util.getNextDataKey($tr, 'row'),
                'order_value': parseInt($('table.generic-admin tbody tr:last-child').find('input[name$="display_order]"]').val()) + 1
            });

            $('table.generic-admin tbody').append(output);

            $('table.generic-admin tbody tr:last-child').on('click', 'a.delete', function () {
                $(this).closest('tr').remove();
            });

            initialiseRow($('table.generic-admin tbody tr:last-child'));
        });

        $('.sortable tbody').sortable({
            stop: function(e, ui) {
                $('.sortable tbody tr').each(function(index, tr) {
                    $(tr).find("[name$='display_order]']").val(index);
                });
            }
        });

        $('#institution_id').on('change', function () {
            $(this).closest('form').submit();
        });

        document.querySelector('.generic-admin-save').addEventListener('click', () => GenericFormJSONConverter.convert());
    });
</script>
<script type="text/template" id="common_systemic_disorder_template">
    <tr data-row="{{row_count}}">
        <td class="reorder">
            <span>↑↓</span>
            <input type="hidden" value="" name="CommonSystemicDisorder[{{row_count}}][id]"
                   id="CommonSystemicDisorder_{{row_count}}_id">
            <input type="hidden" value="{{order_value}}" name="CommonSystemicDisorder[{{row_count}}][display_order]"
                   id="CommonSystemicDisorder_{{row_count}}_display_order">
        </td>
        <td width="200px">
            <span class="medication-display" style="display:none">
                <a href="javascript:void(0)" class="diagnosis-rename">
                    <i class="oe-i remove-circle small" aria-hidden="true" title="Change disorder"></i>
                </a>
                <span class="diagnosis-name"></span>
            </span>
            <input class="diagnoses-search-autocomplete diagnoses-search-inputfield ui-autocomplete-input"
                   data-saved-diagnoses="" type="text" name="CommonSystemicDisorder[{{row_count}}][disorder_id]"
                   id="CommonSystemicDisorder_{{row_count}}_disorder_id" autocomplete="off">
            <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
            <input type="hidden" name="CommonSystemicDisorder[{{row_count}}][disorder_id]" class="savedDiagnosis"
                   value="">
        </td>
        <td>
            <select name="CommonSystemicDisorder[{{row_count}}][group_id]"
                    id="CommonSystemicDisorder_{{row_count}}_group_id">
                <option value="">-- select --</option>
                {{#group_options}}
                <option value="{{id}}">{{name}}</option>
                {{/group_options}}
            </select>
        </td>
        <td>
            <button type='button'><a href='javascript:void(0)' class='delete'>delete</a></button>
        </td>
    </tr>
</script>