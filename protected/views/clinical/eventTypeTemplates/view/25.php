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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

foreach ($elements as $element) {
	if (get_class($element) == 'ElementOperation') {
		$operation = $element;
		break;
	}
}

$scheduled = ($operation->status != $operation::STATUS_CANCELLED && $operation->status != $operation::STATUS_PENDING) ? 'Scheduled' : 'Unscheduled';
?>
<!-- Details -->
<h3>Operation (<?php echo $scheduled?>)</h3>
<h4>Diagnosis</h4>

<div class="eventHighlight">
	<h4><?php echo $operation->getEyeText()?> <?php echo $operation->getDisorder() ?></h4>
</div>

<h4>Operation</h4>
<div class="eventHighlight">
	<h4><?php
foreach ($elements as $element) {
	// Only display elements that have been completed, i.e. they have an event id
	if ($element->event_id) {
		$viewNumber = $element->viewNumber;

		if (get_class($element) == 'ElementOperation') {
			foreach ($element->procedures as $procedure) {
				echo "{$procedure->short_format} ({$procedure->default_duration} minutes)<br />";
			}
		}
	}
}
?></h4>
</div>

<?php if ($operation->status != $operation::STATUS_CANCELLED && $operation->status != $operation::STATUS_PENDING) {?>
	<div style="margin-top:40px; text-align:center;">
		<button type="submit" value="submit" class="btn_print-letter ir" id="btn_btn">Print letter</button>
		<button type="submit" value="submit" class="wBtn_reschedule-now ir" id="btn_btn">Reschedule now</button>
		<button type="submit" value="submit" class="wBtn_reschedule-later ir" id="btn_btn">Reschedule later</button>
		<button type="submit" value="submit" class="wBtn_cancel-operation ir">Cancel operation</button>	
	</div>
<?php }else{?>
	<div style="margin-top:40px; text-align:center;">
		<button type="submit" value="submit" class="wBtn_print-invitation-letter ir" id="btn_btn">Print invitation letter</button>
		<button type="submit" value="submit" class="wBtn_print-reminder-letter ir" id="btn_btn">Print reminder letter</button>
		<button type="submit" value="submit" class="wBtn_print-gp-refer-back-letter ir" id="btn_btn">Print GP refer back letter</button>
		<button type="submit" value="submit" class="wBtn_schedule-now ir" id="btn_btn">Schedule now</button>
		<button type="submit" value="submit" class="wBtn_cancel-operation ir">Cancel operation</button>	
	</div>
<?php }?>
<?php /*
<h4>Admission</h4>
<div class="eventHighlight">
	<h4>Wednesday 12/10/2011 at 10:30am. Come to Mackellar ward at 08:30am</h4>
</div>
*/?>
