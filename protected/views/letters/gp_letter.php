<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="banner" style="font-size: 14pt;">
	<div class="seal"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></div>
	<div class="logo"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></div>
</div>
<div class="fromAddress">
	<?php echo $site->letterhtml ?>
	<br />Tel: <?php echo CHtml::encode($site->telephone) ?>
	<?php if($site->fax) { ?>
	<br />Fax: <?php echo CHtml::encode($site->fax) ?>
	<?php } ?>
</div>
<div class="toAddress">
	<?php $gp = $patient->gp ?>
	<?php echo $gp->contact->fullname ?>
	<br /><?php echo $gp->contact->correspondAddress->letterhtml ?>
</div>
<div class="date">
	<?php echo date(Helper::NHS_DATE_FORMAT) ?>
</div>
<div class="content">

	<p>
		Dear <?php echo $gp->contact->salutationname; ?>,
	</p>

	<p>
		<strong>Hospital number reference: <?php echo $patient->hos_num ?></strong>
	</p>

	<div>
		<div>
			<strong><?php echo $patient->fullname; ?>
			<br /><?php echo $patient->NHSDate('dob') ?>, <?php echo ($patient->gender == 'M') ? 'Male' : 'Female'; ?>
			<br /><?php echo $patient->correspondAddress->letterline ?></strong>
		</div>
		<?php if (!empty($patient->nhs_num)) { ?>
		<strong>NHS number: <?php echo $patient->nhs_num; } ?></strong>
	</div>

	<p>
		This patient was recently referred to this hospital and a decision was made that surgery was appropriate under the care of
		<?php 
			if($consultant = $firm->getConsultant()) {
				$consultantName = $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
			} else {
				$consultantName = 'CONSULTANT';
			}
		?>
		<?php echo CHtml::encode($consultantName) ?>.
	</p>
	
	<p>
		In accordance with the National requirements our admission system provides patients with the opportunity to agree the date
		for their operation. We have written twice to ask the patient to contact us to discuss and agree a date but we have had no
		response.
	</p>
	
	<p>
		Therefore we have removed this patient from our waiting list and we are referring them back to you.
	</p>

<?php $this->renderPartial("/letters/letter_end"); ?>

<?php $this->renderPartial("/letters/break"); ?>

<?php $this->renderPartial("/letters/removal_letter",array(
		'site' => $site,
		'patient' => $patient,
		'firm' => $firm,
	)); ?>
