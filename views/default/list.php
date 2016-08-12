<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$manager = $this->getManager();

$cols = array(
    array(
        'id' => 'event_date',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('event_date', 'Date', array('class' => 'sort-link')),
        'value' => 'Helper::convertMySQL2NHS($data->event->event_date)',
        'htmlOptions' => array('class' => 'date'),
    ),
    array(
        'id' => 'patient_name',
        'class' => 'CLinkColumn',
        'header' => $dp->getSort()->link('patient_name', 'Name', array('class' => 'sort-link')),
        'urlExpression' => 'Yii::app()->createURL("/patient/view/", array("id" => $data->event->episode->patient_id))',
        //'urlExpression' => '$this->grid->controller->getManager()->getEventViewUri($data->event)',
        'labelExpression' => '$data->event->episode->patient->getHSCICName()',
    ),
    array(
        'id' => 'consultant',
        'header' => $dp->getSort()->link('consultant', 'From', array('class' => 'sort-link')),
        'value' => function ($data) use ($manager) {
            if ($consultant = $manager->getClinicalConsultant($data)) {
                return $consultant->getFullNameAndTitle();
            } else {
                return '-';
            }
        }
    ),
    array(
        'id' => 'issue_status',
        'header' => $dp->getSort()->link('issue_status', 'Status', array('class' => 'sort-link')),
        'value' => '$data->getIssueStatusForDisplay()'
    ),
    array(
        'id' => 'issue_date',
        'header' => $dp->getSort()->link('issue_date', 'Issue Date', array('class' => 'sort-link')),
        'value' => function ($data) {
            $date = $data->getIssueDateForDisplay();
            if ($date) {
                return Helper::convertMySQL2NHS($date);
            }
            return '-';
        }
    ),
    array(
        'id' => 'actions',
        'header' => 'Actions',
        'class' => 'CButtonColumn',
        'htmlOptions' => array('class' => 'left'),
        'template' => '<span style="white-space: nowrap;">{view} {edit}</span>',
        'viewButtonImageUrl' => false,
        'buttons' => array(
            'view' => array(
                'options' => array('title' => 'View CVI', 'class' => ''),
                'url' => 'Yii::app()->createURL("/OphCoCvi/Default/view/", array(
                        "id" => $data->event_id))',
                'label' => '<button class="secondary small">view</button>'
            ),
            'edit' => array(
                'options' => array('title' => 'Add a comment'),
                'url' => 'Yii::app()->createURL("/OphCoMessaging/Default/update/", array(
                                        "id" => $data->event_id))',
                'label' => '<button class="secondary small">Edit</button>',
                'visible' => function ($row, $data) {
                    return $data->is_draft;
                },
            ),
        ),
    )
);

?>
<h1 class="badge">CVI List</h1>
<div class="box content">
    <div class="search-filters">
        <?php $this->beginWidget('CActiveForm', array(
            'id' => 'cvi-filter',
            'htmlOptions' => array(
                'class' => 'row',
            ),
            'enableAjaxValidation' => false,
        ))?>

        <div class="large-12 column">
            <div class="panel">
                <h3>Filter CVIs</h3>
                <div class="row">

                    <div class="column large-1"><label for="date_from">From:</label></div>
                    <div class="column large-2"><input type="text" id="date_from" name="date_from" class="datepicker" value="<?=$this->request->getPost('date_from', '')?>" /></div>
                    <div class="column large-1"><label for="date_to">To:</label></div>
                    <div class="column large-2"><input type="text" id="date_to" name="date_to" class="datepicker" value="<?=$this->request->getPost('date_to', '')?>" /></div>
                    <div class="column large-1"><label for="consultants">Consultant(s):</label></div>
                    <div class="column large-2"><?php
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
                                        'url': '".Yii::app()->createUrl('user/autocomplete')."',
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
                        <div><ul id="consultant_list" style="overflow-y: auto; max-height: 40px;">
                                <?php $consultant_ids = $this->request->getPost('consultant_ids', '');
                                if ($consultant_ids) {
                                    foreach(explode(',', $consultant_ids) as $id) {
                                        if ($user = User::model()->findByPk($id)) { ?>
                                            <li data-id="<?=$id?>"><?= $user->getReversedFullname() ?><a href="#" class="remove">X</a></li>
                                        <?php }
                                    }
                                }?>
                            </ul></div>
                        <?= CHtml::hiddenField('consultant_ids', $this->request->getPost('consultant_ids', '')); ?>
                    </div>
                    <div class="column large-2 text-right"><label for="show-issued">Show Issued:</label></div>
                    <div class="column large-1 end"><?php echo CHtml::checkBox('show_issued', ($this->request->getPost('show_issued', '') == 1))?></div>
                </div>
                <div class="row">
                    <div class="column large-12 text-right end"><button id="search_button" class="secondary small" type="submit">Apply</button></div>
                </div>
            </div>
        </div>
        <?php $this->endWidget()?>
    </div>

    <div class="row">
        <div class="large-12 column">
            <div class="box generic">
                <?php $this->widget('zii.widgets.grid.CGridView', array(
                    'itemsCssClass' => 'grid',
                    'dataProvider' => $dp,
                    'htmlOptions' => array('id' => 'inbox-table'),
                    'summaryText' => '<small> {start}-{end} of {count} </small>',
                    'columns' => $cols,
                )); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function addConsultantToList(consultant)
    {
        var currentIds = $('#consultant_ids').val() ? $('#consultant_ids').val().split(',') : [];
        currentIds.push(consultant.id);
        $('#consultant_ids').val(currentIds.join());

        $('#consultant_list').append('<li data-id="'+ consultant.id +'">' + consultant.value +'<a href="#" class="remove">X</a></li>');
        $('#consultant_list').scrollTop($('#consultant_list')[0].scrollHeight);
    }

    $('#consultant_list').on('click', '.remove', function(e) {
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
        $(li).remove();
    });

    $(document).ready(function() {
        $('.datepicker').datepicker({'showAnim':'fold','dateFormat':'d M yy'});
    });
</script>