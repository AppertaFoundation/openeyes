<?php $this->renderPartial("eventTypeTemplates/view/25/letter_start", array(
	'site' => $site,
	'patientDetails' => $patientDetails,
	'patientName' => $patientName,
	'patient' => $patient,
)); ?>

<?php if (!empty($operation->booking)) { ?>

	<?php if ($patient->isChild()) { // Start Child
		if ($operation->status == ElementOperation::STATUS_RESCHEDULED) { // Child Rescheduled?>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	I am writing to inform you that the date for your child's eye operation
	has been changed from
	<?php echo date('d M Y', strtotime($cancelledBookings[0]->date)) ?>
	. The details now are:
</p>


	<?php } else { // Child Scheduled
			if ($site->id == 5) {
				// St George's ?>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	On behalf of
	<?php echo $consultantName ?>
	, I am delighted to confirm the date you have agreed for your child's
	operation. The details are:
</p>


		<?php } else {
			// City Road	?>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	I am writing to confirm the date for your child's eye operation. The
	details are:</p>


		<?php	}
		} ?>

<table
	style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	<tr>
		<td>Date of admission:</td>
		<td><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?>
		</td>
	</tr>
	<tr>
		<td>Time to arrive:</td>
		<td><?php echo $operation->booking->admission_time ?></td>
	</tr>
	<tr>
		<td>Date of surgery:</td>
		<td><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?>
		</td>
	</tr>
	<tr>
		<td>Location:</td>
		<td>
	<?php if ($site->id == 5) {
			// St George's ?>
			St Georges Jungle Ward
		<?php } else { 
			// City Road ?>
			Richard Desmond's Children's Eye Centre (RDCEC)
		<?php }	?>
		</td>
	</tr>
</table>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	To ensure your admission proceeds smoothly, please follow these
	instructions:</p>
<ul style="font-family: sans-serif; font-size: 10pt; margin: 0 0 1.5em 0.5em;">

	<?php if ($site ->id != 5) {
		// City Road ?>

	<li><b>Please contact the Children's Ward as soon as possible on 0207
			566 2595 or 2596 to discuss pre-operative instructions</b></li>

			<?php	}	?>

	<li>Bring this letter with you on <?php echo date('d M Y', strtotime($operation->booking->session->date)) ?></li>
	<li>Please complete the attached in-patient questionnaire and bring it with you</li>

	<?php if ($site->id == 5) {
		// St Georges ?>

	<li>Please go directly to Duke Elder Ward on level 5 of the Lanesborough wing at the time of admission.</li>

	<?php } else { ?>

	<li>Please go directly to the Main Reception on level 5 of the RDCEC at the time of your child's admission.</li>

	<?php } ?>

</ul>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	If there has been any change in your child's general health, such as a
	cough or cold, any infection disease, or any other condition which
	might affect their fitness for operation, please telephone
	<?php if ($site->id == 5) {
		// St George's ?>

	020 8725 0060 and ask Naeela Butt for advice.

	<?php } else { ?>

	0207 566 2596 and ask to speak to a nurse for advice.

	<?php } ?>
</p>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	If you do not speak English, please arrange for an English speaking
	adult to stay with you until you reach the ward and have been seen by a
	Doctor.</p>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	It is very important that you let us know immediately if you are unable
	to keep this admission date.
	<?php if ($site->id == 5) {
		// St George's ?>

	Please let us know by return of post, or if necessary, telephone
	Admission Department on 020 7566 2258.

	<?php } else { ?>

	Please let us know by return of post, or if necessary, telephone 020
	8725 0060 and ask for Naeela Butt.

	<?php } ?>
</p>


<?php
	} // End Child
	else { 
		// Start Adult
		if ($operation->status == ElementOperation::STATUS_RESCHEDULED) { 
			// Adult Rescheduled ?>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	I am writing to inform you that the date for your eye operation has
	been changed from
	<?php echo date('d M Y', strtotime($cancelledBookings[0]->date)) ?>
	. The details now are:
</p>


<?php } else {
	// Adult Scheduled ?>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	On behalf of <?php echo $consultantName ?>, I am delighted to confirm the date of your operation. The details are:
</p>

<?php } ?>

<table
	style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	<tr>
		<td>Date of admission:</td>
		<td><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?>
		</td>
	</tr>
	<tr>
		<td>Time to arrive:</td>
		<td><?php echo $operation->booking->admission_time ?></td>
	</tr>
	<tr>
		<td>Date of surgery:</td>
		<td><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?>
		</td>
	</tr>
	<tr>
		<td>Ward:</td>
		<td><?php if ($specialty->id == 13) {
			// Refractive laser ?>
			Refractive waiting room - Cumberlidge Wing 4th Floor
		<?php } else { ?>
			<?php echo CHtml::encode($operation->booking->ward->name); ?>
		<?php } ?>
		</td>
	</tr>
</table>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	It is very important that you let us know immediately if you are unable
	to attend on this admission date.</p>

	<?php if ($site->id == 1 && $specialty->id != 13) {
		// City Road and not Refractive ?>
<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	You can do this by calling <?php echo $refuseContact ?>
</p>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	Please let us know if you have any change in your general health that
	may affect your surgery.</p>

	<?php } else { ?>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	Please let us know by return of post, or if necessary, telephone <?php echo $refuseContact ?>.
</p>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	If there has been any change in your general health, such as a cough or
	cold, any infection disease, or any other condition which might affect
	your fitness for operation, please telephone <?php echo $healthContact ?> for advice.
</p>

<?php } ?>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	If you do not speak English, please arrange for an English speaking
	adult to stay with you until you reach the ward and have been seen by a
	Doctor.</p>

<p style="font-family: sans-serif; font-size: 10pt; margin-bottom: 1em;">
	To ensure your admission proceeds smoothly, please follow these
	instructions:</p>

<ul style="font-family: sans-serif; font-size: 10pt; margin: 0 0 1.5em 0.5em;">
	<li>Bring this letter with you on <?php echo date('d M Y', strtotime($operation->booking->session->date)) ?>
	</li>
	<li>Please complete the attached in-patient questionnaire and bring it
		with you</li>
	<li>Please go directly to <?php if ($specialty->id == 13) {
		// Refractive laser ?> Refractive waiting room - Cumberlidge Wing 4th
		Floor <?php } else { ?> ward <?php echo CHtml::encode($operation->booking->ward->name) ?>
	<?php } ?></li>
	<li>You must not drive yourself to or from hospital</li>
	<li>We would like to request that only 1 person should accompany you in
		order to ensure that adequate seating area is available for patients
		coming for surgery.</li>
</ul>

	<?php } // End Adult ?>
<?php } ?>

<?php $this->renderPartial("eventTypeTemplates/view/25/letter_end"); ?>
