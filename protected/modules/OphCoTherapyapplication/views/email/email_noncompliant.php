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
* @copyright Copyright (c) 2011-2012, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/

$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $patient->id, Institution::model()->getCurrent()->id, Yii::app()->session['selected_site_id']);
?>

This email was generated from an OpenEyes Therapy Application event

<?php
if ($site = $service_info->site) {
    echo 'Intended Site: ' . $site->name;
}
?>

AMD EC-Form this patient sent to Contracts for PCT approval.
AMD EC-Form document sent by: <?= $diagnosis->user->getReportDisplay() . "\n" ?>

The Eye to inject is: <?= $side . "\n" ?>
Drug to use is: <?= $treatment->drug->name . "\n" ?>
Diagnosis: <?= $diagnosis->getDiagnosisStringForSide($side) . "\n" ?>
<?php
if (
    $exam_info = $exam_api->getInjectionManagementComplexInEpisodeForDisorder(
        $patient,
        $use_context = true,
        $side,
        $diagnosis->{$side . '_diagnosis1_id'},
        $diagnosis->{$side . '_diagnosis2_id'}
    )
) {
    foreach ($exam_info->{$side . '_answers'} as $answer) {
        echo $answer->question->question . ': ';
        echo ($answer->answer) ? "Yes\n" : "No\n";
    }
    echo 'Comments: ' . $exam_info->{$side . '_comments'} . "\n";
}
?>

NICE Status: <?=($suitability->{$side . '_nice_compliance'} ? 'COMPLIANT' : 'NON-COMPLIANT') . "\n" ?>
Urgent: <?=((isset($exceptional) && $exceptional->{$side . '_start_period'}->urgent) ? 'Yes' : 'No') . "\n" ?>
<?php if ((isset($exceptional) && $exceptional->{$side . '_start_period'}->urgent)) {?>
Reason for urgency: <?= $exceptional->{$side . '_urgency_reason'} . "\n"?>
<?php }?>
Patient consents to share data: <?=(is_null($service_info->patient_sharedata_consent) ? 'Not recorded' : ($service_info->patient_sharedata_consent ? 'Yes' : 'No')) . "\n"?>

Patient Details:
Full Name: <?= $patient->fullname . "\n" ?>
<?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?>: <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) . "\n" ?>
DoB: <?= $patient->NHSDate('dob') . "\n" ?>
Gender: <?= $patient->getGenderString() . "\n" ?>
Address: <?= ($address = $patient->getLetterAddress(array('delimiter' => ', '))) ? $address . "\n" : "Unknown\n"; ?>

<?= \SettingMetadata::model()->getSetting('gp_label') ?> Details:
Name: <?= ($patient->gp) ? $patient->gp->fullName . "\n" : "Unknown\n"; ?>
Address: <?= ($patient->practice && $address = $patient->practice->getLetterAddress(array('delimiter' => ', '))) ? $address . "\n" : "Unknown\n"; ?>

<?php
if ($link_to_attachments) {
    ?>
The application files can be found on openeyes. Please enter the following text into the search box to reach download links:
    E:<?= $suitability->event_id ?>
<?php }
