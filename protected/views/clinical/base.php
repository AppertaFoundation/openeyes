<?php
$this->breadcrumbs=array(
	'Clinical',
);

$this->menu=array(
);
?>

<?php

foreach ($this->eventTypes as $eventType) {
	echo CHtml::link(
                $eventType->name,
                Yii::app()->createUrl('clinical/create', array(
					'event_type_id' => $eventType->id
                ))
            );
	echo('&nbsp;');
}

?>
<br />
<br />
<?php

foreach ($this->episodes as $episode) {
	$episodeString = "episode: " . $episode->firm->serviceSpecialtyAssignment->specialty->name;

	// @todo - this shouldn't be here
	$firm = Firm::Model()->findByPk($this->selectedFirmId);
	if ($firm->serviceSpecialtyAssignment->specialty_id == $episode->firm->serviceSpecialtyAssignment->specialty_id) {
		echo('<b>' . $episodeString . '</b>');
	} else {
		echo($episodeString);
	}

	echo("<br />\n");

	foreach ($episode->events as $event) {
		echo("&nbsp;&nbsp;event: " . $event->datetime . "&nbsp;&nbsp;");

		echo CHtml::link(
                'view',
                Yii::app()->createUrl('clinical/view', array(
					'id' => $event->id
                ))
            );
		echo('&nbsp;');

		if ($this->firm->serviceSpecialtyAssignment->specialty_id == $event->episode->firm->serviceSpecialtyAssignment->specialty_id) {
			echo CHtml::link(
	                'update',
	                Yii::app()->createUrl('clinical/update', array(
						'id' => $event->id
	                ))
	            );
		}

		echo('<br />');
	}
}
