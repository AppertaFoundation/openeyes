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
 */

?>

<div class="cols-5">

    <h2>Secondary Common Ophthalmic Disorder</h2>

    <?php
    foreach (Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash- alert-box with-icon warning' . $key . '">' . $message . "</div>\n";
    } ?>

    <form method="get">
        <table class="standard">
            <tbody>
            <tr class="col-gap">
                <td>Parent</td>
                <td>
                    <?=\CHtml::dropDownList(
                        'parent_id',
                        (isset($_GET['parent_id']) ? $_GET['parent_id'] : null),
                        CHtml::listData(
                            array_filter(
                                CommonOphthalmicDisorder::model()->with('disorder')->findAll([ 'condition' => 'disorder_id IS NOT NULL OR finding_id IS NOT NULL', 'group' => 'disorder.term, subspecialty_id', 'distinct' => true,'order' => 'disorder.term ASC']),
                                static function ($item) {
                                    return $item->disorder !== null || $item->finding !== null;
                                } // Exclude inactive entries
                            ),
                            'id',
                            static function ($item) {
                                if ($item->disorder !== null) {
                                    return $item->disorder->term . ' (' . $item->subspecialty->name . ')';
                                } else {
                                    return $item->finding->name . ' (' . $item->subspecialty->name . ')';
                                }
                            }
                        )
                    ); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

    <form method="POST" action="/admin/editSecondaryToCommonOphthalmicDisorder?parent_id=<?=$parent_id;?>">
        <input type="hidden" class="no-clear"
               name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
        <?php
        $columns = array(
            array(
                'header' => 'Order',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return '<span>&uarr;&darr;</span>' .
                        CHtml::hiddenField("SecondaryToCommonOphthalmicDisorder[$row][id]", $data->id) .
                        CHtml::hiddenField("display_order[$row]", $data->display_order);
                },
                'cssClassExpression' => "'reorder'",
            ),
            array(
                'header' => 'Disorder',
                'name' => 'disorder.term',
                'type' => 'raw',
                'htmlOptions' => array('width' => '200px'),
                'value' => function ($data, $row) {
                    $term = null;
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
                }
            ),
            array(
                'header' => 'Finding',
                'name' => 'finding.name',
                'type' => 'raw',
                'value' => function ($data, $row) {

                    $finding_data = array(
                        'id' => isset($data->id) ? $data->id : null,
                        'name' => isset($data->finding) ? $data->finding->name : null,
                        'finding_id' => isset($data->finding) ? $data->finding->id : null,
                    );

                    $remove_a = CHtml::tag(
                        'a',
                        array('href' => 'javascript:void(0)', 'class' => 'finding-rename'),
                        Chtml::tag(
                            'i',
                            [
                                'class' => 'oe-i remove-circle small',
                                'aria-hidden' => "true",
                                'title' => "Change finding"
                            ],
                            null
                        )
                    );

                    $name_span = CHtml::tag('span', array('class' => 'finding-name name'), $finding_data['name']);
                    $rename_span = CHtml::tag('span', array(
                        'class' => "finding-display display",
                        'style' => 'display: ' . ($finding_data['finding_id'] ? 'inline' : 'none') . ';'
                    ), $remove_a . ' ' . $name_span);

                    $input = CHtml::textField(
                        "SecondaryToCommonOphthalmicDisorder[$row][finding_id]",
                        $finding_data['name'],
                        [
                            'class' => 'finding-search-autocomplete finding-search-inputfield ui-autocomplete-input',
                            'style' => 'display: ' . ($finding_data['finding_id'] ? 'none' : 'inline') . ';',
                            'autocomplete' => 'off',
                        ]
                    );

                    $hidden_finding_input = CHtml::hiddenField(
                        "SecondaryToCommonOphthalmicDisorder[$row][finding_id]",
                        $finding_data['finding_id'],
                        array('class' => 'finding-id')
                    );

                    return $rename_span . $input . $hidden_finding_input;
                }
            ),
            array(
                'name' => 'letter_macro_text',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::activeTextField($data, "[$row]letter_macro_text");
                }
            ),
            array(
                'header' => 'Actions',
                'type' => 'raw',
                'value' => function ($data) {
                    return '<a href="javascript:void(0)" class="delete button large">delete</a>';
                }
            ),
            array(
                'header' => 'Assigned to current institution',
                'type' => 'raw',
                'name' => 'assigned_insitution',
                'value' => function ($data, $row) {
                    return CHtml::checkBox("assigned_institution[$row]", $data->hasMapping(ReferenceData::LEVEL_INSTITUTION, $data->getIdForLevel(ReferenceData::LEVEL_INSTITUTION)));
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
            <button class="generic-admin-save button large" name="admin-save"
                    type="submit" id="et_admin-save">Save</button>&nbsp;
        </div>
    </form>

</div>

<script>
    var $table = $('.generic-admin');

    function initialiseRow($row){
        var DiagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
            'inputField': $row.find('.diagnoses-search-autocomplete'),
            'fieldPrefix': 'SecondaryToCommonOphthalmicDisorder',
            'renderCommonlyUsedDiagnoses': false,
            'code': '',
            'singleTemplate' :
            "<span class='medication-display' style='display:none'>" + "<a href='javascript:void(0)' class='diagnosis-rename'><i class='oe-i remove-circle small' aria-hidden='true' title='Change diagnosis'></i></a> " +
            "<span class='diagnosis-name'></span></span>" +
            "<select class='commonly-used-diagnosis cols-full' style='display:none'></select>" +
            "{{{input_field}}}" +
            "<input type='hidden' name='{{field_prefix}}[" + $row.data('row') + "][disorder_id]' class='savedDiagnosis' value=''>"
        });

        // Init finding, unfortunately we cannot use DiagnosesSearchController for this
        initTriggers($row, 'finding');
    }

    $(document).ready(function(){
        $('#parent_id').on('change', function(){
            $(this).closest('form').submit();
        });

        $table.find('tbody tr').each(function() {
            initialiseRow($(this));
        });


        $('#add_new').on('click', function(){
            var $tr =  $('table.generic-admin tbody tr');
            var output = Mustache.render($('#common_ophthalmic_disorder_template').text(),{
                "row_count": OpenEyes.Util.getNextDataKey($tr, 'row'),
                'even_odd': $tr.length % 2 ? 'odd' : 'even',
                'order_value': function() {
                    let raw_value = $('table.generic-admin tbody tr:last-child ').find('input[name^="display_order"]').val();
                    if(raw_value === undefined) {
                        raw_value = 0;
                    }
                    let parsed_value = parseInt(raw_value);
                    return parsed_value + 1;
                }
            });

            $('table.generic-admin tbody').append(output);
            initialiseRow($('table.generic-admin tbody tr:last-child'));
        });
    });

    function initTriggers($row, selector){

        var $inputField = $row.find('.' + selector + '-search-inputfield');
        $row.on('click', '.' + selector + '-rename', function(){
            $inputField.show();
            $(this).closest('.' + selector + '-display').hide();

            $row.find('.' + selector + '-id').val('');

            $inputField.val('');
            $inputField.focus();
        });

        $row.on('click', 'a.delete',function(){
            $(this).closest('tr').remove();
        });

        // Autocomplete

        $inputField.autocomplete({
            minLength: 2,
            delay: 700,
            source: function (request, response) {
                $.ajax({
                    'url': '/autocomplete/search',
                    'type':'GET',
                    'data':{
                        'term': request.term,
                        'model': selector === 'finding' ? 'Finding' : 'Disorder', //yes, I know, this is awful
                        'field': selector === 'finding' ? 'name' : 'term',
                    },
                    'beforeSend': function(){
                    },
                    'success':function(data) {
                        data = $.parseJSON(data);
                        response(data);
                    }
                });
            },
            search: function () {
                $inputField.addClass('inset-loader');
            },
            select: function(event, ui){
                //controller.addDiagnosis(null, ui.item);
                $row.find('.' + selector + '-name').text(ui.item.label);
                $row.find('.' + selector + '-id').val(ui.item.id);

                $row.find('.' + selector + '-search-inputfield').hide();
                $row.find('.' + selector + '-display').show();


                //clear input
                $(this).val("");
                return false;
            },
            response: function (event, ui) {
                $inputField.removeClass('inset-loader');
            }
        });
    }
</script>

<script type="text/template" id="common_ophthalmic_disorder_template">
    <tr data-row="{{row_count}}" class="{{even_odd}}">
        <td class="reorder">
            <span>↑↓</span>
            <input type="hidden" value="" name="SecondaryToCommonOphthalmicDisorder[{{row_count}}][id]" id="SecondaryToCommonOphthalmicDisorder_{{row_count}}_id">
            <input type="hidden" value="{{order_value}}" name="display_order[{{row_count}}]" id="display_order_{{row_count}}">
        </td>
        <td width="200px">
            <span class="medication-display" style="display:none">
                <a href="javascript:void(0)" class="diagnosis-rename"><i class="oe-i remove-circle small" aria-hidden="true" title="Change diagnosis"></i></a>
                <span class="diagnosis-name"></span>
            </span>
            <input class="diagnoses-search-autocomplete diagnoses-search-inputfield ui-autocomplete-input"
                   data-saved-diagnoses="" type="text" autocomplete="off">
            <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
            <input type="hidden" name="SecondaryToCommonOphthalmicDisorder[{{row_count}}][disorder_id]" class="savedDiagnosis" value="">
        </td>
        <td>
            <span class="finding-display display" style="display: none;">
                <a href="javascript:void(0)" class="finding-rename">
                    <i class="oe-i remove-circle small" aria-hidden="true" title="Change finding"></i>
                </a>
                <span class="finding-name name"></span>
            </span>
            <input class="finding-search-autocomplete finding-search-inputfield ui-autocomplete-input"
                   style="display: block;" autocomplete="off" type="text" value=""
                   name="SecondaryToCommonOphthalmicDisorder[{{row_count}}][finding_id]" id="SecondaryToCommonOphthalmicDisorder_{{row_count}}_finding_id">
            <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
            <input class="finding-id" type="hidden" value="" name="SecondaryToCommonOphthalmicDisorder[{{row_count}}][finding_id]" id="SecondaryToCommonOphthalmicDisorder_{{row_count}}_finding_id">
        </td>
        <td>
            <input name="SecondaryToCommonOphthalmicDisorder[{{row_count}}][letter_macro_text]" id="SecondaryToCommonOphthalmicDisorder_{{row_count}}_letter_macro_text" type="text" value="">
        </td>
        <td>
            <a href="javascript:void(0)" class="delete button large">delete</a>
        </td>
    </tr>
</script>
