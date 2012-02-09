<?php $this->renderPartial("/letters/letter_start", array(
	'site' => $site,
	'patient' => $patient,
)); ?>

<p>
	I recently invited you to telephone to arrange a date for your <?php if ($patient->isChild()) { ?>child's <?php } ?>
	admission for surgery under the care of
	<?php 
		if($consultant = $firm->getConsultant()) {
			$consultantName = $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
		} else {
			$consultantName = 'CONSULTANT';
		}
	?>
	<?php echo CHtml::encode($consultantName) ?>.
	I have not yet heard from you.
</p>

<p>
	This is currently anticipated to be a
	<?php
	if ($operation->overnight_stay) {
		echo 'an overnight stay';
	} else {
		echo 'day case';
	}
	?>
	procedure.
</p>

<p>
	Please will you telephone <?php echo $changeContact ?> within 2 weeks of the date of this letter to discuss and agree
	a convenient date for this operation.
</p>

<p>
	Should you<?php	if ($patient->isChild()) { ?>r child<?php } ?> no longer require treatment please let me know as soon as possible.
</p>

<?php $this->renderPartial("/letters/letter_end"); ?>
