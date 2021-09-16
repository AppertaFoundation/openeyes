<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="search-filters">
    <?php $this->beginWidget('CActiveForm', array(
        'id' => 'cvi-filter',
        'htmlOptions' => array(
            'class' => 'row',
        ),
        'enableAjaxValidation' => false,
    )) ?>
    <div class="large-12 column">
        <div class="panel box js-toggle-container">
            <h3>Filter CVIs</h3>
            <a href="#" class="toggle-trigger toggle-hide js-toggle">
                    <span class="icon-showhide">
                        Show/hide this section
                    </span>
            </a>
            <div class=" js-toggle-body">
                <div class="row">
                    <div class="column large-3">
                        <div class="row">
                            <div class="column large-4 text-right"><label for="date_from">Date From:</label></div>
                            <div class="column large-8">
                                <input type="text" id="date_from" name="date_from" class="datepicker filter-field"
                                       value="<?= array_key_exists('date_from', $list_filter) ? $list_filter['date_from'] : ''; ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="column large-4 text-right"><label for="date_to">Date To:</label></div>
                            <div class="column large-8">
                                <input type="text" id="date_to" name="date_to" class="datepicker filter-field"
                                       value="<?= array_key_exists('date_to', $list_filter) ? $list_filter['date_to'] : ''; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="column large-3">
                        <div class="row">
                            <div class="column large-4 text-right"><label for="subspecialty_id">Subspecialty:</label>
                            </div>
                            <div class="column large-8">
                                <?php echo CHtml::dropDownList('subspecialty_id',
                                    (array_key_exists('subspecialty_id', $list_filter) ? $list_filter['subspecialty_id'] : null),
                                    Subspecialty::model()->getList(),
                                    array('class' => 'filter-field', 'empty' => 'All specialties',)) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="column large-4 text-right"><label for="site_id">Site:</label></div>
                            <div class="column large-8">
                                <?php echo CHtml::dropDownList('site_id',
                                    (array_key_exists('site_id', $list_filter) ? $list_filter['site_id'] : null),
                                    Site::model()->getListForCurrentInstitution(),
                                    array('class' => 'filter-field', 'empty' => 'All sites',)) ?>
                            </div>
                        </div>
                    </div>
                    <div class="column large-3">
                        <div class="column large-4 text-right"><label for="createdby">Created By:</label></div>
                        <div class="column large-8"><?php
                            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                'id' => 'createdby_auto_complete',
                                'name' => 'createdby_auto_complete',
                                'value' => '',
                                'source' => "js:function(request, response) {
                                    var existing = [];
                                    $('#createdby_list').children('li').map(function() {
                                        existing.push(String($(this).data('id')));
                                    });

                                    $.ajax({
                                        'url': '" . Yii::app()->createUrl('user/autocomplete') . "',
                                        'type':'GET',
                                        'data':{'term': request.term},
                                        'success':function(data) {
                                            data = $.parseJSON(data);

                                            var result = [];

                                            for (var i = 0; i < data.length; i++) {
                                                var index = $.inArray(data[i].id, existing);
                                                if (index == -1) {
                                                    result.push(data[i]);
                                                }
                                            }

                                            response(result);
                                        }
                                    });
                                    }",
                                'options' => array(
                                    'minLength' => '3',
                                    'select' => "js:function(event, ui) {
                                    addCreatedByToList(ui.item);
                                    $('#createdby_auto_complete').val('');
                                    return false;
                                }",
                                ),
                                'htmlOptions' => array(
                                    'placeholder' => 'type to search for users',
                                ),
                            ));
                            ?>
                            <div>
                                <ul id="createdby_list" class="multi-select-search scroll">
                                    <?php $createdby_ids = array_key_exists('createdby_ids', $list_filter) ? $list_filter['createdby_ids'] : '';
                                    if ($createdby_ids) {
                                        foreach (explode(',', $createdby_ids) as $id) {
                                            if ($user = User::model()->findByPk($id)) { ?>
                                                <li data-id="<?= CHtml::encode($id) ?>"><?= CHtml::encode($user->getReversedFullname()) ?><a href="#"
                                                                                                               class="remove">X</a>
                                                </li>
                                            <?php }
                                        }
                                    } ?>
                                </ul>
                            </div>
                            <?= CHtml::hiddenField('createdby_ids', $this->request->getPost('createdby_ids', ''), array('class' => 'filter-field')); ?>
                        </div>
                    </div>

                    <div class="column large-3">
                        <div class="column large-4 text-right"><label for="consultants">Consultant signed by:</label>
                        </div>
                        <div class="column large-8"><?php
                            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                'id' => 'consultant_auto_complete',
                                'name' => 'consultant_auto_complete',
                                'value' => '',
                                'source' => "js:function(request, response) {
                                    var existing = [];
                                    $('#consultant_list').children('li').map(function() {
                                        existing.push(String($(this).data('id')));
                                    });

                                    $.ajax({
                                        'url': '" . Yii::app()->createUrl('user/autocomplete') . "',
                                        'type':'GET',
                                        'data':{'term': request.term},
                                        'success':function(data) {
                                            data = $.parseJSON(data);

                                            var result = [];

                                            for (var i = 0; i < data.length; i++) {
                                                var index = $.inArray(data[i].id, existing);
                                                if (index == -1) {
                                                    result.push(data[i]);
                                                }
                                            }

                                            response(result);
                                        }
                                    });
                                    }",
                                'options' => array(
                                    'minLength' => '3',
                                    'select' => "js:function(event, ui) {
                                    addConsultantToList(ui.item);
                                    $('#consultant_auto_complete').val('');
                                    return false;
                                }",
                                ),
                                'htmlOptions' => array(
                                    'placeholder' => 'type to search for users',
                                ),
                            ));
                            ?>
                            <div>
                                <ul id="consultant_list" class="multi-select-search scroll">
                                    <?php $consultant_ids = array_key_exists('consultant_ids', $list_filter) ? $list_filter['consultant_ids'] : '';
                                    if ($consultant_ids) {
                                        foreach (explode(',', $consultant_ids) as $id) {
                                            if ($user = User::model()->findByPk($id)) { ?>
                                                <li data-id="<?= CHtml::encode($id) ?>"><?= CHtml::encode($user->getReversedFullname()) ?><a href="#"
                                                                                                               class="remove">X</a>
                                                </li>
                                            <?php }
                                        }
                                    } ?>
                                </ul>
                            </div>
                            <?= CHtml::hiddenField('consultant_ids', $this->request->getPost('consultant_ids', ''), array('class' => 'filter-field')); ?>
                        </div>
                    </div>
                    <div class="column large-3">
                        <div class="row">
                            <div class="column large-4 text-right"><label for="consultants">Consultant in
                                    charge:</label></div>
                            <div class="column large-8"><?php
                                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                    'id' => 'firm_auto_complete',
                                    'name' => 'firm_auto_complete',
                                    'value' => '',
                                    'source' => "js:function(request, response) {
                                    var existing = [];
                                    $('#firm_list').children('li').map(function() {
                                        existing.push(String($(this).data('id')));
                                    });
                                    var subspecialty_id = $('#subspecialty_id').val();
                                    $.ajax({
                                        'url': '" . Yii::app()->createUrl('OphCoCvi/admin/firmautocomplete') . "',
                                        'type':'GET',
                                        'data':{'term': request.term, 'subspecialty_id': subspecialty_id},
                                        'success':function(data) {
                                            data = $.parseJSON(data);

                                            var result = [];

                                            for (var i = 0; i < data.length; i++) {
                                                var index = $.inArray(data[i].id, existing);
                                                if (index == -1) {
                                                    result.push(data[i]);
                                                }
                                            }

                                            response(result);
                                        }
                                    });
                                    }",
                                    'options' => array(
                                        'minLength' => '3',
                                        'select' => "js:function(event, ui) {
                                    addFirmToList(ui.item);
                                    $('#firm_auto_complete').val('');
                                    return false;
                                }",
                                    ),
                                    'htmlOptions' => array(
                                        'placeholder' => 'type to search for consultants',
                                    ),
                                ));
                                ?>
                                <div>
                                    <ul id="firm_list" class="multi-select-search scroll">
                                        <?php $firm_ids = array_key_exists('firm_ids', $list_filter) ? $list_filter['firm_ids'] : '';
                                        if ($firm_ids) {
                                            foreach (explode(',', $firm_ids) as $id) {
                                                if ($firm = Firm::model()->findByPk($id)) { ?>
                                                    <li data-id="<?= CHtml::encode($id) ?>"><?= CHtml::encode($firm->getNameAndSubspecialty()) ?><a
                                                                href="#" class="remove">X</a></li>
                                                <?php }
                                            }
                                        } ?>
                                    </ul>
                                </div>
                                <?= CHtml::hiddenField('firm_ids', $this->request->getPost('firm_ids', ''), array('class' => 'filter-field')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="column large-3">
                        <div class="row">
                            <div class="column large-6 text-right"><label for="show_issued">Show Issued:</label></div>
                            <div class="column large-6">
                                <?php
                                $show_issued = array_key_exists('show_issued', $list_filter) ? $list_filter['show_issued'] : false;
                                echo CHtml::checkBox('show_issued', ($show_issued == 1), array('class' => 'filter-field'));
                                ?>                            </div>
                        </div>
                        <div class="row">
                            <div class="column large-6 text-right"><label for="issue_complete">Complete:</label></div>
                            <div class="column large-6">
                                <?php
                                $issue_complete = array_key_exists('issue_complete', $list_filter) ? $list_filter['issue_complete'] : true;
                                echo CHtml::checkBox('issue_complete', ($issue_complete == 1), array('class' => 'filter-field'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="column large-6 text-right"><label for="issue_incomplete">Incomplete:</label>
                            </div>
                            <div class="column large-6">
                                <?php
                                $issue_incomplete = array_key_exists('issue_incomplete', $list_filter) ? $list_filter['issue_incomplete'] : true;
                                echo CHtml::checkBox('issue_incomplete', ($issue_incomplete == 1), array('class' => 'filter-field'));
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="column large-3">
                        
                        <div class="row">
                            <div class="column large-6 text-right"><label for="missing_consultant_signature">Missing Consultant Signature:</label></div>
                            <div class="column large-6">
                                <?php
                                $missing_consultant_signature = array_key_exists('missing_consultant_signature', $list_filter) ? $list_filter['missing_consultant_signature'] : false;
                                echo CHtml::checkBox('missing_consultant_signature', ($missing_consultant_signature == 1), array('class' => 'filter-field'));
                                ?>                            </div>
                        </div>
                        <div class="row">
                            <div class="column large-6 text-right"><label for="missing_clerical_part">Missing Clerical Part:</label></div>
                            <div class="column large-6">
                                <?php
                                $missing_clerical_part = array_key_exists('missing_clerical_part', $list_filter) ? $list_filter['missing_clerical_part'] : false;
                                echo CHtml::checkBox('missing_clerical_part', ($missing_clerical_part == 1), array('class' => 'filter-field'));
                                ?>                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="column large-12 text-right end">
                            <button id="export_csv" class="primary small" type="button">Export</button>
                            <?php if ($this->isListFiltered()) { ?>
                                <button id="reset_button" class="warning small" type="submit">Reset</button>
                            <?php } ?>
                            <button id="search_button" class="secondary small" type="submit" disabled="true">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->endWidget() ?>
    </div>

    <script type="text/javascript">
        function addConsultantToList(consultant) {
            var currentIds = $('#consultant_ids').val() ? $('#consultant_ids').val().split(',') : [];
            currentIds.push(consultant.id);
            $('#consultant_ids').val(currentIds.join());

            $('#consultant_list').append('<li data-id="' + consultant.id + '">' + consultant.value + '<a href="#" class="remove">X</a></li>');
            $('#consultant_list').scrollTop($('#consultant_list')[0].scrollHeight);
            $('#consultant_ids').trigger('change');
        }

        $('#consultant_list').on('click', '.remove', function (e) {
            var li = $(e.target).parents('li');
            var consultantId = li.data('id');
            var ids = $('#consultant_ids').val() ? $('#consultant_ids').val().split(',') : [];
            var newIds = [];
            for (var i in ids) {
                if (String(ids[i]) != consultantId) {
                    newIds.push(ids[i]);
                }
            }
            $('#consultant_ids').val(newIds.join());
            $('#consultant_ids').trigger('change');
            $(li).remove();
        });

        function addFirmToList(firm) {
            var currentIds = $('#firm_ids').val() ? $('#firm_ids').val().split(',') : [];
            currentIds.push(firm.id);
            $('#firm_ids').val(currentIds.join());

            $('#firm_list').append('<li data-id="' + firm.id + '">' + firm.value + '<a href="#" class="remove">X</a></li>');
            $('#firm_list').scrollTop($('#firm_list')[0].scrollHeight);
            $('#firm_ids').trigger('change');
        }

        $('#firm_list').on('click', '.remove', function (e) {
            var li = $(e.target).parents('li');
            var firmId = li.data('id');
            var ids = $('#firm_ids').val() ? $('#firm_ids').val().split(',') : [];
            var newIds = [];
            for (var i in ids) {
                if (String(ids[i]) != firmId) {
                    newIds.push(ids[i]);
                }
            }
            $('#firm_ids').val(newIds.join());
            $('#firm_ids').trigger('change');
            $(li).remove();
        });


        function addCreatedByToList(user) {
            var currentIds = $('#createdby_ids').val() ? $('#createdby_ids').val().split(',') : [];
            currentIds.push(user.id);
            $('#createdby_ids').val(currentIds.join());

            $('#createdby_list').append('<li data-id="' + user.id + '">' + user.value + '<a href="#" class="remove">X</a></li>');
            $('#createdby_list').scrollTop($('#createdby_list')[0].scrollHeight);
            $('#createdby_ids').trigger('change');
            $('#search_button').removeAttr('disabled');

        }

        $('#createdby_list').on('click', '.remove', function (e) {
            var li = $(e.target).parents('li');
            var userId = li.data('id');
            var ids = $('#createdby_ids').val() ? $('#createdby_ids').val().split(',') : [];
            var newIds = [];
            for (var i in ids) {
                if (String(ids[i]) != userId) {
                    newIds.push(ids[i]);
                }
            }
            $('#createdby_ids').val(newIds.join());
            $('#createdby_ids').trigger('change');
            $('#search_button').removeAttr('disabled');
            $(li).remove();
        });


        $('#reset_button').on('click', function (e) {
            e.preventDefault();
//        $('.filter-field').val(''); subspecialty_id
            $('#date_from').val('');
            $('#date_to').val('');
            $('#subspecialty_id').val('');
            $('#site_id').val('');
            $('#consultant_ids').val('');
            $('#show_issued').val('');
            $('#createdby_ids').val('');
            $('#issue_complete').prop("checked", true);
            $('#issue_incomplete').prop("checked", true);
            $('#firm_ids').val('');
            $('#cvi-filter').submit();
        });

        $('#cvi-filter').on('change', '.filter-field', function () {
            $('#search_button').removeAttr('disabled');
        });
         
        $(document).on('click','#export_csv', function () {
            originalAction = $('#cvi-filter').attr('action');
            action = '/OphCoCvi/Default/export',
            $('#cvi-filter').attr('action', action);
            $('#cvi-filter').submit();
            
            $('#cvi-filter').attr('action', originalAction);
        });

        $(document).ready(function () {
            $('.datepicker').datepicker({'showAnim': 'fold', 'dateFormat': 'd M yy'});
        });
    </script>