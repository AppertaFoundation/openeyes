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

$manager = $this->getManager();

$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];

$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(
        Yii::app()->params['display_primary_number_usage_code'],
        $institution->id ,
        $selected_site_id);

$cols = array(
    array(
        'id' => 'event_date',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('event_date', 'Event Date', array('class' => 'sort-link')),
        'value' => 'Helper::convertMySQL2NHS($data->event->event_date)',
        'htmlOptions' => array('class' => 'date', 'style' => 'whitespace: nowrap;'),
    ),
    array(
        'id' => 'subspecialty',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('subspecialty', 'Subspecialty', array('class' => 'sort-link')),
        'value' => '$data->event->episode->getSubspecialtyText()'
    ),
    array(
        'id' => 'site',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('site', 'Site', array('class' => 'sort-link')),
        'value' => '$data->site ? $data->site->name : "-"',
    ),
    array(
        'id' => 'patient_name',
        'class' => 'CLinkColumn',
        'header' => $dp->getSort()->link('patient_name', 'Name', array('class' => 'sort-link')),
        'urlExpression' => 'Yii::app()->createURL("/patient/view/", array("id" => $data->event->episode->patient_id))',
        'labelExpression' => '$data->event->episode->patient->getDisplayName() . " (" . $data->event->episode->patient->getAge() . "y)"',
// this would be consistent with HSCIC guidance (minus the NHS Number)
//        'labelExpression' => '$data->event->episode->patient->getHSCICName() . "<br /><span style=\"font-size: 0.8em\"><i>Born</i> " . ' .
//            'Helper::convertMySQL2NHS($data->event->episode->patient->dob) . " (" . $data->event->episode->patient->getAge() . "y)</span>"',

    ),
    array(
        'id' => 'hosnum',
        'class' => 'CDataColumn',
        'header' => $dp->getSort()->link('hosnum', $primary_identifier_prompt, array('class' => 'sort-link')),
        'value' => function ($data) {
            $institution = Institution::model()->getCurrent();
            $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $data->event->episode->patient->id, $institution->id, $this->selectedSiteId);
            $patient_identifier_widget = $this->widget('application.widgets.PatientIdentifiers', ['patient' => $data->event->episode->patient, 'show_all' => true, 'tooltip_size' => 'small'], true);
            return PatientIdentifierHelper::getIdentifierValue($primary_identifier) . $patient_identifier_widget;
        },
        'type' => 'raw'
    ),
    array(
        'id' => 'creator',
        'header' => $dp->getSort()->link('creator', 'Created By', array('class' => 'sort-link')),
        'value' => '$data->user->getFullNameAndTitle()'
    ),
    array(
        'id' => 'consultant',
        'header' => $dp->getSort()->link('consultant', 'Consultant', array('class' => 'sort-link')),
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
        'value' => function ($data) {
            if ($data->event->info) {
                return $data->event->info;
            } else {
                // TODO: possibly don't need this, or this method should handle the above conditional
                return $data->getIssueStatusForDisplay();
            }
        }
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
                'label' => '<button class="secondary small">View</button>'
            ),
            'edit' => array(
                'options' => array('title' => 'Add a comment'),
                'url' => 'Yii::app()->createURL("/OphCoCvi/Default/update/", array(
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
<div class="oe-full-header flex-layout">
    <div class="title wordcaps">CVI List</div>
</div>
<div class="oe-full-content subgrid oe-audit">
    <nav class="oe-full-side-panel audit-filters">
        <?php $this->renderPartial('list_filter', array('list_filter' => $list_filter)) ?>
    </nav>
    <main id="searchResults" class="oe-full-main audit-main">
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'itemsCssClass' => 'standard',
            'dataProvider' => $dp,
            'htmlOptions' => array('id' => 'inbox-table'),
            'summaryText' => '<small> {start}-{end} of {count} </small>',
            'columns' => $cols,
        )); ?>
    </main>
</div>
