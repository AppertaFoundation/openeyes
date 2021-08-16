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
$logo_helper = new LogoHelper();
$institution_id = Institution::model()->getCurrent()->id;
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $patient->id, $institution_id, Yii::app()->session['selected_site_id']);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_secondary_number_usage_code'], $patient->id, $institution_id, Yii::app()->session['selected_site_id']);
?>
<div class="banner clearfix">
        <?= $logo_helper->render('letter_head') ?>

</div>
<style>
    td, p {
        font-size: 8pt;
    }

    h1 {
        text-align: center;
    }

    table td {
        border: 1px solid #dae6f1;
    }

    td.signature {
        background-color: #dae6f1;
    }

</style>

<?php
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
//$ccg = $patient->getCommissioningBodyOfType($cbody_type);
$gp_cb = $patient->gp ? $patient->practice->getCommissioningBodyOfType($cbody_type) : null;
?>

<p>Notification for Dexamethasone intravitreal implant for the treatment of macular oedema secondary to retinal vein
    occlusion.</p>

<table cellpadding="5">
    <tr>
        <?php if ($secondary_identifier) { ?>
            <td><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?> Number:</td>
            <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
        <?php } ?>
        <td>Trust</td>
        <td>Moorfields NHS Foundation Trust</td>
        <td><?= \SettingMetadata::model()->getSetting('gp_label') ?> Name:</td>
        <td><?= ($patient->gp) ? $patient->gp->contact->fullName : 'Unknown'; ?></td>
    </tr>

    <tr>
        <td><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?>:</td>
        <td><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></td>
        <td>Consultant Making Request:</td>
<td><?php echo $service_info->consultant->getConsultantName() ?></td>
<td><?php echo \SettingMetadata::model()->getSetting('gp_label')?> Practice Code:</td>
<td><?php echo $patient->gp ? $patient->gp->nat_id : 'Unknown' ?></td>
    </tr>

    <tr>
        <td>Patient Name</td>
        <td><?= $patient->getFullName() ?></td>
        <td>Proposed MEH Site:</td>
        <td>TBD</td>
<td><?php echo \SettingMetadata::model()->getSetting('gp_label')?> Post Code:</td>
<td><?php echo ($patient->practice && $patient->practice->contact->correspondAddress) ? $patient->practice->contact->correspondAddress->postcode : 'Unknown' ?></td>
        <td>Patient consents to share data:</td>
        <td>
            <div class="data-value <?= is_null($service_info) ? 'not-recorded' : '' ?>">
                <?= is_null($service_info) ? 'Not recorded' : ($service_info->patient_sharedata_consent ? 'Yes' : 'No') ?>
            </div>
        </td>
    </tr>

    <tr>
        <td>Patient VA</td>
        <td colspan="5"><?= ($exam_api && ($va = $exam_api->getLetterVisualAcuityBoth($patient))) ? Yii::app()->format->Ntext($va) : 'Not measured'; ?></td>
    </tr>

    <tr>
        <td><?= \SettingMetadata::model()->getSetting('gp_label') ?> CCG</td>
        <td colspan="5"><?= $gp_cb ? $gp_cb->code . ',' . $gp_cb->name . "\n" : "Unknown\n" ?></td>
    </tr>

    <tr>
        <td colspan="6"><b>Please indicate which aspect of NICE TA229 applies for the patient:</b></td>
    </tr>
    <?php foreach ($suitability->getDecisionTreeAnswersForDisplay($side) as $question => $answer) { ?>
        <tr>
            <td colspan="3"><?= $question ?></td>
            <td colspan="3"><?= $answer ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="3">Which eye is the treatment for?</td>
        <td colspan="3"><?= ucfirst($side) ?></td>
    </tr>

    <tr>
        <td colspan="3">What is the acquisition cost of the drug including VAT (if applicable)</td>
        <td colspan="3">&pound;1,044 per vial<br/><br/>Inpatient (Day Case)<br/>PbR Tariff</td>
    </tr>


    <tr>
        <td colspan="6" class="signature">Signature by Trust Chief Pharmacist:<br/><br/><br/></td>
    </tr>
</table>
