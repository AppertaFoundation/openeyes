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
<?php
$cols_size = 'cols-5';
?>
<h2>Procedure Subspecialty Assignments</h2>
<?php $this->renderPartial('//base/_messages') ?>
<div class="<?= $cols_size ?>">
    <form method="get">
        <table class="cols-4">
            <colgroup>
                <col class="cols-1">
                <col class="cols-4">
            </colgroup>
            <tbody>
                <tr class="col-gap">
                    <td>Subspecialty</td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'subspecialty_id',
                            $subspecialty_id,
                            CHtml::listData($subspecialities, 'id', 'name'),
                            [
                                'empty' => '- Select a subspecialty -',
                            ]
                        ); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php if ($subspecialty_id) { ?>
        <form method="POST" action="/OphTrConsent/oeadmin/ExtraProcedures/EditSubspecialty?subspecialty_id=<?= $subspecialty_id ?>">
            <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>" />
            <?php
            $columns = [
                [
                    'header' => 'Order',
                    'type' => 'raw',
                    'value' => function ($data, $row) {
                        return '<span>&uarr;&darr;</span>' .
                            CHtml::hiddenField(get_class($data) . "[$row][id]", $data->id) .
                            CHtml::hiddenField("display_order[$row]", $data->display_order);
                    },
                    'cssClassExpression' => "'reorder'",
                ],
                [
                    'header' => 'Extra Procedure',
                    'name' => 'OphTrConsent_Extra_Procedure.term',
                    'type' => 'raw',
                    'value' => function ($data, $row) use ($procedure_list) {
                        return CHtml::dropDownList((get_class($data) . "[$row][extra_proc_id]"),
                            $data->extra_proc_id,
                            CHtml::listData($procedure_list, 'id', 'term')
                        );
                    }
                ],
            ];
            $columns[] = [
                'header' => 'Institution',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return $data->institution->name . CHtml::hiddenField(
                        (get_class($data) . "[$row][institution_id]"),
                        $data->institution->id,
                    );
                }
            ];
            $columns[] =
                [
                    'header' => 'Actions',
                    'type' => 'raw',
                    'value' => function ($data) {
                        return '<a href="javascript:void(0)" class="delete">delete</a>';
                    }
                ];

            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $dataProvider,
                'itemsCssClass' => 'generic-admin standard sortable',
                'template' => '{items}',
                "emptyTagName" => 'span',
                'rowHtmlOptionsExpression' => 'array("data-row"=>$row)',
                'enableSorting' => false,
                'columns' => $columns
            ));
            ?>
            <div>
                <button class="button large" type="button" id="add_new">Add</button>&nbsp
                <button class="generic-admin-save button large" name="admin-save" type="submit" id="et_admin-save">Save</button>&nbsp;
            </div>
        </form>
    <?php } ?>
</div>

<script>
    let $table = $('.generic-admin');

    function initialiseRow($row) {
        $row.on('click', 'a.delete', function() {
            $(this).closest('tr').remove();
        });
    }

    $(document).ready(function() {
        $('#subspecialty_id').on('change', function() {
            $(this).closest('form').submit();
        });

        $table.find('tbody tr').each(function() {
            initialiseRow($(this));
        });

        $('#add_new').on('click', function() {
            let $tr = $('table.generic-admin tbody tr');
            let output = Mustache.render($('#procedure_assignment_template').text(), {
                "row_count": OpenEyes.Util.getNextDataKey($tr, 'row'),
                'order_value': parseInt($('table.generic-admin tbody tr:last-child ').find('input[name^="display_order"]').val()) + 1,
                "procedure_options": procedure_options,
                "institution_options": institution_options
            });

            $('table.generic-admin tbody').append(output);

            initialiseRow($('table.generic-admin tbody tr:last-child'));
        });
    });
</script>

<script type="text/template" id="procedure_assignment_template">
    <tr data-row="{{row_count}}" >
        <td class="reorder">
            <span>↑↓</span>
            <input type="hidden" value="" name="<?=$field_name?>[{{row_count}}][id]"
                   id="<?=$field_name?>_{{row_count}}_id">
            <input type="hidden" value="{{order_value}}" name="display_order[{{row_count}}]"
                   id="display_order_{{row_count}}">
        </td>
        <td>
            <select name="<?=$field_name?>[{{row_count}}][extra_proc_id]"
                    id="<?=$field_name?>{{row_count}}_extra_proc_id">
                <option value="">-- select --</option>
                {{#procedure_options}}
                <option value="{{id}}">{{term}}</option>
                {{/procedure_options}}
            </select>
        </td>
        <td>
            <select name="<?=$field_name?>[{{row_count}}][institution_id]"
                    id="<?=$field_name?>{{row_count}}_institution_id">
                {{#institution_options}}
                <option value="{{id}}">{{name}}</option>
                {{/institution_options}}
            </select>
        </td>
        <td>
            <a href="javascript:void(0)" class="delete">delete</a>
        </td>
    </tr>
</script>