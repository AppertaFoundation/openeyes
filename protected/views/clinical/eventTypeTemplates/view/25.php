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

$cancelledBookings = $operation->getCancelledBookings();

if (!empty($operation->booking)) {
	$session = $operation->booking->session;
}

$status = ($operation->status == $operation::STATUS_CANCELLED) ? 'Cancelled' : 'Not scheduled';
?>
<span style="display: none;" id="header_text"><?php if (isset($session)) {?>Operation: <?php echo $session->NHSDate('date') ?>, <?php echo $operation->event->user->first_name.' '.$operation->event->user->last_name?><?php }else{?>Operation: <?php echo $status?>, <?php echo $operation->event->user->first_name.' '.$operation->event->user->last_name?><?php }?></span>

<!-- Details -->
<h3 class="withEventIcon" style="background:transparent url(/img/_elements/icons/event/medium/_blank.png) center left no-repeat;">Operation (<?php echo $operation->getStatusText()?>)</h3>

<h4>Created by</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->event->user->username ?> on <?php echo $operation->event->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($operation->event->created_date)) ?></h4>
</div>

<h4>Last modified by</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->event->usermodified->username?> on <?php echo $operation->event->NHSDate('last_modified_date') ?> at <?php echo date('H:i', strtotime($operation->event->last_modified_date)) ?></h4>
</div>

<h4>Diagnosis</h4>
<div class="eventHighlight">
	<?php $disorder = $operation->getDisorder(); ?>
	<h4><?php echo !empty($disorder) ? $operation->getDisorderEyeText() : 'Unknown' ?> <?php echo !empty($disorder) ? $operation->getDisorder() : 'Unknown' ?></h4>
</div>

<h4>Operation</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->getEyeText()?> 
<?php
foreach ($elements as $element) {
	// Only display elements that have been completed, i.e. they have an event id
	if ($element->event_id) {
		$viewNumber = $element->viewNumber;

		if (get_class($element) == 'ElementOperation') {
			$procedureList = array();
			foreach ($element->procedures as $procedure) {
				echo "{$procedure->term}<br />";
				$procedureList[] = $procedure->short_format;
			}
		}
	}
}
?></h4>
</div>

<h4>Anaesthetic</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->getAnaestheticText()?></h4>
</div>

<h4>Consultant required?</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->consultant_required ? 'Yes' : 'No'?></h4>
</div>

<h4>Post Operative Stay Required</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->overnight_stay ? 'Yes' : 'No'?></h4>
</div>

<h4>Decision Date</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->NHSDate('decision_date') ?></h4>
</div>

<h4>Operation priority</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->urgent ? 'Urgent' : 'Routine'?></h4>
</div>

<?php if (!empty($operation->comments)) {?>
<h4>Operation Comments</h4>
	<div class="eventHighlight">
		<h4><?php echo $operation->comments?></h4>
	</div>
<?php }?>

<?php

if (!empty($operation->booking)) {
?>
<h4>Session</h4>
<div class="eventHighlight">
<?php $session = $operation->booking->session ?>
<h4><?php
	if (empty($session->sequence->sequenceFirmAssignment)) {
		$firmName = 'Emergency List';
	} else {
		$firmName = $session->sequence->sequenceFirmAssignment->firm->name . ' (' .
		$session->sequence->sequenceFirmAssignment->firm->serviceSpecialtyAssignment->specialty->name . ')';
	}

	echo $session->NHSDate('date') . ' ' . substr($session->start_time,0,5) . ' - ' . substr($session->end_time,0,5) . ', '.$firmName;
?></h4>
</div>

<h4>Admission Time</h4>
<div class="eventHighlight">
<h4><?php echo substr($operation->booking->admission_time,0,5) ?></h4>
</div>
<?php
}

if (count($cancelledBookings)) {
?>
<h4>Cancelled Bookings</h4>
<div class="eventHighlight"><h4>
<?php
	foreach ($cancelledBookings as $cb) {
		echo 'Scheduled for ' . $cb->start_time . ' - ' . $cb->end_time . ', ' . $cb->NHSDate('date');
		echo ', ' . $cb->theatre->name . ' (' . $cb->theatre->site->name . ') ';
		echo ', cancelled on ' . $cb->NHSDate('cancelled_date') . ' by user ' . $cb->user->username . ' for reason: ' . $cb->cancelledReason->text;
		if ($cb->cancellation_comment) {
			echo ' ('.$cb->cancellation_comment.')';
		}
		echo '<br />';
	}
?>
</h4></div>
<?php
}

if ($operation->status == $operation::STATUS_CANCELLED && !empty($operation->cancellation)) {
$co = $operation->cancellation;
?>
<h4>Cancellation details</h4>
<div class="eventHighlight"><h4>
<?php
	echo 'Cancelled on ' . $co->NHSDate('cancelled_date') . ' by user ' . $co->user->username . ' for reason: ' . $co->cancelledReason->text . '<br />';
?>
</h4></div>
<?php if ($co->cancellation_comment) {?>
	<h4>Cancellation comments</h4>
	<div class="eventHighlight">
		<h4><?php echo str_replace("\n","<br/>",$co->cancellation_comment)?></h4>
	</div>
<?php }?>
<?php
}

if ($operation->status != $operation::STATUS_CANCELLED && $editable) {
?>
<!-- editable -->
<div style="margin-top:40px; text-align:center;">
	<?php
	if (empty($operation->booking)) {
	// The operation hasn't been booked yet
	$letterTypes = ElementOperation::getLetterOptions();
	$letterType = isset($letterTypes[$operation->getDueLetter()]) ? $letterTypes[$operation->getDueLetter()] : false;
	if($letterType) {
	?>
	<button type="submit" class="classy blue venti" value="submit" id="btn_print-invitation-letter"><span class="button-span button-span-blue">Print <?php echo $letterType ?> letter</span></button>
	<?php } ?>
	<button type="submit" class="classy green venti" value="submit" id="btn_schedule-now"><span class="button-span button-span-green">Schedule now</span></button>
	<?php } else {?>
	<button type="submit" class="classy blue venti" value="submit" id="btn_print-letter"><span class="button-span button-span-blue">Print letter</span></button>
	<button type="submit" class="classy green venti" value="submit" id="btn_reschedule-now"><span class="button-span button-span-green">Reschedule now</span></button>
	<button type="submit" class="classy green venti" value="submit" id="btn_reschedule-later"><span class="button-span button-span-green">Reschedule later</span></button>
	<?php }?>
	<button type="submit" class="classy red venti" value="submit" id="btn_cancel-operation"><span class="button-span button-span-red">Cancel operation</span></button>
</div>
<?php }?>

<script type="text/javascript">

	$('#btn_schedule-now').unbind('click').click(function() {
		$.ajax({
			url: '/booking/schedule',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				$('div.action_options').hide();
				return false;
			}
		});
	});

	$('#btn_cancel-operation').unbind('click').click(function() {
		$.ajax({
			url: '/booking/cancelOperation',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				$('div.action_options').hide();
				return false;
			}
		});
	});

	$('#btn_reschedule-now').unbind('click').click(function() {
		$.ajax({
			url: '/booking/reschedule',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				$('div.action_options').hide();
				return false;
			}
		});
	});

	$('#btn_reschedule-later').unbind('click').click(function() {
		$.ajax({
			url: '/booking/rescheduleLater',
			type: "GET",
			data: {'operation': <?php echo $operation->id?>},
			success: function(data) {
				$('#event_content').html(data);
				$('div.action_options').hide();
				return false;
			}
		});
	});

	$('#btn_print-invitation-letter').unbind('click').click(function() {
		printUrl('/waitingList/printletters?confirm=1&operations[]='+<?php echo $operation->id ?>);
	});

	$('#btn_print-letter').unbind('click').click(function() {
		clearPrintContent();
		appendPrintContent($('#printcontent_scheduledletter').html());
		appendPrintContent($('#printcontent_form').html());
		printContent();
	});

</script>
<div id="printcontent_scheduledletter" style="display: none;">
<?php
	// TODO: This needs moving to a controller so we can pull it in using an ajax call
	$event = Event::model()->findByPk($eventId);
	$consultant = $event->episode->firm->getConsultant();
	if (empty($consultant)) {
		$consultantName = 'CONSULTANT';
	} else {
		$consultantName = CHtml::encode($consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name);
	}
	$patient = $event->episode->patient;
	$specialty = $event->episode->firm->serviceSpecialtyAssignment->specialty;
	$scheduledContact = $operation->getScheduledContact();

	$this->renderPartial("/letters/admission_letter", array(
		'site' => $site,
		'patient' => $patient,
		'consultantName' => $consultantName,
		'operation' => $operation,
		'specialty' => $specialty,
		'refuseContact' => $scheduledContact['refuse'],
		'healthContact' => $scheduledContact['health'],
		'cancelledBookings' => $cancelledBookings,
	));
	$this->renderPartial("/letters/break");
	$this->renderPartial("/letters/form", array(
		'operation' => $operation, 
		'site' => $site,
		'patient' => $patient,
		'consultantName' => $consultantName,
	));
?>
</div>
