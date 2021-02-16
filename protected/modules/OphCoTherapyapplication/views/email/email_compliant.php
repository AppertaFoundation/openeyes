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
?>

This email was generated from the OpenEyes Therapy Application event

<?php
if ($site = $service_info->site) {
    echo 'Intended Site: '.$site->name;
}
?>

Request for AMD Injection booking sent by: <?php echo $diagnosis->user->getReportDisplay()."\n" ?>
The Eye to inject is: <?php echo $side."\n" ?>
Drug to use is: <?php echo $treatment->drug->name."\n" ?>
VA: Right eye: <?php echo $exam_api->getLetterVisualAcuityRight($patient)?>, left eye: <?php echo $exam_api->getLetterVisualAcuityLeft($patient)."\n" ?>
<?php foreach ($suitability->getDecisionTreeAnswersForDisplay($side) as $question => $answer) {?>
    <?php echo "$question: $answer\n" ?>
<?php }?>
NICE Status: <?php echo($suitability->{$side.'_nice_compliance'} ? 'COMPLIANT' : 'NON-COMPLIANT')."\n" ?>
Diagnosis: <?php echo $diagnosis->getDiagnosisStringForSide($side)."\n" ?>
<?php
if ($exam_info = $exam_api->getInjectionManagementComplexInEpisodeForDisorder(
    $patient,
    $use_context = true,
    $side,
    $diagnosis->{$side.'_diagnosis1_id'},
    $diagnosis->{$side.'_diagnosis2_id'}
)) {
    foreach ($exam_info->{$side.'_answers'} as $answer) {
        echo $answer->question->question.': ';
        echo ($answer->answer) ? "Yes\n" : "No\n";
    }
    echo 'Comments: '.$exam_info->{$side.'_comments'}."\n";
}
?>
Patient consents to share data: <?php echo(is_null($service_info->patient_sharedata_consent) ? 'Not recorded' : ($service_info->patient_sharedata_consent ? 'Yes' : 'No'))."\n"?>

Patient Details:
Full Name: <?php echo $patient->getFullName()."\n" ?>
Number:<?php echo $patient->hos_num."\n" ?>
<?php echo \SettingMetadata::model()->getSetting('nhs_num_label')?> Number: <?php echo $patient->nhs_num."\n" ?>
DoB: <?php echo $patient->NHSDate('dob')."\n" ?>
Gender: <?php echo $patient->getGenderString()."\n" ?>
Address: <?php echo ($address = $patient->getLetterAddress(array('delimiter' => ', '))) ? $address."\n" : "Unknown\n"; ?>
CCG Code: <?php echo $cb ? $cb->code."\n" : "Unknown\n" ?>
CCG Description: <?php echo $cb ? $cb->name."\n" : "Unknown\n" ?>
CCG Address: <?php echo $cb && $address = $cb->getLetterAddress(array('delimiter' => ', ')) ? $address."\n" : "Unknown\n"; ?>

GP Details:
Name: <?php echo ($patient->gp) ? $patient->gp->contact->fullName."\n" : "Unknown\n"; ?>
Address: <?php echo ($patient->practice && $address = $patient->practice->getLetterAddress(array('delimiter' => ','))) ? $address."\n" : "Unknown\n"; ?>
CCG Code: <?php echo $gp_cb ? $gp_cb->code."\n" : "Unknown\n" ?>
CCG Description: <?php echo $gp_cb ? $gp_cb->name."\n" : "Unknown\n" ?>
CCG Address: <?php echo $gp_cb && $address = $gp_cb->getLetterAddress(array('delimiter' => ', ')) ? $address."\n" : "Unknown\n" ?>
