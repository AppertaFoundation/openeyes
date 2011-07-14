<?php

$this->renderPartial('base');

?>
<br />

Start date: <?php echo $episode->start_date ?>
<br />
End date: <?php 

if (empty($episode->end_date)) {
	echo 'still open';
} else {
	echo $episode->end_date;
}

?>
<br />

Specialty: <?php echo $episode->firm->serviceSpecialtyAssignment->specialty->name ?>
<br />

Consultant firm: <?php echo $episode->firm->name ?>
<br />

<?php

$diagnosis = $episode->getPrincipalDiagnosis();

if (empty($diagnosis)) {
	$eye = 'No diagnosis';
	$text = 'No diagnosis';
} else {
	$eye = $diagnosis->getEyeText();
	$text = $diagnosis->disorder->term . ' - ' . $diagnosis->disorder->fully_specified_name;
}

?>

Principal eye: <?php echo $eye ?>
<br />

Principal diagnosis: <?php echo $text ?>

<br />

<?php

try {
	echo $this->renderPartial(
		'/clinical/episodeSummaries/' . $episode->firm->serviceSpecialtyAssignment->specialty_id,
		array('episode' => $episode)
	);
} catch (Exception $e) {
	// If there is no extra episode summary detail page for this specialty we don't care
}
