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
<div class="booking-admission-form">
<?php
$logo_helper = new LogoHelper();
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $patient->id, $institution_id, $site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $patient->id, $institution_id, $site_id);
?>
    <div class="banner clearfix">
        <?= $logo_helper->render() ?>
    </div>
    <h1>Admission Form</h1>

    <table>
        <tr>
            <th>
                <?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?>
            </th>
            <td>
                <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?>
            </td>
            <th>
                Patient Name
            </th>
            <td>
                <?= $patient->fullname?>
            </td>
        </tr>

        <tr>
            <th>
                <?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?>
            </th>
            <td>
                <?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?>
            </td>
            <th rowspan="2">
                Address
            </th>
            <td rowspan="2">
                <?= $patient->getLetterAddress(array('delimiter' => '<br/>'))?>
            </td>
        </tr>

        <tr>
            <th>DOB</th>
            <td><?= $patient->NHSDate('dob')?></td>
        </tr>
        <?php if (isset($contact_details) && $contact_details) : ?>
        <tr>
            <th>
                Person collecting:
            </th>
            <td>
                <?= $contact_details->collector_name; ?> - <?= $contact_details->collector_contact_number; ?>
            </td>
            <th>
                Patient telephone number:
            </th>
            <td>
                <?= $contact_details->patient_booking_contact_number; ?>
            </td>
        </tr>
        <?php endif; ?>

    </table>

    <h2>Admission Information</h2>
    <table class="borders">
        <tr>
            <th>
                <?php if ($operation->booking) {?>
                    Admitting Consultant:
                <?php } else {?>
                    Consultant:
                <?php }?>
            </th>
            <td>
                <?php if ($emergencyList) {?>
                    Emergency List (<?=\CHtml::encode($firm->consultantName)?>)
                <?php } else {?>
                    <?=\CHtml::encode($firm->consultantName)?>
                <?php }?>
            </td>
            <th>
                Decision to admit (or today's) date:
            </th>
            <td>
                <?= $operation->NHSDate('decision_date')?>
            </td>
        </tr>

        <tr>
            <th>Service:</th>
            <td><?=\CHtml::encode($firm->serviceSubspecialtyAssignment->service->name) ?></td>
            <th>Patient Telephone:</th>
            <td><?=\CHtml::encode($patient->primary_phone) ?></td>
        </tr>

        <tr>
            <th>Site:</th>
            <td><?=\CHtml::encode($site->name)?></td>
            <?php if ($operation->booking) {?>
                <th>Person organising operation:</th>
                <td><?= $operation->booking->user->getFullName()?></td>
            <?php } else {?>
                <th>Person organising admission:</th>
                <td><?= $operation->event->user->getFullName()?></td>
            <?php }?>
        </tr>
    </table>

    <h2>Admission Details</h2>

    <table class="borders">

        <tr>
            <th>Priority:</th>
            <td><?= $operation->priority->name?></td>
            <th>Admission category:</th>
            <td><?= ($operation->overnight_stay) ? 'an overnight stay' : 'day case'?></td>
        </tr>

        <tr>
            <th>Consultant to be present:</th>
            <td>
                <?php
                echo (empty($operation->consultant_required)) ? 'No' : 'Yes';
                if ($operation->consultant_required && $operation->consultant) {
                    echo ', ' . $operation->consultant->ReversedFullName;
                }
                ?>
            </td>
            <th>Total theatre time (mins):</th>
            <td><?=\CHtml::encode($operation->total_duration)?></td>
        </tr>

        <tr>
            <th>Any other doctor to do:</th>
            <td><?= (empty($operation->any_grade_of_doctor)) ? 'No' : 'Yes'?></td>
            <th></th>
            <td></td>
        </tr>

        <tr>
            <th>Intended procedure(s):</th>
            <td><?=\CHtml::encode($operation->proceduresCommaSeparated)?></td>
            <?php if ($operation->booking) {?>
                <th>Operation date:</th>
                <td><?= $operation->booking->session->NHSDate('date')?></td>
            <?php } else {?>
                <th colspan="2" rowspan="4">Patient Added to partial bookings waiting List, admission Date to be arranged</th>
            <?php }?>
        </tr>

        <tr>
            <th>Eye:</th>
            <td><?= $operation->eye->name?></td>
            <?php if ($operation->booking) {?>
                <th>Theatre session:</th>
                <td><?= substr($operation->booking->session->start_time, 0, 5) . ' - ' . substr($operation->booking->session->end_time, 0, 5)?></td>
            </tr>
            <tr>
                <th>Theatre:</th>
                <td><?= $operation->booking->session->TheatreName?></td>
                <th>Ward:</th>
                <td><?= $operation->booking->ward ? $operation->booking->ward->name : 'None'?></td>
            <?php }?>
        </tr>

        <tr>
            <th>Diagnosis:</th>
            <td>
                <?= $operation->diagnosis->eye->adjective . ' ' . CHtml::encode($operation->diagnosis->disorder->term)?>
            </td>
            <?php if ($operation->booking) {?>
                <th>Admission time:</th>
                <td><?= date('H:i', strtotime($operation->booking->admission_time))?></td>
            <?php }?>
        </tr>

        <tr>
            <th>Anaesthesia:</th>
            <td><?= $operation->getAnaestheticTypeDisplay() ?></td>
            <?php if ($operation->booking) {?>
                <th>Proposed admission date:</th>
                <td><?= $operation->booking->session->NHSDate('date')?></td>
            <?php }?>
        </tr>
    </table>

    <h2>Comments</h2>
    <table class="borders">
        <tr>
            <td height="50"><?= nl2br(CHtml::encode($operation->comments))?></td>
        </tr>
    </table>

    <h2>Pre-op Assessment Date</h2>

    <table class="borders">
        <tr>
            <td height="50"></td>
        </tr>
    </table>
</div>
