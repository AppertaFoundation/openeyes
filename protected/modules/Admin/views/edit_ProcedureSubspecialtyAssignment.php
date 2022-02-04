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
$enable_eur = SettingMetadata::model()->getSetting('cataract_eur_switch');
$cols_size = (int)$subspecialty_id === 4 && $enable_eur === 'on' ? 'cols-9' : 'cols-5';
?>
<h2>Procedure Subspecialty Assignments</h2>
<?php $this->renderPartial('//base/_messages') ?>
<div class="<?=$cols_size?>">
    <form method="get">
        <table class="cols-4">
            <colgroup>
                <col class="cols-1">
                <col class="cols-4">
            </colgroup>
            <tbody>
            <tr class="col-gap">
                <?php if ($this->checkAccess('admin')):?>
                <td>Institution</td>
                <td><?=\CHtml::dropDownList('institution_id', $institution_id, \CHtml::listData($institutions, 'id', 'name'));?></td>
                <?php endif;?>
                <td>Subspecialty</td>
                <td>
                    <?=\CHtml::dropDownList(
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
        <form method="POST" action="/Admin/procedureSubspecialtyAssignment/edit?subspecialty_id=<?= $subspecialty_id ?>&institution_id=<?=$institution_id ?>">
            <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
              <?php
                $columns = [
                    [
                    'header' => 'Order',
                    'type' => 'raw',
                    'value' => function ($data, $row) {
                        return '<span>&uarr;&darr;</span>' .
                        CHtml::hiddenField("ProcedureSubspecialtyAssignment[$row][id]", $data->id) .
                        CHtml::hiddenField("display_order[$row]", $data->display_order);
                    },
                    'cssClassExpression' => "'reorder'",
                    ],
                    [
                    'header' => 'Procedure',
                    'name' => 'procedure.term',
                    'type' => 'raw',
                    'value' => function ($data, $row) use ($procedure_list) {
                        return CHtml::dropDownList((get_class($data) . "[$row][procedure_id]"), $data->procedure->id,
                            CHtml::listData($procedure_list, 'id', 'term'));
                    }
                    ],
                ];
                //
                if ((int)$subspecialty_id === 4 && $enable_eur === 'on') {
                    $columns[] = [
                        'header' => 'Require Effective Use of Resources (EUR) assessment',
                        'type' => 'raw',
                        'value' => function ($data, $row) {
                            return CHtml::dropDownList(
                                (get_class($data) . "[$row][need_eur]"),
                                $data->need_eur,
                                ['NO', 'YES'],
                                array('class' => 'cols-full')
                            );
                        }
                    ];
                }
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
        $row.on('click', 'a.delete', function () {
            $(this).closest('tr').remove();
        });

        const $institution = $row.find("select[id$='institution_id']");

        if ($institution.length) {
            $institution.val(<?=$institution_id;?>);
            // "selected" attribute does not set automatically
            $institution.find('option[value="<?=$institution_id;?>"]').attr("selected","selected");

            // disable all other option
            $institution[0].querySelectorAll(':not([selected])').forEach(option => {
                option.disabled = true;
            });
        }
    }

    $(document).ready(function () {
        $('#subspecialty_id, #institution_id').on('change', function () {
            $(this).closest('form').submit();
        });

        $table.find('tbody tr').each(function () {
            initialiseRow($(this));
        });

        $('#add_new').on('click', function () {
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
            <input type="hidden" value="" name="ProcedureSubspecialtyAssignment[{{row_count}}][id]"
                   id="ProcedureSubspecialtyAssignment_{{row_count}}_id">
            <input type="hidden" value="{{order_value}}" name="display_order[{{row_count}}]"
                   id="display_order_{{row_count}}">
        </td>
        <td>
            <select name="ProcedureSubspecialtyAssignment[{{row_count}}][procedure_id]"
                    id="ProcedureSubspecialtyAssignment{{row_count}}_procedure_id">
                <option value="">-- select --</option>
                {{#procedure_options}}
                <option value="{{id}}">{{term}}</option>
                {{/procedure_options}}
            </select>
        </td>
        <td>
            <select name="ProcedureSubspecialtyAssignment[{{row_count}}][institution_id]"
                    id="ProcedureSubspecialtyAssignment{{row_count}}_institution_id">
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