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
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $patient->id, $institution_id, $site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_secondary_number_usage_code'], $patient->id, $institution_id, $site_id);
?>
<p<?php if (@$accessible) {
    ?> class="accessible"<?php
  } ?>>Dear <?= $to; ?>,</p>
<p<?php if (@$accessible) {
    ?> class="accessible"<?php
  } ?>>
    <?php if (@$patient_ref) {
        echo $patient->fullname . ', ';
    } ?>
    <strong><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?>
        : <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?>
        <?php if ($secondary_identifier) { ?>
            <br/><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?> Number: <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?>
        <?php } ?>
        <?php if (@$patient_ref) { ?>
            <br/><?= $patient->getLetterAddress(array('delimiter' => ', ')) ?>
            <br/>DOB: <?= $patient->NHSDate('dob') ?>, <?= ($patient->gender == 'M') ? 'Male' : 'Female'; ?>
        <?php } ?></strong>
</p>
