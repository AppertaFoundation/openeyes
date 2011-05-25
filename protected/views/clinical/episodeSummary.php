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

<?php

try {
	echo $this->renderPartial(
		'/clinical/EpisodeSummaries/' . $episode->firm->serviceSpecialtyAssignment->specialty_id,
		array('episode' => $episode)
	);
} catch (Exception $e) {
	// If there is no extra episode summary detail page for this specialty we don't care
}
