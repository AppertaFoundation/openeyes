<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\components\ExaminationHelper;
?>
<?php
list($right_principals, $left_principals) = ExaminationHelper::getOtherPrincipalDiagnoses($this->episode);
?>
<div class="element-data element-eyes flex-layout">
    <div class="js-element-eye cols-6 sright-eye">
        <?php if ($principal = OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()->find('element_diagnoses_id=? and principal=1 and eye_id in (2,3)', array($element->id))) {
            ?>
            <div class="data-group">
                <div class="data-value">
                    <strong>
                        <?php echo $principal->getDisplayDate() ?>
                        <?php echo $principal->eye->adjective ?>
            <?php echo $principal->disorder->term ?>
                    </strong>
                </div>
            </div>
        <?php } foreach ($right_principals as $disorder) {
            ?>
            <div class="data-group">
                <div class="data-value">
            <?= ($disorder[0]->term) ?><sup>*</sup></span>
                </div>
                </div>
            </div>
            <?php
        }
        foreach (\OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()->findAll('element_diagnoses_id=? and principal=0 and eye_id in (2,3)', array($element->id)) as $diagnosis) {
            ?>
            <div class="data-group">
                <div class="data-value">
                <?php echo $diagnosis->getDisplayDate() ?>
                <?php echo $diagnosis->eye->adjective ?>
                <?php echo $diagnosis->disorder->term ?>
                </div>
            </div>
            <?php }
        ?>
    </div>
    <div class="js-element-eye cols-6 left-eye">
        <?php if ($principal = \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()->find('element_diagnoses_id=? and principal=1 and eye_id in (1,3)', array($element->id))) {
            ?>
            <div class="data-group">
                <div class="data-value">
                    <strong>
                        <?php echo $principal->getDisplayDate() ?>
                        <?php echo $principal->eye->adjective ?>
            <?php echo $principal->disorder->term ?>
                    </strong>
                </div>
            </div>
            <?php
        }
        foreach ($left_principals as $disorder) {
            ?>
            <div class="data-group">
                <div class="data-value">
            <?= ($disorder[0]->term) ?><sup>*</sup></span>
                </div>
            </div>
            <?php
        } foreach (\OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()->findAll('element_diagnoses_id=? and principal=0 and eye_id in (1,3)', array($element->id)) as $diagnosis) {
            ?>
            <div class="data-group">
                <div class="data-value">
                    <?php echo $diagnosis->getDisplayDate() ?>
            <?php echo $diagnosis->eye->adjective ?>
            <?php echo $diagnosis->disorder->term ?>
                </div>
            </div>
        <?php }
        ?>
    </div>
</div>
<?php if (count($left_principals) || count($right_principals)) { ?>
    <p><small><sup>*</sup>This diagnosis is included because it is the principle diagnosis from another ophthalmic subspecialty episode</small></p>
<?php } ?>
