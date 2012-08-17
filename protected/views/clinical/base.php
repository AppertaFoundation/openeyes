<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$this->breadcrumbs=array(
	'Clinical',
);

$this->menu=array(
);
?>

<?php

foreach ($this->eventTypes as $eventType) {
	echo CHtml::link($eventType->name, Yii::app()->createUrl('clinical/create', array('event_type_id' => $eventType->id))).'&nbsp';
}

?>
<br />
<br />
<?php

foreach ($this->episodes as $episode) {
	$episodeString = "episode: " . $episode->firm->serviceSubspecialtyAssignment->subspecialty->name;

	if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id == $episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
		$episodeString = '<b>' . $episodeString . '</b>';
	}

	echo CHtml::link($episodeString, Yii::app()->createUrl('clinical/episodeSummary', array('id' => $episode->id)))."<br/>\n";

	foreach ($episode->events as $event) {
		echo("&nbsp;&nbsp;event: " . $event->datetime . "&nbsp;&nbsp;");
		echo CHtml::link('view', Yii::app()->createUrl('clinical/view', array('id' => $event->id))).'&nbsp;';

		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id == $event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			echo CHtml::link('update', Yii::app()->createUrl('clinical/update', array('id' => $event->id)));
		}

		echo '<br />';
	}
}
