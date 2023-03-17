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
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .d_overview th,
    .d_overview td {
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

    .d_data td,
    .d_data th {
      border: 1px double black;
      padding: 3px;
    }

    .d_data td:last-child,
    .d_data th:last-child {
      text-align: right;
    }

    .label {
      font-weight: 600;
    }
  }
</style>

<?php
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
$diary_count = count($diary) - 1;
foreach ($diary as $i => $theatre) { ?>
  <div <?= $i < $diary_count ? 'style="page-break-after:always"' : '' ?>>
    <h3 class="theatre">
      <strong><?php echo htmlspecialchars($theatre->name, ENT_QUOTES) ?> (<?= htmlspecialchars($theatre->site->name, ENT_QUOTES) ?>)</strong>
    </h3>
    <?php
    $sessions_count = count($theatre->sessions) - 1;
    foreach ($theatre->sessions as $j => $session) { ?>
      <div id="diaryTemplate" <?= $j < $sessions_count ? 'style="page-break-after:always"' : '' ?>>
        <div id="d_title">OPERATION LIST FORM</div>
        <table class="d_overview">
          <tbody>
            <tr>
              <td class='label'>THEATRE NO:</td>
              <td colspan="2"><?= htmlspecialchars($theatre->name, ENT_QUOTES) ?></td>
            </tr>
            <tr>
              <td class='label'>SESSION:</td>
              <td><?= $session->start_time ?> - <?= $session->end_time ?></td>
              <td>NHS</td>
            </tr>
            <tr>
              <td><span class='label'>SURGICAL FIRM:</span><?= htmlspecialchars($session->firmName, ENT_QUOTES) ?></td>
              <td><span class='label'>ANAESTHETIST:</span></td>
              <td>&nbsp;</td>
              <td><span class='label'>DATE:</span></td>
              <td><?= Helper::convertDate2NHS($session->date) ?></td>
            </tr>
            <tr>
              <td> <span class='label'>COMMENTS:</span><?= \CHtml::encode($session->comments) ?></td>
            </tr>
          </tbody>
        </table>
        <table class="d_data">
          <tbody>
            <tr>
              <th><?= PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $institution_id, $site_id) ?></th>
              <th>PATIENT</th>
              <th>AGE</th>
              <th>WARD</th>
              <th>GA or LA</th>
              <th>PRIORITY</th>
              <th>PROCEDURES AND COMMENTS</th>
              <th>ADMISSION TIME</th>
            </tr>
            <?php foreach ($session->getActiveBookingsForWard($ward_id) as $booking) {
                if ($booking->operation->event) { ?>
                <tr>
                  <td><?= PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $booking->operation->event->episode->patient->id, $institution_id, $site_id)) ?></td>
                  <td><?= strtoupper($booking->operation->event->episode->patient->last_name) ?>, <?= $booking->operation->event->episode->patient->first_name ?></td>
                  <td><?= $booking->operation->event->episode->patient->age ?></td>
                  <td><?= $booking->ward ? htmlspecialchars($booking->ward->name, ENT_QUOTES) : 'None' ?></td>
                  <td><?= htmlspecialchars($booking->operation->getAnaestheticTypeDisplay(), ENT_QUOTES) ?></td>
                  <td><?= $booking->operation->priority->name ?></td>
                  <td style="max-width: 500px; word-wrap:break-word; overflow: hidden;"><?= $booking->operation->procedures ? "[{$booking->operation->eye->adjective}] " . $booking->operation->getProceduresCommaSeparated() : 'No procedures' ?>
                    <br /><?= \CHtml::encode($booking->operation->comments) ?>
                  </td>
                  <td><?= substr($booking->admission_time, 0, 5) ?></td>
                </tr>
                <?php }
            } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
  </div>
<?php } ?>
