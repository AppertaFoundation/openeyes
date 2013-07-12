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
<h2>Patient Summary</h2>
<div class="wrapTwo clearfix">
	<?php $this->renderPartial('//base/_messages'); ?>
	<?php if ($this->patient->contact->address && !$this->patient->contact->address->isCurrent()) {?>
		<div id="no-current-address-error" class="alertBox">
			<h3>Warning: The patient has no current address. The address shown is their last known address.</h3>
		</div>
	<?php }?>
	<?php if ($this->patient->isDeceased()) {?>
		<div id="deceased-notice" class="alertBox">
			This patient is deceased (<?php echo $this->patient->NHSDate('date_of_death'); ?>)
		</div>
	<?php }?>
	<?php if (!$this->patient->practice || !$this->patient->practice->contact->address) {?>
	<div id="no-practice-address" class="alertBox">
		Patient has no GP practice address, please correct in PAS before printing GP letter.
	</div>
	<?php }?>

	<div class="halfColumnLeft">
		<?php $this->renderPartial('_patient_details')?>
		<?php $this->renderPartial('_patient_contact_details')?>
		<?php $this->renderPartial('_patient_gp')?>
		<?php $this->renderPartial('_patient_contacts')?>
	</div>

	<?php if (BaseController::checkUserLevel(2)) {?>
		<?php $this->renderPartial('_patient_episodes',array(
			'episodes' => $episodes,
			'ordered_episodes' => $ordered_episodes,
			'legacyepisodes' => $legacyepisodes,
			'episodes_open' => $episodes_open,
			'episodes_closed' => $episodes_closed,
			'firm' => $firm,
		))?>
	<?php }?>
</div>
