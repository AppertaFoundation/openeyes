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
//$clinical = $this->checkAccess('OprnViewClinical');
$warnings = $this->patient->getWarnings($allow_clinical);
Yii::app()->assetManager->registerCssFile('components/font-awesome/css/font-awesome.css', null, 10);
?>

<div class="panel patient<?php if ($warnings): echo ' warning'; endif; ?><?= $this->patient->isDeceased() ? ' patient-deceased' : ''?>" id="patientID">
    <div class="patient-details">
        <?php echo CHtml::link($this->patient->getDisplayName(), array('/patient/view/'.$this->patient->id)) ?>
        <span class="patient-title">
			(<?= $this->patient->title ?>)
            </span>
    </div>
    <div class="hospital-number">
		<span>
			Hospital No.
		</span>
        <?php echo $this->patient->hos_num?>
    </div>
    <div class="row">
        <div class="large-8 column">
            <!-- NHS number -->
            <div class="nhs-number warning">
				<span class="hide-text print-only">
					NHS number:
				</span>
                <?php echo $this->patient->nhsnum?>
                <?php if ($this->patient->nhsNumberStatus && $this->patient->nhsNumberStatus->isAnnotatedStatus()):?>
                    <i class="fa fa-asterisk" aria-hidden="true"></i><span class="messages"><?= $this->patient->nhsNumberStatus->description;?></span>
                <?php endif;?>
            </div>

            <!-- Gender -->
            <span class="patient-gender">
				<?php echo $this->patient->getGenderString() ?>
			</span>

        </div>
        <div class="large-4 column end">
            <div class="row">
                <div class="patient-summary-anchor">
                    <?php echo CHtml::link('Summary',array('/patient/view/'.$this->patient->id)); ?>
                </div>
            </div>
            <?php if(Yii::app()->params['allow_clinical_summary']){?>
                <div class="large-4 column clinical-summary-anchor">
                    <?php echo CHtml::link('Clinical Summary', array('/dashboard/oescape/'.$this->patient->id), array('target' => '_blank')); ?>
                </div>
            <?php }?>
        </div>
    </div>
    <!-- Widgets (extra icons, links etc) -->
    <ul class="patient-widgets">
        <?php if($this->patient->isEditable() ):?>
            <li>
                <a class="patient-edit-link" href="<?php echo $this->controller->createUrl('/patient/update/' . $this->patient->id); ?>"> <span class="fa fa-pencil-square" aria-hidden="true" aria-title="Edit patient"></span></a>
            </li>
        <?php endif; ?>
        <?php foreach ($this->widgets as $widget) {
            echo "<li>{$widget}</li>";
        }?>
    </ul>
</div>
