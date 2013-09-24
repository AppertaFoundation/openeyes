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
	$clinical = (BaseController::checkUserLevel(2));

	$warnings = $this->patient->getWarnings($clinical);
?>

<div class="panel patient radius" id="patientID">
	<div class="patient-details">
		<a href="#">
			<?php echo CHtml::link($this->patient->getDisplayName(),array('/patient/view/'.$this->patient->id)) ?>
			<span class="patient-age">(<?php if ($this->patient->isDeceased()) { ?>Deceased<?php } else { echo $this->patient->getAge(); } ?>)</span>
		</a>
	</div>
	<div class="hospital-number">
		No. <?php echo $this->patient->hos_num?>
	</div>
	<div class="row">
		<div class="large-6 small-6 columns">
			<div class="nhs-number">
				<span class="hide-text">
					NHS number:
				</span>
				<?php echo $this->patient->nhsnum?>
			</div>
			<span class="icon icon-alert icon-alert-<?php echo strtolower($this->patient->getGenderString()) ?>_trans"><?php echo $this->patient->getGenderString() ?></span>

			<?php if ($warnings) {
				$msgs = array();
				foreach ($warnings as $warn) {
					$msgs[] = $warn['short_msg'];
				}
			?>
				<!-- FIXME: check these warnings display correctly -->
				<span class="warning"><span><?php echo implode(' / ', $msgs); ?></span>
			<?php } ?>
		</div>
		<div class="large-6 small-6 columns text-right patient-summary-anchor">
			<?php echo CHtml::link('Patient Summary',array('/patient/view/'.$this->patient->id)); ?>
		</div>
	</div>
</div>