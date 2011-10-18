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

?>
<!-- Details -->
<h3>Operation (<?php echo $operation->getStatusText()?>)</h3>
<h4>Diagnosis</h4>

<div class="eventHighlight">
	<?php $disorder = $operation->getDisorder(); ?>
	<h4><?php echo !empty($disorder) ? $operation->getEyeText() : 'Unknown' ?> <?php echo !empty($disorder) ? $operation->getDisorder() : '' ?></h4>
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

<?php

if (!empty($operation->booking)) {
?>
<h4>Session</h4>
<div class="eventHighlight">
<?php $session = $operation->booking->session ?>
<h4><?php
	echo $session->start_time . ' - ' .
		$session->end_time . ' ' .
		date('jS F, Y', strtotime($session->date)) . ', ' .
		$session->sequence->sequenceFirmAssignment->firm->name . ' (' . 
		$session->sequence->sequenceFirmAssignment->firm->serviceSpecialtyAssignment->specialty->name . ')'
?></h4>
</div>
<?php
}
?>

<?php if ($operation->status != $operation::STATUS_CANCELLED && $editable) {
	if (empty($operation->booking)) {
		$isAdmissionLetter = true;

		// The operation hasn't been booked yet?>
		<div style="margin-top:40px; text-align:center;">
			<button type="submit" value="submit" class="wBtn_print-invitation-letter ir" id="btn_btn">Print invitation letter</button>
			<button type="submit" value="submit" class="wBtn_print-reminder-letter ir" id="btn_btn">Print reminder letter</button>
			<button type="submit" value="submit" class="wBtn_print-gp-refer-back-letter ir" id="btn_btn">Print GP refer back letter</button>
			<button type="submit" value="submit" class="wBtn_schedule-now ir" id="btn_schedule_now">Schedule now</button>
			<button type="submit" value="submit" class="wBtn_cancel-operation ir" id="btn_cancel_operation">Cancel operation</button> 
		</div>
	<?php } else {?>
		<button type="submit" value="submit" class="btn_print-letter ir" id="btn_btn">Print letter</button>
		<button type="submit" value="submit" class="wBtn_reschedule-now ir" id="btn_reschedule_now">Reschedule now</button>
		<button type="submit" value="submit" class="wBtn_reschedule-later ir" id="btn_reschedule_later">Reschedule later</button>
		<button type="submit" value="submit" class="wBtn_cancel-operation ir" id="btn_cancel_operation">Cancel operation</button>
	<?php }?>
<?php }?>
<script type="text/javascript">
	$('#btn_schedule_now').unbind('click').click(function() {
		$.ajax({
			url: '/booking/schedule',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				return false;
			}
		});
	});
	$('#btn_cancel_operation').unbind('click').click(function() {
		$.ajax({
			url: '/booking/cancelOperation',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				return false;
			}
		});
	});
	$('#btn_reschedule_now').unbind('click').click(function() {
		$.ajax({
			url: '/booking/reschedule',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				return false;
			}
		});
	});
	$('#btn_reschedule_later').unbind('click').click(function() {
		$.ajax({
			url: '/booking/rescheduleLater',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				return false;
			}
		});
	});
</script>
