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
        'value' => function($data) use ($manager) {
            if ($consultant = $manager->getClinicalConsultant($data)) {
                return $consultant->getFullNameAndTitle();
            }
            else {
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