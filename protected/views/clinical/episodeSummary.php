<?php

$this->renderPartial('base');

?>
<br />

Start date: <?php echo $episode->start_date ?>
<br />
End date: <?php echo $episode->end_date ?>
(still open)

Specialty: <?php echo $episode->firm->serviceSpecialtyAssignment->specialty->name ?>
<br />

Consultant firm: <?php echo $episode->firm->name ?>
<br />

Principal eye: ? Where will this be fetched from ?
<br />

Principal diagnosis: ? Where will this be fetched from ?

<br />
VIEW SUMMARIES:
<br />

<?php

foreach ($summaries as $summary) {
	echo CHtml::link(
		'View summary \'' . $summary->name . '\'',
		Yii::app()->createUrl('clinical/summary', array(
			'id' => $episode->id,
			'summary_id' => $summary->id,
		))
	);

	echo('&nbsp;');
}