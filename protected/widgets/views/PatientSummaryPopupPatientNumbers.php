<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/**
 * @var PatientSummaryPopup $this
 */
?>
<?php if (count($this->patient->identifiers) > 0) { ?>
    <div class="patient-numbers flex-layout">
        <div class="local-numbers">
            <?php foreach ($this->patient->localIdentifiers as $local_identifier) { ?>
                <?php if ($local_identifier->hasValue()) { ?>
                    <div class="num nowrap">
                        <?= $local_identifier->patientIdentifierType->short_title ?>
                        <label class="inline highlight">
                            <?= $local_identifier->getDisplayValue() ?>
                        </label>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <?php if ($this->patient->globalIdentifier) { ?>
            <div class="nhs-number">
                <span><?= PatientIdentifierHelper::getIdentifierPrompt($this->patient->globalIdentifier); ?></span>
                <?= PatientIdentifierHelper::getIdentifierValue($this->patient->globalIdentifier); ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
