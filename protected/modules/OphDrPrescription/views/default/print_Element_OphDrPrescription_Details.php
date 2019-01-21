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

$copy = $data['copy'];

$header_text = null;
$footer_text = null;

$allowed_tags = '<b><br><div><em><h1><h2><h3><h4><h5><h6><hr><i><ul><ol><li><p><small><span><strong><sub><sup><u><wbr><table><thead><tbody><tfoot><tr><th><td><colgroup>';

$header_param = Yii::app()->params['prescription_boilerplate_header'];
if ($header_param !== null) {
    $header_text = strip_tags($header_param, $allowed_tags);
}

$footer_param = Yii::app()->params['prescription_boilerplate_footer'];
if ($footer_param !== null) {
    $footer_text = strip_tags($footer_param, $allowed_tags);
}

?>

<?php
$firm = $element->event->episode->firm;
$consultantName = $firm->consultant ? $firm->consultant->getFullName() : 'None';
$subspecialty = $firm->serviceSubspecialtyAssignment->subspecialty;
?>

<?php if ($header_text !== null): ?>
  <div class="clearfix"><?= $header_text ?></div>
<?php endif; ?>

  <table class="borders prescription_header">
    <tr>
      <th>Patient Name</th>
      <td><?php echo $this->patient->fullname ?> (<?php echo $this->patient->gender ?>)</td>
      <th>Hospital Number</th>
      <td><?php echo $this->patient->hos_num ?></td>
    </tr>
    <tr>
      <th>Date of Birth</th>
      <td><?php echo $this->patient->NHSDate('dob') ?> (<?php echo $this->patient->age ?>)</td>
      <th><?php echo Yii::app()->params['nhs_num_label']?> Number</th>
      <td><?php echo $this->patient->getNhsnum() ?></td>
    </tr>
    <tr>
      <th>Consultant</th>
      <td><?php echo $consultantName ?></td>
      <th>Service</th>
      <td><?php echo $subspecialty->name ?></td>
    </tr>
    <tr>
      <th>Patient's address</th>
      <td colspan="3"><?php echo $this->patient->getSummaryAddress(', ') ?></td>
    </tr>
  </table>

  <div class="spacer"></div>

  <h2>Allergies</h2>
  <table class="borders">
    <tr>
      <td><?php echo $this->patient->getAllergiesString(); ?></td>
    </tr>
  </table>

  <div class="spacer"></div>

<?php
$items_data = $this->groupItems($element->items);
foreach ($items_data as $group => $items) { ?>
  <b>
      <?php
      $group_name = OphDrPrescription_DispenseCondition::model()->findByPk($group)->name;
      echo $group_name; ?>
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
          <th>Dispensed</th>
          <th>Checked Status</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($items as $item) {
        /** @var OphDrPrescription_Item $item */
        ?>
        <tr
                class="prescriptionItem<?php if ($this->patient->hasDrugAllergy($item->medication_id)) { ?> allergyWarning<?php } ?>">
            <td class="prescriptionLabel"><?php echo $item->medication->preferred_term; ?></td>
            <td><?php echo is_numeric($item->dose) ? ($item->dose . " " . $item->dose_unit_term) : $item->dose ?></td>
            <td><?php echo $item->route->term ?><?php if ($item->laterality) {
                    echo ' (' . $item->laterality . ')';
                } ?></td>
            <td><?php echo $item->frequency->term; ?></td>
            <td><?php echo $item->drugDuration->name ?></td>
            <?php if(strpos($group_name,"Hospital") !== false ){?>
                <td><?php echo $item->dispense_location->name ?></td>
                <td></td>
                <td></td>
            <?php }?>
        </tr>
        <?php foreach ($item->tapers as $taper) { ?>
            <tr class="prescriptionTaper">
                <td class="prescriptionLabel">then</td>
                <td><?php echo is_numeric($taper->dose) ? ($taper->dose . " " . $item->dose_unit_term) : $taper->dose ?></td>
                <td>-</td>
                <td><?php if ($data['copy'] == 'patient') {
                        echo $taper->frequency->term;
                    } else {
                        echo $taper->frequency->code;
                    } ?>
                </td>
                <td><?php echo $taper->duration->name ?></td>
                <?php if(strpos($group_name,"Hospital") !== false ){?>
                    <td></td>
                    <td>-</td>
                    <td>-</td>
                <?php }?>
            </tr>
            <?php
        }

        if (strlen($item->comments) > 0) { ?>
          <tr class="prescriptionComments">
            <td class="prescriptionLabel">Comments:</td>
            <td colspan="<?php echo strpos($group_name, "Hospital") !== false ? 7 : 4 ?>">
              <i><?=\CHtml::encode($item->comments); ?></i></td>
          </tr>
        <?php }
    } ?>
    </tbody>
  </table>
<?php } ?>
  <div class="spacer"></div>

  <table class="borders prescription_items">
    <colgroup>
      <col width="25%">
      <col width="75%">
    </colgroup>
    <tbody>
    <tr>
      <td>Other medications patient is taking</td>
      <td>
          <?php $this->widget('OEModule\OphCiExamination\widgets\HistoryMedications', array(
              'patient' => $this->patient,
              'mode' => OEModule\OphCiExamination\widgets\HistoryMedications::$PRESCRIPTION_PRINT_VIEW,
          )); ?>
      </td>
    </tr>
    </tbody>
  </table>

  <h2>Comments</h2>
  <table class="borders">
    <tr>
      <td><?php echo $element->comments ? $element->textWithLineBreaks('comments') : '&nbsp;' ?></td>
    </tr>
  </table>

  <div class="spacer"></div>
<?php if (!$data['copy'] && $site_theatre = $this->getSiteAndTheatreForLatestEvent()) { ?>
  <table class="borders done_bys">
    <tr>
      <th>Site</th>
      <td><?php echo $site_theatre->site->name ?></td>
        <?php if ($site_theatre->theatre) { ?>
          <th>Theatre</th>
          <td><?php echo $site_theatre->theatre->name ?></td>
        <?php } ?>
    </tr>
  </table>
  <div class="spacer"></div>
<?php } ?>
  <table class="borders done_bys">
    <tr>
      <th>Prescribed by</th>
      <td><?php echo $element->usermodified->fullname ?><?php if ($element->usermodified->registration_code) echo ' (' . $element->usermodified->registration_code . ')' ?>
      </td>
      <th>Date</th>
      <td><?php echo $element->NHSDate('last_modified_date') ?>
      </td>
    </tr>
    <tr class="handWritten">
      <th>Clinical Checked by</th>
      <td>&nbsp;</td>
      <th>Date</th>
      <td>&nbsp;</td>
    </tr>
  </table>

<?php if ($footer_text !== null): ?>
  <div><?= $footer_text ?></div>
<?php endif; ?>