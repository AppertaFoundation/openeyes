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
<h1>Admission Form</h1>

<table>

	<tr>
		<th>Hospital Number</th>
		<td><?php echo $patient->hos_num ?></td>
		<th>Patient Name</th>
		<td><?php echo $patient->fullname ?></td>
	</tr>

	<tr>
		<th>NHS Number</th>
		<td><?php echo $patient->nhsnum?></td>
		<th rowspan="2">Address</th>
		<td rowspan="2"><?php echo $patient->correspondAddress->letterhtml ?></td>
	</tr>

	<tr>
		<th>DOB</th>
		<td><?php echo $patient->NHSDate('dob'); ?></td>
	</tr>
	
</table>

<h2>Admission Information</h2>
	<table class="borders">

		<tr>
			<?php $booking = $operation->booking; ?>
			<th><?php if($booking) { ?>Admitting Consultant:<?php } else { ?>Consultant:<?php } ?>
			</th>
			<?php 
			if($consultant = $firm->getConsultant()) {
				$consultantName = $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
			} else {
				$consultantName = 'CONSULTANT';
			}
		?>
		<td><?php if($emergencyList) { ?>Emergency List (<?php echo CHtml::encode($consultantName); ?>)<?php } else { echo CHtml::encode($consultantName); }?></td>
		<th>Decision to admit (or today's) date:</th>
		<td><?php echo $operation->NHSDate('decision_date'); ?></td>
	</tr>

	<tr>
		<th>Service:</th>
		<td><?php echo CHtml::encode($firm->serviceSubspecialtyAssignment->service->name) ?></td>
		<th>Patient Telephone:</th>
		<td><?php echo CHtml::encode($patient->primary_phone) ?></td>
	</tr>

	<tr>
		<th>Site:</th>
		<td><?php echo CHtml::encode($site->name) ?></td>
		<?php if($booking) { ?>
		<th>Person organising operation:</th>
		<td><?php echo $booking->user->getFullName() ?></td>
		<?php } else { ?>
		<th>Person organising admission:</th>
		<td><?php echo $operation->event->user->getFullName() ?></td>
		<?php } ?>
	</tr>

</table>

<h2>Admission Details</h2>

<table class="borders">

	<tr>
		<th>Priority:</th>
		<td><?php echo $operation->priority->name?></td>
		<th>Admission category:</th>
		<td><?php echo ($operation->overnight_stay) ? 'an overnight stay' : 'day case'; ?></td>
	</tr>

	<tr>
		<th>Consultant to be present:</th>
		<td><?php echo (empty($operation->consultant_required)) ? 'No' : 'Yes'; ?></td>
		<th>Total theatre time (mins):</th>
		<td><?php echo CHtml::encode($operation->total_duration) ?></td>
	</tr>
	
	<tr>
		<th>Intended procedure(s):</th>
		<td><?php echo CHtml::encode($operation->proceduresString); ?></td>
		<?php if($booking) { ?>
		<th>Operation date:</th>
		<td><?php echo $booking->session->NHSDate('date'); ?></td>
		<?php } else {	?>
		<th colspan="2" rowspan="4">Patient Added to partial bookings waiting List, admission Date to be arranged</th>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Eye:</th>
		<td><?php echo $operation->eye->name?></td>
		<?php if ($booking) { ?>
		<th>Theatre session:</th>
		<td><?php echo substr($booking->session->start_time,0,5) . ' - ' . substr($booking->session->end_time,0,5)?></td>
	</tr>
	<tr>
		<th>Theatre:</th>
		<td><?php echo $booking->session->TheatreName?></td>
		<th>Ward:</th>
		<td><?php echo $booking->ward->name?></td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Diagnosis:</th>
		<td>
			<?php if ($operation->getDisorder()) {
				echo $operation->eye->adjective. ' ' . CHtml::encode($operation->getDisorder());
			} else {
				echo 'Unknown';
			} ?>
		</td>
		<?php if ($booking) { ?>
		<th>Admission time:</th>
		<td><?php echo date('H:i',strtotime($booking->admission_time)) ?></td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Anaesthesia:</th>
		<td><?php echo $operation->anaesthetic_type->name?></td>
		<?php if ($booking) { ?>
		<th>Proposed admission date:</th>
		<td><?php echo $booking->session->NHSDate('date'); ?></td>
		<?php } ?>
	</tr>
	
</table>

<h2>Comments</h2>
	<table class="borders">
		<tr>
			<td height="50"><?php echo nl2br(CHtml::encode($operation->comments)); ?></td>
		</tr>
	</table>

<h2>Pre-op Assessment Date</h2>
	<table class="borders">
		<tr>
			<td height="50"></td>
		</tr>
	</table>

