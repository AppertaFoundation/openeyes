<div class="banner compact">
	<div class="logo"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></div>
</div>
<h1>Admission Form</h1>
<?php
if ($patient->address === NULL) {
	throw new SystemException('patient->address is NULL: '.print_r($patient,true));
}
?>
<table class="half right">
	<tr>
		<th>Patient Name</th>
		<td><?php echo $patient->fullname ?></td>
	</tr>
	<tr>
		<th>Address</th>
		<td><?php echo $patient->address->letterhtml ?></td>
	</tr>
</table>	
<table class="half">
	<tr>
		<th>Hospital Number</th>
		<td><?php echo $patient->hos_num ?></td>
	</tr>
	<tr>
		<th>DOB</th>
		<td><?php echo $patient->NHSDate('dob'); ?></td>
	</tr>
</table>

<h2>Admission Information</h2>
<table>

	<tr>
		<?php $booking = $operation->booking; ?>
		<th><?php if($booking) { ?>Admitting Consultant:<?php } else { ?>Consultant:<?php } ?></th>
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
		<td><?php echo CHtml::encode($firm->serviceSpecialtyAssignment->service->name) ?></td>
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

<table>

	<tr>
		<th>Priority:</th>
		<td><?php echo ($operation->urgent) ? 'Urgent' : 'Routine'; ?></td>
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
		<?php } else { 	?>
		<th colspan="2" rowspan="4">Patient Added to partial bookings waiting List, admission Date to be arranged</th>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Eye:</th>
		<td><?php echo $operation->getEyeText() ?></td>
		<?php if ($booking) { ?>
		<th>Theatre session:</th>
		<td><?php echo substr($booking->session->start_time,0,5) . ' - ' . substr($booking->session->end_time,0,5)?></td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Diagnosis:</th>
		<td>
			<?php if ($operation->getDisorder()) {
				echo $operation->getEyeText() . ' ' . CHtml::encode($operation->getDisorder());
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
		<td><?php echo $operation->getAnaestheticText() ?></td>
		<?php if ($booking) { ?>
		<th>Proposed admission date:</th>
		<td><?php echo $booking->session->NHSDate('date'); ?></td>
		<?php } ?>
	</tr>
	
</table>

<h2>Comments</h2>
	<table>
		<tr>
			<td height="50"><?php echo nl2br(CHtml::encode($operation->comments)); ?></td>
		</tr>
	</table>

<h2>Pre-op Assessment Date</h2>
	<table>
		<tr>
			<td height="50"></td>
		</tr>
	</table>

