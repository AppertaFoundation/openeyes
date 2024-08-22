<?php
/**
 * (C) OpenEyes Foundation, 2018
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
 *
 * @var AdminController $this
 */

Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.GenericFormJSONConverter.js'), CClientScript::POS_HEAD);
foreach (Yii::app()->user->getFlashes() as $key => $message) {
    echo '<div class="flash- alert-box with-icon warning' . $key . '">' . $message . "</div>\n";
}
if ($this->checkAccess('admin')) {
    $options = array('empty' => 'All Institutions');
} else {
    $options = array();
}
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
                Institution::model()->getTenantedList(!Yii::app()->user->checkAccess('admin')),
                $options
            ) ?>
            </td>
            <td>
                <small>Subspeciality</small><br/>
                <?=\CHtml::dropDownList(
                    'subspecialty_id',
                    $subspecialty_id,
                    CHtml::listData($subspecialty, 'id', 'name'),
                    array('empty' => 'All subspecialties')
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>
</form>

<div class="cols-8">
    <form method="POST" action="/admin/editcommonophthalmicdisordergroups?institution_id=<?= $current_institution_id ?>&subspecialty_id=<?= $subspecialty_id; ?>">
        <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <?php
        $columns = array(
            array(
                'header' => 'Order',
                'type' => 'raw',
                'htmlOptions' => array('width' => '50px'),
                'value' => function ($data, $row) {
                    return '<span>&uarr;&darr;</span>' .
                        CHtml::hiddenField("CommonOphthalmicDisorderGroup[$row][id]", $data->id) .
                        CHtml::hiddenField("display_order[$row]", $data->display_order);
                },
                'cssClassExpression' => "'reorder'",
            ),
            array(
                'header' => 'Group Name',
                'type' => 'raw',
                'htmlOptions' => array('width' => '420px'),
                'value' => function ($data, $row) {
                    return CHtml::textField(
                        "CommonOphthalmicDisorderGroup[$row][name]",
                        $data->name,
                        array('style' => 'width:400px', 'data-test' => 'group-name')
                    );
                }
            ),
            array(
                'header' => 'Institution',
                'name' => 'institution.name',
                'type' => 'raw',
                'htmlOptions' => array('width' => '300px'),
                'value' => function ($data, $row) use ($options, $active_group_ids) {
                    if (in_array($data->id, $active_group_ids)) {
                        if ($data->institution_id) {
                            $institution = Institution::model()->findByPk($data->institution_id);
                            $str = $institution->name;
                        } else {
                            $str = 'All institutions';
                        }
                        return $str
                            . '<span data-tooltip-content="This group has disorders assigned to it so can\'t be reassigned." class="oe-i info small js-has-tooltip"></span>';
                    }
                    if ($this->checkAccess('admin')) {
                        $institutions = CHtml::listData(Institution::model()->getTenanted(), 'id', 'name');
                        return CHtml::activeDropDownList($data, "[$row]institution_id", $institutions, $options);
                    } else {
                        if ($data->institution_id) {
                            $institution = Institution::model()->findByPk($data->institution_id);
                            return $institution->name;
                        } else {
                            return 'All institutions';
                        }
                    }
                }
            ),
            array(
                'header' => 'Subspecialty',
                'type' => 'raw',
                'htmlOptions' => array('width' => '120px'),
                'value' => function ($data, $row) {
                    $subspecialty = Subspecialty::model()->findByPk($data->subspecialty_id);
                    return $subspecialty->name ?? '-';
                }
            ),
            array(
                'header' => 'Actions',
                'type' => 'raw',
                'value' => function ($data) use ($active_group_ids) {
                    if (!in_array($data->id, $active_group_ids)) {
                        return '<button type="button"><a href="javascript:void(0)" class="delete">delete</a></button>';
                    } else {
                        return '<span data-tooltip-content="This group has disorders assigned to it so can\'t be deleted." class="oe-i info small js-has-tooltip"></span>';
                    }
                }
            ),
        );

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
            <button class="generic-admin-save button large" name="admin-save" type="submit"id="et_admin-save">Save</button>&nbsp;
        </div>
    </form>
</div>

<script>

    let formStructure = {
        'name' : 'CommonOphthalmicDisorderGroups',
        'tableSelector': '.generic-admin.standard.sortable',
        'rowSelector': 'tr',
        'rowIdentifier': 'row',
        'structure': {
            'CommonOphthalmicDisorderGroup[ROW_IDENTIFIER][id]' : '',
            'display_order[ROW_IDENTIFIER]' : '',
            'CommonOphthalmicDisorderGroup[ROW_IDENTIFIER][name]' : '#CommonOphthalmicDisorderGroup_ROW_IDENTIFIER_name',
            'CommonOphthalmicDisorderGroup[ROW_IDENTIFIER][institution_id]' : '#CommonOphthalmicDisorderGroup_ROW_IDENTIFIER_institution_id'
        }
    };
    let GenericFormJSONConverter = new OpenEyes.GenericFormJSONConverter(formStructure);
    let $table = $('.generic-admin');

    function initialiseRow($row) {
        initTriggers($row, 'finding');
    }

    $(document).ready(function () {
        let formSubmitted = false;

        $('.generic-admin-save').on('click', function (e) {
            if(formSubmitted) {
                formSubmitted = false;
                return;
            }

            e.preventDefault();

            formSubmitted = true;
            GenericFormJSONConverter.convert();

            // only if there are no errors in the page proceed to save
            $(this).trigger('click');
        });

        $('#institution_id').on('change', function () {
            $(this).closest('form').submit();
        });

        $('#subspecialty_id').on('change', function () {
            $(this).closest('form').submit();
        });

        $table.find('tbody tr').each(function () {
            initialiseRow($(this));
        });

        $('.sortable tbody').sortable({
            stop: function(e, ui) {
                $('.sortable tbody tr').each(function(index) {
                    $(this).find("[name^='display_order']").val(index);
                });
            }
        });

        $('#add_new').on('click', function () {
            let $tr = $('table.generic-admin tbody tr');
            const $last_order_input = document.querySelector('table.generic-admin tbody tr:last-child input[name^="display_order"]');
            const order_value = $last_order_input ? +$last_order_input.value + 1 : 0;
            let institution_id = $('#institution_id').val()

            let output = Mustache.render($('#common_ophthalmic_disorder_group_template').text(), {
                "row_count": OpenEyes.Util.getNextDataKey($tr, 'row'),
                'even_odd': $tr.length % 2 ? 'odd' : 'even',
                'order_value': order_value,
                'institution_id': institution_id,
                'institution_name': $('#institution_id option[value=' + institution_id + ']').text()
            });

            $('table.generic-admin tbody').append(output);

            initialiseRow($('table.generic-admin tbody tr:last-child'));
        });

        $('.tool-tip').tooltip();
    });

    function initTriggers($row) {
        $row.on('click', 'a.delete', function () {
            $(this).closest('tr').remove();
        });
    }
</script>

<script type="text/template" id="common_ophthalmic_disorder_group_template">
    <tr data-row="{{row_count}}" class="{{even_odd}}">
        <td class="reorder">
            <span>↑↓</span>
            <input type="hidden" value="" name="CommonOphthalmicDisorderGroup[{{row_count}}][id]"
                   id="CommonOphthalmicDisorderGroup_{{row_count}}_id">
            <input type="hidden" value="{{order_value}}" name="display_order[{{row_count}}]"
                   id="display_order_{{row_count}}">
        </td>
        <td width="400px">
            <input type="text" name="CommonOphthalmicDisorderGroup[{{row_count}}][name]"
                   id="CommonOphthalmicDisorderGroup_{{row_count}}_name" autocomplete="off" style="width:400px;">
        </td>
        <td>
            <input type="hidden" name="CommonOphthalmicDisorderGroup[{{row_count}}][institution_id]"
                   id="CommonOphthalmicDisorderGroup_{{row_count}}_institution_id" value="{{institution_id}}">
            {{institution_name}}
        </td>
        <td width="120px">
        </td>
        <td>
            <button type="button"><a href="javascript:void(0)" class="delete">delete</a></button>
        </td>
    </tr>
</script>