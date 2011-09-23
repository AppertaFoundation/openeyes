<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

if (empty($episodes)) {
	echo 'No episodes.';
} else {
	foreach ($episodes as $episode) { ?>
<div class="episode">
	<div class="title">
		<input type="hidden" name="episode-id" value="<?php echo $episode->id; ?>" />
		<span class="date"><?php echo date('d/m/y', strtotime($episode->start_date)); ?></span> - <?php
		echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name); ?></div>
	<ul class="events">
<?php
		foreach ($episode->events as $event) { ?>
		<li><?php
		$text = '<span class="type">' . ucfirst($event->eventType->name) .
			'</span><span class="date"> ' . date('d/m/Y', strtotime($event->datetime)) .
			'</span>';
		echo CHtml::link($text, array('clinical/view', 'id'=>$event->id));
		} ?>
	</ul>
	<div class="footer"></div>
</div>
<?php
	}
}