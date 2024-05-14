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
    $deceased = $this->patient->isDeceased();
if ($this->controller->id != "patient" && $this->controller->id != 'default') { ?>
        <div class="oe-patient-meta">
            <div class="patient-name">
                <a href="<?= (new CoreAPI())->generatePatientLandingPageLink($this->patient); ?>">
                    <span class="patient-surname"><?= $this->patient->getLast_name(); ?></span>,
                    <span class="patient-firstname">
                        <?= $this->patient->getFirst_name(); ?>
                        <?= $this->patient->getTitle() ? "({$this->patient->getTitle()})" : ''; ?>
                    </span>
                </a>
            </div>
            <div class="patient-details">
                <div class="hospital-number">
                    <span><?php echo \SettingMetadata::model()->getSetting('hos_num_label') ?></span>
                    <div class="js-copy-to-clipboard hospital-number" style="cursor: pointer;"> <?php echo $this->patient->hos_num ?></div>
                </div>
                <div class="nhs-number">
                    <span><?php echo \SettingMetadata::model()->getSetting('nhs_num_label') ?></span>
                    <?php echo $this->patient->nhsnum ?>
                    <?php if ($this->patient->nhsNumberStatus) : ?>
                        <i class="oe-i <?= isset($this->patient->nhsNumberStatus->icon->class_name) ? $this->patient->nhsNumberStatus->icon->class_name : 'exclamation' ?> small"></i>
                    <?php endif; ?>
                </div>
                <div class="patient-gender">
                    <em>Gender</em>
                    <?php echo $this->patient->getGenderString() ?>
                </div>
                <div class="patient-<?= $deceased ? 'died' : 'age' ?>">
                    <?php if ($deceased) : ?>
                        <em>Died</em> <?= Helper::convertDate2NHS($this->patient->date_of_death); ?>
                    <?php endif; ?>
                    <em>Age<?= $deceased ? 'd' : '' ?></em> <?= $this->patient->getAge().'y'; ?>
                </div>
            </div>
        </div>
<?php } ?>

