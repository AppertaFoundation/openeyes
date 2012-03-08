<div class="banner" style="font-size: 14pt;">
	<div class="seal"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></div>
	<div class="logo"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></div>
</div>
<div class="fromAddress" style="font-size: 14pt;">
	<?php echo $site->letterhtml ?>
	<br />Tel: <?php echo CHtml::encode($site->telephone) ?>
	<?php if($site->fax) { ?>
	<br />Fax: <?php echo CHtml::encode($site->fax) ?>
	<?php } ?>
</div>
<div class="toAddress" style="font-size: 14pt;">
	<?php $gp = $patient->gp ?>
	<?php echo $gp->contact->fullname ?>
	<br /><?php echo $gp->contact->correspondAddress->letterhtml ?>
</div>
<div class="date" style="font-size: 14pt;">
	<?php echo date(Helper::NHS_DATE_FORMAT) ?>
</div>
<div class="content" style="font-size: 14pt;">

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
