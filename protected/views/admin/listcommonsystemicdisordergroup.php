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
<h2>Common Systemic Disorder Groups</h2>
<?php
$this->renderPartial('//base/_messages');
if (isset($errors)) {
    $this->renderPartial('/admin/_form_errors', $errors);
}
?>

<div class="alert-box error with-icon js-admin-errors" style="display:none">
    <p>Could not be deleted:</p>
    <div class="js-admin-error-container"></div>
</div>

<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.GenericFormJSONConverter.js'), CClientScript::POS_HEAD);
foreach (Yii::app()->user->getFlashes() as $key => $message) {
    echo '<div class="flash- alert-box with-icon warning' . $key . '">' . $message . "</div>\n";
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
                    Institution::model()->getTenantedList(!Yii::app()->user->checkAccess('admin'))
                ) ?></td>
        </tr>
        </tbody>
    </table>
</form>

<div class="cols-8">
    <form method="POST" action="/oeadmin/CommonSystemicDisorderGroup/save?institution_id=<?= $current_institution_id; ?>">
        <input type="hidden" class="no-clear" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <?php
        $columns = array(
            array(
                'header' => 'Order',
                'type' => 'raw',
                'htmlOptions' => array('width' => '50px'),
                'value' => function ($data, $row) {
                    return '<span>&uarr;&darr;</span>' .
                        CHtml::hiddenField("CommonSystemicDisorderGroup[$row][id]", $data->id) .
                        CHtml::hiddenField("display_order[$row]", $data->display_order);
                },
                'cssClassExpression' => "'reorder'",
            ),
            array(
                'header' => 'Group Name',
                'type' => 'raw',
                'htmlOptions' => array('width' => '420px'),
                'value' => function ($data, $row) {
                    return CHtml::textField("CommonSystemicDisorderGroup[$row][name]", $data->name, array('style' => 'width:400px'));
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
        'name' : 'CommonSystemicDisorderGroups',
        'tableSelector': '.generic-admin.standard.sortable',
        'rowSelector': 'tr',
        'rowIdentifier': 'row',
        'structure': {
            'CommonSystemicDisorderGroup[ROW_IDENTIFIER][id]' : '',
            'display_order[ROW_IDENTIFIER]' : '',
            'CommonSystemicDisorderGroup[ROW_IDENTIFIER][name]' : '#CommonSystemicDisorderGroup_ROW_IDENTIFIER_name'
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

            let output = Mustache.render($('#common_systemic_disorder_group_template').text(), {
                "row_count": OpenEyes.Util.getNextDataKey($tr, 'row'),
                'even_odd': $tr.length % 2 ? 'odd' : 'even',
                'order_value': order_value
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

<script type="text/template" id="common_systemic_disorder_group_template">
    <tr data-row="{{row_count}}" class="{{even_odd}}">
        <td class="reorder">
            <span>↑↓</span>
            <input type="hidden" value="" name="CommonSystemicDisorderGroup[{{row_count}}][id]"
                   id="CommonSystemicDisorderGroup_{{row_count}}_id">
            <input type="hidden" value="{{order_value}}" name="display_order[{{row_count}}]"
                   id="display_order_{{row_count}}">
        </td>
        <td width="400px">
            <input type="text" name="CommonSystemicDisorderGroup[{{row_count}}][name]"
                   id="CommonSystemicDisorderGroup_{{row_count}}_name" autocomplete="off" style="width:400px;">
        </td>
        <td>
            <button type="button"><a href="javascript:void(0)" class="delete">delete</a></button>
        </td>
    </tr>
</script>
