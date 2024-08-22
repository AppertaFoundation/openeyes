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
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(SettingMetadata::model()->getSetting('display_secondary_number_usage_code'), $this->patient->id, $institution_id, $site_id);
?>

<table class="borders prescription_header">
    <tbody>
    <tr>
        <th>Patient Name</th>
        <td><?= $this->patient->contact->fullName ?></td>
        <th><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?></th>
        <td><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></td>
    </tr>
    <tr>
        <th>Date of Birth</th>
        <td><?= Helper::convertDate2NHS($this->patient->dob) ?> (<?= $this->patient->getAge() ?>)</td>
        <th><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?></th>
        <td><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier) ?></td>
    </tr>
    <tr>
        <th>Consultant</th>
        <td>
            <?php if ($consultant = $this->event->episode->firm->consultant) { ?>
                <p><strong><?= $consultant->contact->getFullName() ?></strong></p>
            <?php } ?>
        </td>
        <th>Service</th>
        <td><?= $this->event->episode->firm->getSubspecialtyText() ?></td>
    </tr>
    <tr>
        <th>Event Date</th>
        <td>
            <?= Helper::convertDate2NHS($this->event->event_date) ?>
        </td>
        <th>Printed</th>
        <td><?= Helper::convertDate2NHS(date('Y-m-d')) ?></td>
    </tr>
    <tr>
        <th>Patient's address</th>
        <td colspan="3"><?= $this->patient->getLetterAddress(array('delimiter' => '<br/>',)) ?></td>
    </tr>
    </tbody>
</table>
