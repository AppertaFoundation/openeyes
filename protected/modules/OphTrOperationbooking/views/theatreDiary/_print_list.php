<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
?>
<style>
    @media print {
        @page {
            size: landscape;
            width: 100%;
        }
        #d_title {
            text-align: center;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .d_overview {
            width: 100%;
            /* border: 1px solid black; */
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .d_overview th, .d_overview td {
            text-align: left;
            padding: 0 10px;
        }
        .d_overview tr:not(:last-child) {
            border-bottom: 1px solid black;
        }
        .d_overview th:first-child,
        .d_overview td:first-child {
            padding-left: 0;
        }
        .d_overview th:last-child,
        .d_overview td:last-child {
            padding-right: 0;
        }

        .d_data th {
            text-align: left;
        }

        .d_data td, .d_data th {
            border: 1px double black;
            padding: 3px;
        }

        .d_data td:last-child, .d_data th:last-child {
        text-align: right;
        }

        .label {
            font-weight: 600;
        }
    }
</style>
<div id="diaryTemplate">
    <div id="d_title">TCIs in date range <?= \CHtml::encode($_POST['date-start']) ?>
        to <?= \CHtml::encode($_POST['date-end']) ?></div>
    <table class='d_data' width="100%">
        <tr>
            <th><?= PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution_id, $site_id) ?></th>
            <th>Patient name</th>
            <th>D.O.B.</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Op. date</th>
            <th>Ward</th>
            <th>Consultant</th>
            <th>Subspecialty</th>
        </tr>
        <?php foreach ($bookings as $booking) {
            if ($booking->operation->event) { ?>
                <tr>
                    <td><?= PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $booking->operation->event->episode->patient->id, $institution_id, $site_id)) ?></td>
                    <td>
                        <strong><?= strtoupper($booking->operation->event->episode->patient->last_name) ?></strong>, <?= $booking->operation->event->episode->patient->first_name ?>
                    </td>
                    <td><?= $booking->operation->event->episode->patient->NHSDate('dob') ?></td>
                    <td><?= $booking->operation->event->episode->patient->age ?></td>
                    <td><?= $booking->operation->event->episode->patient->gender ?></td>
                    <td><?= $booking->NHSDate('session_date') ?></td>
                    <td><?= $booking->ward ? $booking->ward->name : 'None' ?></td>
                    <td><?= $booking->session->firm->pas_code ?></td>
                    <td><?= $booking->session->firm->serviceSubspecialtyAssignment->subspecialty->name ?></td>
                </tr>
            <?php }
        } ?>
    </table>
</div>
