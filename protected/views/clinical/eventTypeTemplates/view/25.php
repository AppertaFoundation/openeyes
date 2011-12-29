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
<span style="display: none;" id="header_text"><?php if (isset($session)) {?>Operation: <?php echo date('j M Y', strtotime($session->date))?>, <?php echo $operation->event->user->first_name.' '.$operation->event->user->last_name?><?php }else{?>Operation: <?php echo $status?>, <?php echo $operation->event->user->first_name.' '.$operation->event->user->last_name?><?php }?></span>

<!-- Details -->
<h3 class="withEventIcon" style="background:transparent url(/img/_elements/icons/event/medium/_blank.png) center left no-repeat;">Operation (<?php echo $operation->getStatusText()?>)</h3>

<h4>Created by</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->event->user->username ?> on <?php echo date('d M Y', strtotime($operation->event->created_date)) ?> at <?php echo date('H:i', strtotime($operation->event->created_date)) ?></h4>
</div>

<h4>Last modified by</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->event->usermodified->username?> on <?php echo date('d M Y', strtotime($operation->event->last_modified_date))?> at <?php echo date('H:i', strtotime($operation->event->last_modified_date)) ?></h4>
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

<h4>Priority</h4>
<div class="eventHighlight">
	<h4><?php echo ($operation->urgent) ? 'Urgent' : 'Routine' ?></h4>
</div>

<h4>Anaesthetic</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->getAnaestheticText()?></h4>
</div>

<h4>Consultant</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->consultant_required ? 'Yes' : 'No'?></h4>
</div>

<h4>Post Operative Stay Required</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->overnight_stay ? 'Yes' : 'No'?></h4>
</div>

<h4>Decision Date</h4>
<div class="eventHighlight">
	<h4><?php echo date('d M Y',strtotime($operation->decision_date))?></h4>
</div>

<h4>Operation Type</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->urgent ? 'Urgent' : 'Routine'?></h4>
</div>

<h4>Operation Comments</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->comments ? $operation->comments : 'None'?></h4>
</div>

<?php if (!empty($operation->comments)) {?>
	<h4>Comments</h4>
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

	echo date('d M Y', strtotime($session->date)).' '.substr($session->start_time,0,5) . ' - ' . substr($session->end_time,0,5) . ', '.$firmName;
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
		echo 'Scheduled for ' . $cb->start_time . ' - ' . $cb->end_time . ', ' . date('d M Y', strtotime($cb->date));
		echo ', ' . $cb->theatre->name . ' (' . $cb->theatre->site->name . ') ';
		echo ', cancelled on ' . date('d M Y', strtotime($cb->cancelled_date)) . ' by user ' . $cb->user->username . ' for reason: ' . $cb->cancelledReason->text;
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
	echo 'Cancelled on ' . date('d M Y', strtotime($co->cancelled_date)) . ' by user ' . $co->user->username . ' for reason: ' . $co->cancelledReason->text . '<br />';
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
<?php
	if (empty($operation->booking)) {
		// The operation hasn't been booked yet?>
		<div style="margin-top:40px; text-align:center;">
			<button type="submit" class="classy blue venti" value="submit" id="btn_print-invitation-letter"><span class="button-span button-span-blue">Print invitation letter</span></button>
			<button type="submit" class="classy blue venti" value="submit" id="btn_print-reminder-letter"><span class="button-span button-span-blue">Print reminder letter</span></button>
			<button type="submit" class="classy green venti" value="submit" id="btn_schedule-now"><span class="button-span button-span-green">Schedule now</span></button>
			<button type="submit" class="classy red venti" value="submit" id="btn_cancel-operation"><span class="button-span button-span-red">Cancel operation</span></button>
		</div>
	<?php } else {?>
		<div style="margin-top:40px; text-align:center;">
			<button type="submit" class="classy blue venti" value="submit" id="btn_print-letter"><span class="button-span button-span-blue">Print letter</span></button>
			<button type="submit" class="classy green venti" value="submit" id="btn_reschedule-now"><span class="button-span button-span-green">Reschedule now</span></button>
			<button type="submit" class="classy green venti" value="submit" id="btn_reschedule-later"><span class="button-span button-span-green">Reschedule later</span></button>
			<button type="submit" class="classy red venti" value="submit" id="btn_cancel-operation"><span class="button-span button-span-red">Cancel operation</span></button>
		</div>
	<?php }?>
<?php }?>

<script type="text/javascript">

$('#btn_schedule-now').unbind('click').click(function() {
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

	$('#btn_cancel-operation').unbind('click').click(function() {
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

	$('#btn_reschedule-now').unbind('click').click(function() {
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

	$('#btn_reschedule-later').unbind('click').click(function() {
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

	$('#btn_print-invitation-letter').unbind('click').click(function() {
		clearPrintContent();
		appendPrintContent($('#printcontent_invitationletter').html());
		appendPrintContent($('#printcontent_form').html());
		printContent();
	});

	$('#btn_print-reminder-letter').unbind('click').click(function() {
		clearPrintContent();
		appendPrintContent($('#printcontent_reminderletter').html());
		printContent();
	});

	$('#btn_print-letter').unbind('click').click(function() {
		clearPrintContent();
		appendPrintContent($('#printcontent_scheduledletter').html());
		appendPrintContent($('#printcontent_form').html());
		printContent();
	});

</script>

<?php

	$event = Event::model()->findByPk($eventId);
	$consultant = $event->episode->firm->getConsultant();
	if (empty($consultant)) {
		$consultantName = 'CONSULTANT';
	} else {
		$contact = $consultant->contact;
		$consultantName = CHtml::encode($contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name);
	}

	$patient = $event->episode->patient;
	$patientDetails = '';

	// Here because of yii bug that fails to recognise address despite valid relationship and address_id
	$address = Address::model()->findByPk($patient->address_id);

	foreach (array('address1', 'address2', 'city', 'county', 'postcode') as $field) {
		if (!empty($address->$field)) {
			$patientDetails .= CHtml::encode($address->$field) . '<br />';
		}
	}

	if (!empty($address->country->name)) {
		$patientDetails .= CHtml::encode($address->country->name) . '<br />';
	}


	if ($patient->isChild()) {
		$salutation = CHtml::encode('Parent/Guardian of ' . $patient->first_name . ' ' . $patient->last_name);
	} else {
		$salutation = CHtml::encode($patient->title . ' ' . $patient->last_name);
	}
	$patientName = CHtml::encode($patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name);
	
	$serviceId = $event->episode->firm->serviceSpecialtyAssignment->service->id;
	$specialty = $event->episode->firm->serviceSpecialtyAssignment->specialty;
	
	// Generate change contact
	$changeContact = '';
	if ($patient->isChild()) {
		if ($site->id == 1) {
			// City Road
			$changeContact = 'a nurse on 020 7566 2596';
		} else {
			// St. George's
			$changeContact = 'Naeela Butt on 020 8725 0060';
		}
	} else {
		switch ($site->id) {
			case 1: // City Road
				switch ($serviceId) {
					case 2: // Adnexal
						$changeContact = 'Sarah Veerapatren on 020 7566 2206';
						break;
					case 4: // Cataract
						$changeContact = 'Ian Johnson on 020 7566 2006';
						break;
					case 5: // External Disease aka Corneal
						$changeContact = 'Ian Johnson on 020 7566 2006';
						break;
					case 6: // Glaucoma
						$changeContact = 'Joanna Kuzmidrowicz on 020 7566 2056';
						break;
					case 11: // Vitreoretinal
						$changeContact = 'Deidre Clarke on 020 7566 2004';
						break;
					default: // Medical Retinal, Paediatric, Strabismus
						$changeContact = 'Sherry Ramos on 0207 566 2258';
					break;
				}
				break;
			case 3: // Ealing
				$changeContact = 'Valerie Giddings on 020 8967 5648';
				break;
			case 4: // Northwick Park
				$changeContact = 'Saroj Mistry on 020 8869 3161';
				break;
			case 6: // Mile End
				if ($serviceId == 4) {
					// Cataract
					$changeContact = 'Linda Haslin on 020 7566 2712';
				} else {
					$changeContact = 'Eileen Harper on 020 7566 2020';
				}
				break;
			case 7: // Potters Bar
				$changeContact = 'Sue Harney on 020 7566 2339';
				break;
			case 9: // St Anns
				$changeContact = 'Veronica Brade on 020 7566 2843';
				break;
			default: // St George's
				$changeContact = 'Naeela Butt on 020 8725 0060';
			break;
		}
	}
	
	// Generate refuse and health contacts
	$refuseContact = '';
	$healthContact = '';
	switch ($site->id) {
		case 1: // City Road
			$refuseContact = $specialty->name . ' Admission Coordinator on ';
			switch ($specialty->id) {
				case 7: // Glaucoma
					$refuseContact .= '020 7566 2056';
					break;
				case 8: // Medical Retinal
					$refuseContact .= '020 7566 2258';
					break;
				case 11: // Paediatrics
					$refuseContact = 'Paediatrics and Strabismus Admission Coordinator on 020 7566 2258';
					break;
				case 13: // Refractive Laser
					$refuseContact = '020 7566 2205 and ask for Joyce Carmichael';
					$healthContact = '020 7253 3411 X4336 and ask Laser Nurse';
					break;
				case 14: // Strabismus
					$refuseContact = 'Paediatrics and Strabismus Admission Coordinator on 020 7566 2258';
					break;
				default:
					$refuseContact .= '020 7566 2206';
			}
			break;
		case 3: // Ealing
			$refuseContact = '020 8967 5766 and ask for Sister Kelly';
			$healthContact = 'Sister Kelly on 020 8967 5766';
			break;
		case 4: // Northwick Park
			$refuseContact = '020 8869 3161 and ask for Sister Titmus';
			$healthContact = 'Sister Titmus on 020 8869 3162';
		case 6: // Mile End
			switch ($specialty->id) {
				case 7:	// Glaucoma
					$refuseContact = '020 7566 2020 and ask for Eileen Harper';
					$healthContact = 'Eileen Harper on 020 7566 2020';
					break;
				default:
					$refuseContact = '020 7566 2712 and ask for Linda Haslin';
					$healthContact = 'Linda Haslin on 020 7566 2712';
			}
			break;
		case 7: // Potters Bar
			$refuseContact = '01707 646422 and ask for Potters Bar Admission Team';
			$healthContact = 'Potters Bar Admission Team on 01707 646422';
			break;
		case 9: // St Anns
			$refuseContact = '020 8211 8323 and ask for St Ann\'s Team';
			$healthContact = 'St Ann\'s Team on 020 8211 8323';
			break;
		case 5: // St George's
			$refuseContact = '020 8725 0060 and ask for Naeela Butt';
			$healthContact = 'Naeela Butt Team on 020 8725 0060';
			break;
	}

?>

<div id="printcontent_form" style="display: none;">
<?php $this->renderPartial("/clinical/eventTypeTemplates/view/25/form", array(
	'site' => $site,
	'patient' => $patient,
	'patientDetails' => $patientDetails,
	'patientName' => $patientName,
	'salutation' => $salutation,
	'consultantName' => $consultantName,
	'operation' => $operation, 
	'event' => $event,
	'procedureList' => $procedureList,
)); ?>
</div>
<div id="printcontent_invitationletter" style="display: none;">
<?php $this->renderPartial("/clinical/eventTypeTemplates/view/25/invitation_letter", array(
	'site' => $site,
	'patient' => $patient,
	'patientDetails' => $patientDetails,
	'patientName' => $patientName,
	'salutation' => $salutation,
	'consultantName' => $consultantName,
	'changeContact' => $changeContact,
	'operation' => $operation,
)); ?>
</div>
<div id="printcontent_reminderletter" style="display: none;">
<?php $this->renderPartial("/clinical/eventTypeTemplates/view/25/reminder_letter", array(
	'site' => $site,
	'patient' => $patient,
	'patientDetails' => $patientDetails,
	'patientName' => $patientName,
	'salutation' => $salutation,
	'consultantName' => $consultantName,
	'changeContact' => $changeContact,
	'operation' => $operation,
)); ?>
</div>
<div id="printcontent_scheduledletter" style="display: none;">
<?php $this->renderPartial("/clinical/eventTypeTemplates/view/25/scheduled_letter", array(
	'site' => $site,
	'patient' => $patient,
	'patientDetails' => $patientDetails,
	'patientName' => $patientName,
	'salutation' => $salutation,
	'consultantName' => $consultantName,
	'operation' => $operation,
	'specialty' => $specialty,
	'refuseContact' => $refuseContact,
	'healthContact' => $healthContact,
	'cancelledBookings' => $cancelledBookings,
)); ?>
</div>
