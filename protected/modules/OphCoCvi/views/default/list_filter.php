<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="search-filters">
    <?php $this->beginWidget('CActiveForm', array(
        'id' => 'cvi-filter',
        'htmlOptions' => array(
            'class' => 'data-group',
        ),
        'enableAjaxValidation' => false,
    )) ?>
    <div class="cols-12 column">
        <div class="panel box js-toggle-container">
            <h3>Filter CVIs</h3>
            <a href="#" class="toggle-trigger toggle-hide js-toggle">
                    <span class="icon-showhide">
                        Show/hide this section
                    </span>
            </a>
            <div class="row">
                <table class="standard last-right">
                    <colgroup>
                        <col class="cols-3">
                    </colgroup>
                    <tr>
                        <td>
                            Site
                        </td>
                        <td>
                            <?= \CHtml::dropDownList('site_id',
                                (array_key_exists('site_id', $list_filter) ? $list_filter['site_id'] : null),
                                Site::model()->getListForCurrentInstitution(),
                                array('class' => 'filter-field cols-full', 'empty' => 'All sites',)) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Subspecialty
                        </td>
                        <td>
                            <?= \CHtml::dropDownList('subspecialty_id',
                                (array_key_exists('subspecialty_id', $list_filter) ? $list_filter['subspecialty_id'] : null),
                                Subspecialty::model()->getList(),
                                array('class' => 'filter-field cols-full', 'empty' => 'All specialties',)) ?>
                        </td>
                    </tr>
                </table>
                <div class="data-group js-toggle-body">
                    <h3>Filter by Date</h3>
                    <div class="flex-layout">
                        <fieldset>
                            <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                'name' => 'date_from',
                                'id' => 'date_from',
                                'options' => array(
                                    'showAnim' => 'fold',
                                    'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                ),
                                'value' => array_key_exists('date_from', $list_filter) ? $list_filter['date_from'] : '',
                                'htmlOptions' => array(
                                    'class' => 'cols-5',
                                    'placeholder' => 'from'
                                ),
                            )) ?>
                            <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                'name' => 'date_to',
                                'id' => 'date_to',
                                'options' => array(
                                    'showAnim' => 'fold',
                                    'dateFormat' => Helper::NHS_DATE_FORMAT_JS,
                                ),
                                'value' => array_key_exists('date_to', $list_filter) ? $list_filter['date_to'] : '',
                                'htmlOptions' => array(
                                    'class' => 'cols-5',
                                    'placeholder' => 'to'
                                ),
                            )) ?>
                        </fieldset>
                    </div>

                    <h3>Created By:</h3>
                     <?php $this->widget('application.widgets.AutoCompleteSearch',['field_name' => 'createdby_auto_complete']); ?>
                    <div>
                        <ul id="createdby_list" class="multi-select-search scroll">
                            <?php $createdby_ids = array_key_exists('createdby_ids', $list_filter) ? $list_filter['createdby_ids'] : '';
                            if ($createdby_ids) {
                                foreach (explode(',', $createdby_ids) as $id) {
                                    if ($user = User::model()->findByPk($id)) { ?>
                                        <li data-id="<?= $id ?>"><?= $user->getReversedFullname() ?><a href="#"
                                                                                                       class="remove">X</a>
                                        </li>
                                    <?php }
                                }
                            } ?>
                        </ul>
                    </div>
                    <?= CHtml::hiddenField('createdby_ids', $this->request->getPost('createdby_ids', ''), array('class' => 'filter-field')); ?>

                    <h3>Consultant(s):</h3>
                    <?php $this->widget('application.widgets.AutoCompleteSearch',['field_name' => 'consultant_auto_complete']); ?>
                    <div>
                        <ul id="consultant_list" class="multi-select-search scroll">
                            <?php $consultant_ids = array_key_exists('consultant_ids', $list_filter) ? $list_filter['consultant_ids'] : '';
                            if ($consultant_ids) {
                                foreach (explode(',', $consultant_ids) as $id) {
                                    if ($user = User::model()->findByPk($id)) { ?>
                                        <li data-id="<?= $id ?>"><?= $user->getReversedFullname() ?><a href="#"
                                                                                                       class="remove">X</a>
                                        </li>
                                    <?php }
                                }
                            } ?>
                        </ul>
                    </div>
                    <?= CHtml::hiddenField('consultant_ids', $this->request->getPost('consultant_ids', ''), array('class' => 'filter-field')); ?>


                    <h3>Show Issued:</h3>
                    <div class="column cols-6">
                        <?php
                        $show_issued = array_key_exists('show_issued', $list_filter) ? $list_filter['show_issued'] : false;
                        echo CHtml::checkBox('show_issued', ($show_issued == 1), array('class' => 'filter-field'));
                        ?>                            </div>
                    <h3>Complete:</h3>
                    <div class="column cols-6">
                        <?php
                        $issue_complete = array_key_exists('issue_complete', $list_filter) ? $list_filter['issue_complete'] : true;
                        echo CHtml::checkBox('issue_complete', ($issue_complete == 1), array('class' => 'filter-field'));
                        ?>
                    </div>
                    <h3>Incomplete:</h3>
                    <div class="column cols-6">
                        <?php
                        $issue_incomplete = array_key_exists('issue_incomplete', $list_filter) ? $list_filter['issue_incomplete'] : true;
                        echo CHtml::checkBox('issue_incomplete', ($issue_incomplete == 1), array('class' => 'filter-field'));
                        ?>
                    </div>

                    <h3></h3>
                    <div>
                        <?php if ($this->isListFiltered()) { ?>
                            <button id="reset_button" class="warning cols-full" type="submit">Reset</button>
                        <?php } ?>
                    </div>
                    <h3></h3>
                    <div>
                        <button id="search_button" type="submit" class="green hint cols-full">Search</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget() ?>

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
        $('#cvi-filter').submit();
    });

    $('#cvi-filter').on('change', '.filter-field', function () {
        $('#search_button').removeAttr('disabled');
    });

    $(document).ready(function () {
        $('.datepicker').datepicker({'showAnim': 'fold', 'dateFormat': 'd M yy'});
    });
</script>