<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
?>
<div class="box admin">
    <h2>Contact location</h2>
    <div class="data-group">
        <div class="cols-2 column">
            <div class="data-label">Contact:</div>
        </div>
        <div class="cols-10 column">
            <div class="data-value"><?= $location->contact->fullName ?></div>
        </div>
    </div>
    <div class="data-group">
        <div class="cols-2 column">
            <div class="data-label"><?= $location->site_id ? 'Site' : 'Institution' ?>:</div>
        </div>
        <div class="cols-10 column">
            <div class="data-value">
                <?= $location->site ? $location->site->name : $location->institution->name ?>
            </div>
        </div>
    </div>
</div>

<div class="box admin">
    <h2>Patients</h2>
    <form id="admin_contact_patients">
        <table class="standard">
            <thead>
            <tr>
                <th><?= PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(Yii::app()->params['display_primary_number_usage_code'], $institution_id, $site_id) ?></th>
                <th>Title</th>
                <th>First name</th>
                <th>Last name</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($location->patients as $i => $patient) { ?>
                <tr class="clickable" data-id="<?= $patient->id ?>" data-uri="patient/view/<?= $patient->id ?>">
                    <td><?= PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $patient->id, Institution::model()->getCurrent()->id, Yii::app()->session['selected_site_id'])) ?></td>
                    <td><?= $patient->title ?></td>
                    <td><?= $patient->first_name ?></td>
                    <td><?= $patient->last_name ?>&nbsp</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
</div>