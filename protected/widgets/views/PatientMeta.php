<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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
/**
 * @var $patient Patient
 * @var $coreapi CoreAPI
 */
if (!isset($patient)) {
    $patient = $this->patient;
}
if (!isset($coreapi)) {
    $coreapi = new CoreAPI();
}
$deceased = $patient->isDeceased();
$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];
$display_primary_number_usage_code = Yii::app()->params['display_primary_number_usage_code'];
$display_secondary_number_usage_code = Yii::app()->params['display_secondary_number_usage_code'];
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $patient->id, $institution->id, $selected_site_id);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $patient->id, $institution->id, $selected_site_id);
if (!isset($this->controller) || ($this->controller->id != "patient" && $this->controller->id != 'default')) { ?>
    <div class="oe-patient-meta">
        <div class="patient-name">
            <a href="<?= $coreapi->generatePatientLandingPageLink($patient, ['pathway_id' => $pathway->id]); ?>">
                <span class="patient-surname"><?= $patient->getLast_name(); ?></span>,
                <span class="patient-firstname">
                        <?= $patient->getFirst_name(); ?>
                        <?= $patient->getTitle() ? "({$patient->getTitle()})" : ''; ?>
                    </span>
            </a>
        </div>
        <div class="patient-details">
            <?php if ($display_primary_number_usage_code) { ?>
                <div class="hospital-number">
                    <span><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier); ?> </span>
                    <div class="js-copy-to-clipboard hospital-number" style="cursor: pointer;">
                        <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier); ?>
                        <?php
                        $this->widget(
                            'application.widgets.PatientIdentifiers',
                            [
                                'patient' => $patient,
                                'show_all' => true
                            ]); ?>
                        <?php if ($display_primary_number_usage_code === 'GLOBAL' && $primary_identifier && $primary_identifier->patientIdentifierStatus) { ?>
                            <i class="oe-i <?= isset($primary_identifier->patientIdentifierStatus->icon->class_name) ? $primary_identifier->patientIdentifierStatus->icon->class_name : 'exclamation' ?> small"></i>
                        <?php } ?>
                    </div>
                </div>
            <?php }
            if ($display_secondary_number_usage_code) { ?>
                <div class="nhs-number">
                    <span><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier); ?></span>
                    <?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier); ?>
                    <?php if ($display_secondary_number_usage_code === 'GLOBAL' && $secondary_identifier && $secondary_identifier->patientIdentifierStatus) { ?>
                        <i class="oe-i <?= isset($secondary_identifier->patientIdentifierStatus->icon->class_name) ? $secondary_identifier->patientIdentifierStatus->icon->class_name : 'exclamation' ?> small"></i>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="patient-gender">
                    <em>Sex</em>
                <?php echo $patient->getGenderString() ?>
            </div>
            <div class="patient-<?= $deceased ? 'died' : 'age' ?>">
                <?php if ($deceased) : ?>
                    <em>Died</em> <?= Helper::convertDate2NHS($patient->date_of_death); ?>
                <?php endif; ?>
                <em>Age<?= $deceased ? 'd' : '' ?></em> <?= $patient->getAge() . 'y'; ?>
            </div>
        </div>
    </div>
<?php } ?>

