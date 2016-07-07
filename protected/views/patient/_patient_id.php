<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
	$clinical = $this->checkAccess('OprnViewClinical');
	$warnings = $this->patient->getWarnings($clinical);
	Yii::app()->assetManager->registerCssFile('components/font-awesome/css/font-awesome.css', null, 10);
?>

<div class="panel patient<?php if ($warnings): echo " warning"; endif; ?>" id="patientID">
	<div class="patient-details">
		<?php echo CHtml::link($this->patient->getDisplayName(),array('/patient/view/'.$this->patient->id)) ?>
		<span class="patient-age">(<?php if ($this->patient->isDeceased()) { ?>Deceased<?php } else { echo $this->patient->getAge(); } ?>)</span>
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
				<?php if($this->patient->nhsNumberStatus && $this->patient->nhsNumberStatus->isAnnotatedStatus()):?>
					<i class="fa fa-asterisk" aria-hidden="true"></i><span class="messages"><?= $this->patient->nhsNumberStatus->description;?></span>
				<?php endif;?>
			</div>

			<!-- Gender -->
			<span class="icon icon-alert icon-alert-<?php echo strtolower($this->patient->getGenderString()) ?>_trans">
				<?php echo $this->patient->getGenderString() ?>
			</span>

			<?php
			$widgets = Yii::app()->params['patient_summary_id_widgets'];
			if (is_array($widgets)) {
				foreach ($widgets as $w) {
					$this->widget($w['class'], array(
									'patient' => $this->patient,
							));
				}
			}
			?>

			<!-- Warnings -->
			<?php if (is_array($warnings) && count($warnings) > 0 ) {
				$msgs = array();
				foreach ($warnings as $warn) {
					$msgs[] = $warn['short_msg'];
				}?>
				<span class="warning">
					<span class="icon icon-alert icon-alert-warning"></span>
					<span class="messages"><?php echo implode(', ', $msgs); ?></span>
				</span>
			<?php } ?>

		</div>
		<div class="large-4 column text-right patient-summary-anchor">
			<?php echo CHtml::link('Patient Summary',array('/patient/view/'.$this->patient->id)); ?>
		</div>
		<?php if(Yii::app()->params['allow_clinical_summary']){?>
			<div class="large-4 column clinical-summary-anchor">
				<?php echo CHtml::link('Clinical Summary',array('/dashboard/oescape/'.$this->patient->id), array('target'=>'_blank')); ?>
			</div>
		<?php }?>
	</div>
</div>
