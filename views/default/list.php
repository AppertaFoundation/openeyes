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
            if ($consultant = $manager->getEventConsultant($data->event)) {
                return $consultant->getFullNameAndTitle();
            }
            else {
                return '-';
            }
        }
    ),
    array(
        'id' => 'status',
        'header' => 'Status',
        'value' => function($data) use ($manager) {
            return $manager->getDisplayStatusForEvent($data->event);
        }
    ),
    array(
        'id' => 'issue-date',
        'header' => 'Issue Date',
        'value' => function ($data) use ($manager) {
            $date = $manager->getDisplayIssueDateForEvent($data->event);
            if ($date) {
                return Helper::convertMySQL2NHS($date);
            }
            return '-';
        }
    )
);

$this->widget('zii.widgets.grid.CGridView', array(
    'itemsCssClass' => 'grid',
    'dataProvider' => $dp,
    'htmlOptions' => array('id' => 'inbox-table'),
    'summaryText' => '<h3>CVI Events <small> {start}-{end} of {count} </small></h3>',
    'columns' => $cols,
));
