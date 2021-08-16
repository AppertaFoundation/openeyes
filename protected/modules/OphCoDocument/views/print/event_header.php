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
$event = $this->event;
$event_type = $event->eventType->name;
$logo_helper = new LogoHelper();
$institution_id = Institution::model()->getCurrent()->id;
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $this->patient->id, $institution_id, $this->selectedSiteId);
$secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $this->patient->id, $institution_id, $this->selectedSiteId);
?>
<header class="header">
    <div class="title">
        <?= $logo_helper->render('//base/_logo_seal'); ?>
        <h1><?php if ($this->attachment_print_title != null) {
                echo $this->attachment_print_title;
            } else {
                echo $event_type;
            } ?></h1>
    </div>
    <div class="data-group">
        <!-- Patient details -->
        <div class="cols-4 column patient">
            <strong><?= $this->patient->contact->fullName ?></strong>
            <br/>
            <p>
                <?= $this->patient->getLetterAddress(array(
                    'delimiter' => '<br/>',
                )) ?>
            </p>
        </div>
        <div class="cols-4 column firm">
            <?php if ($consultant = $this->event->episode->firm->consultant) { ?>
                <p><strong><?= $consultant->contact->getFullName() ?></strong></p>
            <?php } ?>
            <p>Service: <strong><?= $this->event->episode->firm->getSubspecialtyText() ?></strong></p>
            <p><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) ?>
                <strong><?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?></strong>
                <br/>
                <?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) ?>
                <strong><?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier)) ?></strong>
                <br/>
                DOB: <strong><?= Helper::convertDate2NHS($this->patient->dob) ?> (<?= $this->patient->getAge() ?>
                    )</strong>
            </p>
        </div>
        <div class="dates" style="width: 33.33333%;float: left;">
            <p><?= $event_type ?> Created: <strong><?= Helper::convertDate2NHS($this->event->created_date) ?></strong></p>
            <p><?= $event_type ?> Printed: <strong><?= Helper::convertDate2NHS(date('Y-m-d')) ?></strong></p>
        </div>
    </div>
</header>
