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
/**
 * @var Element_OphDrPrescription_Details $element
 */

$copy = $data['copy'];

$header_text = null;
$footer_text = null;

$print_mode = \SettingMetadata::model()->getSetting('prescription_form_format');

$allowed_tags = '<b><br><div><em><h1><h2><h3><h4><h5><h6><hr><i><ul><ol><li><p><small><span><strong><sub><sup>
<u><wbr><table><thead><tbody><tfoot><tr><th><td><colgroup>';

$header_param = \SettingMetadata::model()->getSetting('prescription_boilerplate_header');
if ($header_param !== null) {
    $header_text = strip_tags($header_param, $allowed_tags);
}

$footer_param = \SettingMetadata::model()->getSetting('prescription_boilerplate_footer');
if ($footer_param !== null) {
    $footer_text = strip_tags($footer_param, $allowed_tags);
}
?>

<?php
    $firm = $element->event->episode->firm;
    $cost_code = $firm->cost_code ? " ($firm->cost_code)" : '';
    $consultantName = $firm->consultant ? ($firm->consultant->getFullName() . $cost_code) : 'None';
    $subspecialty = $firm->serviceSubspecialtyAssignment->subspecialty;

    $prescribed_by = $element->event->usermodified;
    $prescribed_date = $element->event->NHSDate('last_modified_date');

if (isset($element->authorisedByUser)) {
    $prescribed_by = $element->authorisedByUser;
    $prescribed_date = $element->NHSDate('authorised_date');
}
?>

<?php if (!isset($data['print_mode']) || ($data['print_mode'] !== 'WP10' && $data['print_mode'] !== 'FP10')) {
    $institution_id = Institution::model()->getCurrent()->id;
    $site_id = Yii::app()->session['selected_site_id'];
    $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
    $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $this->patient->id, $institution_id, $site_id);

    if ($header_text !== null) { ?>
        <div class="clearfix"><?= $header_text ?></div>
    <?php } ?>

    <table class="borders prescription_header" style="margin-bottom:0px">
        <tr>
            <th>Patient Name</th>
            <td><?= $this->patient->fullname ?> (<?= $this->patient->gender ?>)</td>
            <th><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?></th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?= $this->patient->NHSDate('dob') ?> (<?= $this->patient->age ?>)</td>
            <th><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?></th>
            <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
        </tr>
        <tr>
            <th>Consultant</th>
            <td><?= $consultantName ?></td>
            <th>Service</th>
            <td><?= $subspecialty->name ?></td>
        </tr>
        <tr>
            <th>Patient's address</th>
            <td colspan="3"><?= $this->patient->getSummaryAddress(', ') ?></td>
        </tr>
    </table>
    <table class="borders prescription_header" style="margin-top:0px">
        <tr style="table-layout: fixed;">
            <th>Payment status</th>
            <td> PAID &#9744 </td>
            <td> EXEMPT &#9744 </td>
        </tr>
    </table>

    <div class="spacer"></div>

    <h2>Allergies</h2>
    <table class="borders">
        <tr>
            <td><?= $this->patient->getAllergiesString() ?></td>
        </tr>
    </table>

    <div class="spacer"></div>

    <?php
    $items_data = $this->groupItems($element->items);
    foreach ($items_data as $group => $items) { ?>
        <b>
            <?php
            $group_name = OphDrPrescription_DispenseCondition::model()->findByPk($group)->name;
            echo str_replace('{form_type}', $print_mode, $group_name); ?>
        </b>
        <table class="borders prescription_items">
            <thead>
            <tr>
                <th class="prescriptionLabel">Prescription details</th>
                <th>Dose</th>
                <th>Route</th>
                <th>Freq.</th>
                <th>Duration</th>
                <?php if (strpos($group_name, 'Hospital') !== false) { ?>
                    <th>Dispense Location</th>
                    <th>Quantity Dispensed</th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($items as $item) {
                ?>
                <tr class="prescriptionItem<?= $this->patient->hasDrugAllergy($item->medication_id) ? ' allergyWarning' : ''; ?> ">
                <td class="prescriptionLabel"><?=$item->medication->getLabel(true); ?></td>
                <td><?=is_numeric($item->dose) ? ($item->dose . " " . $item->dose_unit_term) : $item->dose ?></td>
                <td><?=$item->route->term ?><?php if ($item->laterality) {
                        echo ' (' . $item->medicationLaterality->name . ')';
                    } ?></td>
                <td><?=$item->frequency->term; ?></td>
                <td><?=$item->medicationDuration->name ?></td>
                        <?php if (strpos($group_name, 'Hospital') !== false) { ?>
                            <td><?= $item->dispense_location->name ?></td>
                            <td></td>
                        <?php } ?>
                    </tr>
                    <?php foreach ($item->tapers as $taper) { ?>
                        <tr class="prescriptionTaper">
                            <td class="prescriptionLabel">then</td>
                    <td><?=is_numeric($taper->dose) ? ($taper->dose . " " . $item->dose_unit_term) : $taper->dose ?></td>
                            <td>-</td>
                            <td><?= $taper->frequency->term ?></td>
                            <td><?= $taper->duration->name ?></td>
                            <?php if (strpos($group_name, 'Hospital') !== false) { ?>
                                <td></td>
                                <td>-</td>
                            <?php } ?>
                        </tr>
                        <?php
                    }

                    if (strlen($item->comments) > 0) { ?>
                        <tr class="prescriptionComments">
                            <td class="prescriptionLabel">Comments:</td>
                            <td colspan="<?= strpos($group_name, 'Hospital') !== false ? 7 : 4 ?>">
                                <i><?= CHtml::encode($item->comments) ?></i></td>
                </tr>
                    <?php }
            } ?>
        </tbody>
    </table>
    <?php } ?>
    <div class="spacer"></div>

    <h2>Comments</h2>
    <table class="borders">
        <tr>
            <td><?= $element->comments ? $element->textWithLineBreaks('comments') : '&nbsp;' ?></td>
        </tr>
    </table>

    <div class="spacer"></div>
    <?php
    $site_theatre = $this->getSiteAndTheatreForSameDayEvent($prescribed_date, $element->event->episode->firm_id);
    if (!$data['copy'] && $site_theatre) { ?>
        <table class="borders done_bys">
            <tr>
                <th>Site</th>
                <td><?= $site_theatre->site->name ?></td>
                <?php if ($site_theatre->theatre) { ?>
                    <th>Theatre</th>
                    <td><?= $site_theatre->theatre->name ?></td>
                <?php } ?>
            </tr>
        </table>
        <div class="spacer"></div>
    <?php } ?>
    <?php
    if ($signatures = $element->isSignedByMedication()) {
        $readonly_signatures = $signatures->getSignatures(true);
        foreach ($readonly_signatures as $signature) {
            ?>
    <table class="borders done_bys">
        <tr>
            <th>Prescribed by</th>
            <td><?=$prescribed_by->fullname ?><?php if ($prescribed_by->registration_code) {
                echo ' (' . $prescribed_by->registration_code . ')';
                } ?>
            </td>
            <th>Date</th>
            <td><?= $prescribed_date ?>
            </td>
        </tr>
        <tr class="handWritten">
            <th>Signature</th>
            <td>
                <?= $signature->getPrintout() ?>
            </td>
            <th>Contact Number</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
    </table>
            <?php
        }
    } else {
        ?>
    <table class="borders done_bys">
        <tr>
            <th>Prescribed by</th>
            <td><?=$prescribed_by->fullname ?><?php if ($prescribed_by->registration_code) {
                echo ' (' . $prescribed_by->registration_code . ')';
                } ?>
            </td>
            <th>Date</th>
            <td><?= $prescribed_date ?>
            </td>
        </tr>
        <tr class="handWritten">
            <th>Signature</th>
            <td>
                <div class="dotted-write"></div>
            </td>
            <th>Contact Number</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
    </table>    
        <?php
    }
    ?>
    <table class="borders done_bys"  style="width:48%;float: left">
        <tr class="handWritten">
            <th>Screened by</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
        <tr>
            <th>Date</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
    </table>

    <table class="borders done_bys"  style="width:48%;float: right">
        <tr class="handWritten">
            <th>Dispensed by</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
        <tr>
            <th>Date</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
    </table>

    <table class="borders done_bys"  style="width:48%;float: left">
        <tr class="handWritten">
            <th>Checked by</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
        <tr>
            <th>Date</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
    </table>

    <table class="borders done_bys"  style="width:48%;float: right">
        <tr class="handWritten">
            <th>Counselled by</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
        <tr>
            <th>Date</th>
            <td>
                <div class="dotted-write"></div>
            </td>
        </tr>
    </table>
    <div class="clearfix"></div>
    <?php if ($footer_text !== null) { ?>
        <div><?= $footer_text ?></div>
    <?php } ?>
<?php } else {
    $this->widget('PrescriptionFormPrinter', array(
        'patient' => $this->patient,
        'site' => $this->site,
        'user' => $element->usermodified,
        'firm' => $this->firm,
        'items' => $element->items,
    ));
} ?>
