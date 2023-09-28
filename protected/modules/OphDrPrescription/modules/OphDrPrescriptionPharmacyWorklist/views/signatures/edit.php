<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<form method="get">
    <table class="cols-7">
        <colgroup>
            <col class="cols-3">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr class="col-gap">
            <td>&nbsp;
                <br/><?=\CHtml::dropDownList(
                    'institution_id',
                    $this->current_institution->id ?? null,
                    Institution::model()->getTenantedList(!Yii::app()->user->checkAccess('admin')),
                    \Yii::app()->user->checkAccess('admin') ? ['empty' => 'All Institutions'] : []
                ) ?>
            </td>
        </tr>
        </tbody>
    </table>
</form>

<form method="POST">
    <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
    <input type="hidden" class="no-clear" name="institution_id" value="<?=$this->current_institution->id ?? null ?>"/>
    <?php
    $columns = [
        [
            'header' => 'Order',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return '<span>&uarr;&darr;</span>' .
                    \CHtml::activeHiddenField($data, "[$row]id");
            },
            'cssClassExpression' => "'reorder cols-1'",
        ],
        [
            'name' => 'name',
            'type' => 'raw',
            'value' => function ($data, $row) {
                return CHtml::activeTextField($data, "[$row]name");
            },
            'cssClassExpression' => "'cols-2'",
        ],
        [
            'class' => 'CDataColumn',
            'header' => 'Active',
            'type' => 'raw',
            'value' => function($data, $row) {
                return CHtml::activeCheckBox($data, "[$row]active");
            },
            'cssClassExpression' => "'cols-1'",
        ],
        [
            'header' => 'Actions',
            'type' => 'raw',
            'value' => function ($data) {
                return '<button type="button" class="js-delete-signatory" data-test="delete-signatory"><a class="delete">delete</a></button>';
            }
        ],
    ];

    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => $data_provider,
        'itemsCssClass' => 'generic-admin standard sortable',
        'template' => '{items}',
        "emptyTagName" => 'span',
        'rowHtmlOptionsExpression' => '["data-row"=>$row, "class" => $data->hasErrors() ? "error":""]',
        'enableSorting' => false,
        'columns' => $columns
    ]);
    ?>
    <div>
        <button class="button large" type="button" data-test="add-new-signatory" id="add_new">Add</button>&nbsp
        <button class="generic-admin-save button large" data-test="save-signatory-form" name="admin-save" type="submit"id="et_admin-save">Save</button>&nbsp;
    </div>
</form>

<script>
    $('.generic-admin.sortable tbody').sortable();
    const table = document.querySelector('table.standard');

    document.getElementById('institution_id').addEventListener('change', function () {
        const form = this.closest('form');
        if (form) {
            form.submit();
        }
    });

    const add_btn = document.getElementById(`add_new`);
    OpenEyes.UI.DOM.addEventListener(add_btn, 'click', null, () => {
        const tbody = table.querySelector('tbody');
        let tr = Mustache.render(document.getElementById('secondary_signatory_template').textContent, {
            "row_count": OpenEyes.Util.getNextDataKey($('table.standard tr'), 'row')
        });
        tbody.insertAdjacentHTML('beforeend', tr);
    });

    OpenEyes.UI.DOM.addEventListener(table, 'click', '.js-delete-signatory', (e) => {
        e.target.closest('tr').remove();
    });

</script>

<script type="text/template" id="secondary_signatory_template">
    <tr data-row="{{row_count}}">
        <td class="reorder">
            <span>↑↓</span>
            <input type="hidden" value="" name="SecondarySignatory[{{row_count}}][id]"
                   id="SecondarySignatory_{{row_count}}_id">
            <input type="hidden" value="{{order_value}}" name="display_order[{{row_count}}]"
                   id="display_order_{{row_count}}">
        </td>
        <td>
            <input name="SecondarySignatory[{{row_count}}][name]"
                   id="SecondarySignatory{{row_count}}_name" type="text" value="">
        </td>
        <td>
            <input id="ytSecondarySignatory_{{row_count}}_active" type="hidden" value="0" name="SecondarySignatory[{{row_count}}][active]">
            <input name="SecondarySignatory[{{row_count}}][active]" id="SecondarySignatory_{{row_count}}_active" value="1" checked="checked" type="checkbox">
        </td>
        <td>
            <button type="button" class="js-delete-signatory" data-test="delete-signatory"><a class="delete">delete</a></button>
        </td>
    </tr>
</script>
