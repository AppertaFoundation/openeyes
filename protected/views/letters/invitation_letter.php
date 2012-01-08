<?php $this->renderPartial("/letters/letter_start", array(
	'site' => $site,
	'patient' => $patient,
)); ?>

<p>
	I have been asked to arrange your <?php	if ($patient->isChild()) { ?>child's <?php } ?> admission for surgery under the care of
	<?php echo $consultantName ?>. This is currently anticipated to be
	<?php
	if ($operation->overnight_stay) {
		echo 'an overnight stay';
	} else {
		echo 'a day case';
	}
	?>
	procedure.
</p>

<p>
	Please will you telephone <?php echo $changeContact ?> within 2 weeks of the date of this letter to discuss and agree a
	convenient date for this operation. If there is no reply, please leave a message and contact number on the answer phone.
</p>

<p>
	Should you<?php if ($patient->isChild()) { ?>r child<?php	}	?> no longer require treatment please let me know as soon as possible.
</p>

<?php $this->renderPartial("/letters/letter_end"); ?>
