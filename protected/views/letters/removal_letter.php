<?php $this->renderPartial("/letters/letter_start", array(
	'site' => $site,
	'patient' => $patient,
)); ?>

<p>
	I recently invited you to telephone to arrange a date for your admission for surgery under the care of
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
	Despite a reminder letter, I have not heard from you. I am therefore referring you back to your GP and have removed you from our waiting list.
</p>

<?php $this->renderPartial("/letters/letter_end"); ?>
