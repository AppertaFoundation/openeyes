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
$ccg = CommissioningBodyType::model()->find('shortname=?', array('CCG'));
$cb = $patient->getCommissioningBodyOfType($ccg);
$gp_cb = ($patient->gp && $patient->practice) ? $patient->practice->getCommissioningBodyOfType($ccg) : null;
$institution_id = Institution::model()->getCurrent()->id;
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $patient->id, $institution_id, Yii::app()->session['selected_site_id']);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $patient->id, $institution_id, Yii::app()->session['selected_site_id']);
?>

This email was generated from the OpenEyes Therapy Application event

<?php
if ($site = $service_info->site) {
    echo 'Intended Site: ' . $site->name;
}
?>

Request for AMD Injection booking sent by: <?= $diagnosis->user->getReportDisplay() . "\n" ?>
The Eye to inject is: <?= $side . "\n" ?>
Drug to use is: <?= $treatment->drug->name . "\n" ?>
VA: Right eye: <?= $exam_api->getLetterVisualAcuityRight($patient) ?>, left eye: <?= $exam_api->getLetterVisualAcuityLeft($patient) . "\n" ?>
<?php foreach ($suitability->getDecisionTreeAnswersForDisplay($side) as $question => $answer) { ?>
    <?= "$question: $answer\n" ?>
<?php } ?>
NICE Status: <?= ($suitability->{$side . '_nice_compliance'} ? 'COMPLIANT' : 'NON-COMPLIANT') . "\n" ?>
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
Patient consents to share data: <?= (is_null($service_info->patient_sharedata_consent) ? 'Not recorded' : ($service_info->patient_sharedata_consent ? 'Yes' : 'No')) . "\n" ?>

Patient Details:
Full Name: <?= $patient->getFullName() . "\n" ?>
<?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?>: <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) . "\n" ?>
<?php if ($secondary_identifier) { ?>
    <?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?>: <?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) . "\n" ?>
<?php } ?>
DoB: <?= $patient->NHSDate('dob') . "\n" ?>
Gender: <?= $patient->getGenderString() . "\n" ?>
Address: <?= ($address = $patient->getLetterAddress(array('delimiter' => ', '))) ? $address . "\n" : "Unknown\n"; ?>
CCG Code: <?= $cb ? $cb->code . "\n" : "Unknown\n" ?>
CCG Description: <?= $cb ? $cb->name . "\n" : "Unknown\n" ?>
CCG Address: <?= $cb && $address = $cb->getLetterAddress(array('delimiter' => ', ')) ? $address . "\n" : "Unknown\n"; ?>

GP Details:
Name: <?= ($patient->gp) ? $patient->gp->contact->fullName . "\n" : "Unknown\n"; ?>
Address: <?= ($patient->practice && $address = $patient->practice->getLetterAddress(array('delimiter' => ','))) ? $address . "\n" : "Unknown\n"; ?>
CCG Code: <?= $gp_cb ? $gp_cb->code . "\n" : "Unknown\n" ?>
CCG Description: <?= $gp_cb ? $gp_cb->name . "\n" : "Unknown\n" ?>
CCG Address: <?= $gp_cb && $address = $gp_cb->getLetterAddress(array('delimiter' => ', ')) ? $address . "\n" : "Unknown\n" ?>
