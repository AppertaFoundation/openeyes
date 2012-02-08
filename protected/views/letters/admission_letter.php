<?php $this->renderPartial("/letters/letter_start", array(
	'site' => $site,
	'patient' => $patient,
)); ?>

<?php
	$booking = $operation->booking;
	if($consultant = $firm->getConsultant()) {
		$consultantName = $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
	} else {
		$consultantName = 'CONSULTANT';
	}
	$specialty = $firm->serviceSpecialtyAssignment->specialty;
?>
<?php if ($patient->isChild()) {
	// Start Child ?>

<p>
	<?php if ($operation->status == ElementOperation::STATUS_RESCHEDULED) {
		// Rescheduled ?>
		I am writing to inform you that the date for your child's eye operation has been
		changed<?php if(isset($cancelledBookings[0])) { echo ' from ' . date('jS F Y', strtotime($cancelledBookings[0]->date)); } ?>.
		The details are now:
	<?php } else {
		// Scheduled ?>
		I am writing to confirm the date for your child's eye operation. The details are:
	<?php } ?>
</p>

<table>
	<tr>
		<th>Date of admission:</th>
		<td><?php echo date('jS F Y', strtotime($booking->session->date)) ?></td>
		<th>Time to arrive:</th>
		<td><?php echo date('g:ia',strtotime($booking->admission_time)) ?></td>
	</tr>
	<tr>
		<th>Ward:</th>
		<td>
		<?php if ($site->id == 5) {
			// St George's ?>
			St Georges Jungle Ward
		<?php } else { 
			// City Road ?>
			Richard Desmond's Children's Eye Centre (RDCEC)
		<?php }	?>
		</td>
		<th>Location:</th>
		<td><?php echo CHtml::encode($site->name); ?></td>
	</tr>
	<tr>
		<th>Consultant:</th>
		<td><?php echo $consultantName ?></td>
		<th>Speciality:</th>
		<td><?php echo $specialty->name ?></td>
	</tr>
</table>

<p>
	To help ensure this admission proceeds smoothly, please follow these instructions:
</p>

<ul>
	<?php if ($site ->id != 5) {
		// City Road ?>
	<li><strong>Please contact the Children's Ward as soon as possible on 0207
			566 2595 or 2596 to discuss pre-operative instructions</strong></li>
	<?php } ?>
	<li>Bring this letter with you on date of admission</li>
	<?php if ($site->id == 5) {
		// St Georges ?>
	<li>Please go directly to the Jungle Ward on level 5 of the Lanesborough wing at the time of your child's admission</li>
	<?php } else { ?>
	<li>Please go directly to the Main Reception in the RDCEC at the time of your child's admission</li>
	<?php } ?>
</ul>

<p>
	If there has been any change in your child's general health, such as a cough or cold, any infectious disease,
	or any other condition which might affect their fitness for operation, please telephone
	<?php if ($site->id == 5) {
		// St Georges ?>
	020 8725 0060
	<?php } else { ?>
	0207 566 2596 and ask to speak to a nurse
	<?php } ?>
	for advice.
</p>

<p>
	If you do not speak English, please arrange for an English speaking adult to stay with you until you reach
	the ward and have been seen by a doctor and anaesthetist.
</p>

<p>
	It is very important that you let us know immediately if you are unable to keep this admission	date.
	Please let us know by return of post, or if necessary, telephone
	<?php if ($site->id == 5) {
		// St Georges ?>
	the Admissions Department 020 8725 0060
	<?php } else { ?>
	the Paediatrics and Strabismus Admission Coordinator on 020 7566 2258.
	<?php } ?>
</p>

<?php
	} // End Child
	else { 
		// Start Adult ?>
		
<p>
	<?php if ($operation->status == ElementOperation::STATUS_RESCHEDULED) { 
		// Adult Rescheduled ?>
	I am writing to inform you that the date for your eye operation has	been
	changed<?php if(isset($cancelledBookings[0])) { echo ' from ' . date('jS F Y', strtotime($cancelledBookings[0]->date)); } ?>,
	the new details are:
	<?php } else {
		// Adult Scheduled ?>
	I am pleased to confirm the date of your operation with <?php echo $consultantName ?>, the details are:
	<?php } ?>
</p>

<table>
	<tr>
		<th>Date of admission:</th>
		<td><?php echo date('jS F Y', strtotime($booking->session->date)) ?></td>
		<th>Time to arrive:</th>
		<td><?php echo date('g:ia',strtotime($booking->admission_time)) ?></td>
	</tr>
	<tr>
		<th>Ward:</th>
		<td><?php if ($specialty->id == 13) {
			// Refractive laser ?>
			Refractive waiting room - Cumberledge Wing 4th Floor
		<?php } else { ?>
			<?php echo CHtml::encode($booking->ward->name); ?>
		<?php } ?>
		</td>
		<th>Location:</th>
		<td><?php echo CHtml::encode($site->name); ?></td>
	</tr>
	<tr>
		<th>Consultant:</th>
		<td><?php echo $consultantName ?></td>
		<th>Speciality:</th>
		<td><?php echo $specialty->name ?></td>
	</tr>
</table>

<?php if(!$operation->overnight_stay) { ?>
<p>
	<em>This is a daycase and you will be discharged from hospital on the same day.</em>
</p>
<?php } ?>

<?php if($specialty->id != 13) { // Not Refractive laser ?>
<p>
	<strong>All admissions require a Pre-Operative Assessment which you must attend. Non-attendance will cause
	a delay to your surgery.</strong>
</p>
<?php } ?>

<p>
	<strong>It is important that you let us know immediately if you wish to cancel or rearrange this admission
	date.  
	<?php if ($site->id == 1 && $specialty->id != 13) {	// City Road and not Refractive ?>
	You can do this by calling <?php echo $refuseContact ?> within 5 working days.
	<?php } else { ?>
	Please let us know by return of post, or if necessary, telephone <?php echo $refuseContact ?>.
	<?php } ?>
	</strong>
</p>

<p>
	<?php if (!$healthContact) {	// City Road and not Refractive ?>
	If you are unwell the day before admission, please contact us to ensure that it is still safe and
	appropriate to do the procedure.
	<?php } else { ?>
	If there has been any change in your general health, such as a cough or cold, any infection disease, or any
	other condition which might affect your fitness for operation, please telephone <?php echo $healthContact ?>
	for advice.
	<?php } ?>
</p>

<p>
	If you do not speak English, please arrange for an English speaking adult to stay with you until you reach
	the ward and have been seen by a Doctor.
</p>

<p>
	To help ensure your admission proceeds smoothly, please follow these instructions:
</p>

<ul>
	<li>Bring this letter with you on date of admission</li>
	<li>Please go directly to <?php if ($specialty->id == 13) {
		// Refractive laser ?> Refractive waiting room - Cumberledge Wing 4th
		Floor<?php } else { ?><?php echo CHtml::encode($booking->ward->name) ?> ward<?php } ?></li>
	<li>Please bring with you any medication you are using</li>
	<li>You must not drive yourself to or from hospital</li>
	<li>We would like to request that only 1 person should accompany you in
		order to ensure that adequate seating area is available for patients
		coming for surgery</li>
</ul>

<?php } // End Adult ?>

<?php $this->renderPartial("/letters/letter_end"); ?>
