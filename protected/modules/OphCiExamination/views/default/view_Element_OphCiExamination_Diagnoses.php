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
use OEModule\OphCiExamination\components\ExaminationHelper;

?>
<?php
// construct arrays of other subspecialty principal diagnoses
list($right_principals, $left_principals) = ExaminationHelper::getOtherPrincipalDiagnoses($this->episode);
 ?>
<div class="element-data element-eyes row">
    <div class="element-eye right-eye column">
        <?php
        $principal = OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()->find('element_diagnoses_id=? and principal=1 and eye_id in (2,3)', array($element->id));
        if ($principal) {
        ?>
        <div class="data-row">
            <div class="data-value">
                <strong>
                    <?php echo $principal->disorder->term ?>
                </strong>
            </div>
        </div>
        <?php
        } foreach($right_principals as $disorder) { ?>
            <div class="data-row">
                <div class="data-value">
                        <?= $disorder[0]->term ?> <span class="has-tooltip fa fa-info-circle" data-tooltip-content="Principal diagnosis for <?= $disorder[1] ?>"></span>
                </div>
            </div>
        <?php
        }

        $diagnoses = \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()
            ->findAll('element_diagnoses_id=? and principal=0 and eye_id in (2,3)', array($element->id));
        foreach ($diagnoses as $diagnosis) {
            ?>
            <div class="data-row">
                <div class="data-value">
                    <?php echo $diagnosis->disorder->term ?>
                </div>
            </div>
            <?php
        } ?>
    </div>
    <div class="element-eye left-eye column">
        <?php
        $principal = \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()
            ->find('element_diagnoses_id=? and principal=1 and eye_id in (1,3)', array($element->id));
        if ($principal) {
            ?>
            <div class="data-row">
                <div class="data-value">
                    <strong>
                        <?php echo $principal->disorder->term ?>
                    </strong>
                </div>
            </div>
            <?php
        }
        foreach($left_principals as $disorder) { ?>
            <div class="data-row">
                <div class="data-value">
                    <?= $disorder[0]->term ?> <span class="has-tooltip fa fa-info-circle" data-tooltip-content="Principal diagnosis for <?= $disorder[1] ?>"></span>
                </div>
            </div>
            <?php
        }
        $diagnoses = \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()
            ->findAll('element_diagnoses_id=? and principal=0 and eye_id in (1,3)', array($element->id));
        foreach ($diagnoses as $diagnosis) {
            ?>
            <div class="data-row">
                <div class="data-value">
                    <?php echo $diagnosis->disorder->term ?>
                </div>
            </div>
            <?php
        } ?>
    </div>
</div>

