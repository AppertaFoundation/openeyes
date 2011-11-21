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
?>
<script type="text/javascript">
	<?php if (isset($session)) {?>
		var header_text = "Operation: <?php echo date('d M Y', strtotime($session->date))?> (<?php echo $operation->event->user->first_name.' '.$operation->event->user->last_name?>)";
	<?php }else{?>
		var header_text = "Operation:";
	<?php }?>
</script>

<!-- Details -->
<h3>Operation (<?php echo $operation->getStatusText()?>)</h3>

<h4>User</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->event->user->username ?> on <?php echo date('d M Y', strtotime($operation->event->datetime)) ?> at <?php echo date('H:i', strtotime($operation->event->datetime)) ?></h4>
</div>

<h4>Diagnosis</h4>
<div class="eventHighlight">
	<?php $disorder = $operation->getDisorder(); ?>
	<h4><?php echo !empty($disorder) ? $operation->getEyeText() : 'Unknown' ?> <?php echo !empty($disorder) ? $operation->getDisorder() : 'Unknown' ?></h4>
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
				echo "{$procedure->term} ({$procedure->default_duration} minutes)<br />";
				$procedureList[] = $procedure->short_format;
			}
		}
	}
}
?></h4>
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

	echo $session->start_time . ' - ' .
		$session->end_time . ' ' .
		date('d M Y', strtotime($session->date)) . ', ' . $firmName
?></h4>
</div>

<h4>Admission Time</h4>
<div class="eventHighlight">
<h4><?php echo $operation->booking->admission_time ?></h4>
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
		echo ', cancelled on ' . date('d M Y', strtotime($cb->cancelled_date)) . ' by user ' . $cb->user->username . ' for reason: ' . $cb->cancelledReason->text . '<br />';
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
<?php
}

if ($operation->status != $operation::STATUS_CANCELLED && $editable) {
?>
<!-- editable -->
<?php
	if (empty($operation->booking)) {
		// The operation hasn't been booked yet?>
		<div style="margin-top:40px; text-align:center;">
			<button type="submit" value="submit" class="wBtn_print-invitation-letter ir" id="btn_print-invitation-letter">Print invitation letter</button>
			<button type="submit" value="submit" class="wBtn_print-reminder-letter ir" id="btn_print-reminder-letter">Print reminder letter</button>
			<!--button type="submit" value="submit" class="wBtn_print-gp-refer-back-letter ir" id="btn_print-gp-refer-back-letter">Print GP refer back letter</button-->
			<button type="submit" value="submit" class="wBtn_schedule-now ir" id="btn_schedule-now">Schedule now</button>
			<button type="submit" value="submit" class="wBtn_cancel-operation ir" id="btn_cancel-operation">Cancel operation</button>
		</div>
	<?php } else {?>
		<div style="margin-top:40px; text-align:center;">
			<button type="submit" value="submit" class="btn_print-letter ir" id="btn_print-letter">Print letter</button>
			<button type="submit" value="submit" class="wBtn_reschedule-now ir" id="btn_reschedule-now">Reschedule now</button>
			<button type="submit" value="submit" class="wBtn_reschedule-later ir" id="btn_reschedule-later">Reschedule later</button>
			<button type="submit" value="submit" class="wBtn_cancel-operation ir" id="btn_cancel-operation">Cancel operation</button>
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
	$patientDetails = '<br />';

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

	$patientName = CHtml::encode($patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name);

	if ($patient->isChild()) {
		$patientName = CHtml::encode('Parent/Guardian of ') . $patientName;
	}
?>

	function loadStartLetterPrintContent() {
		var baseContent = '<div id="letters" style="display:block; background:#000; font-family: sans-serif; font-size:10pt;"><div id="letterTemplate"><div id="l_address">';
		baseContent += '<table width="100%"><tr><td style="text-align:left;" width="50%"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></td><td style="text-align:right; font-family: sans-serif; font-size:10pt;"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td></tr>';
		baseContent += '<tr><td colspan="2" style="text-align:right; font-family: sans-serif; font-size:10pt;">';
		baseContent += '<?php

			foreach (array('name', 'address1', 'address2', 'address3', 'postcode') as $field) {
				if (!empty($site->$field)) {
					echo CHtml::encode($site->$field) . '<br />';
				}
			}

			echo '<br />Tel ' . CHtml::encode($site->telephone) . '<br />';
			echo 'Fax: ' . CHtml::encode($site->fax) . '</td></tr>';
		?>';

		baseContent += '<tr><td colspan="2" style="text-align:left; font-family: sans-serif; font-size:10pt;"><?php echo $patientName ?>';

		baseContent += '<?php echo $patientDetails ?></td></tr>';

		baseContent += '<tr><td colspan="2" style="text-align:right; font-family: sans-serif; font-size:10pt;"><?php echo date('d M Y') ?></td></tr></table></div>';



		baseContent += '<div id="l_content" style="font-family: sans-serif; font-size:10pt;"><p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;"><strong>Hospital number reference: <?php echo $patient->hos_num ?><?php
			if (!empty($patient->nhs_num)) {
				echo '<br />NHS number: ' . $patient->nhs_num . '</strong>';
			}
		?><p />';

		baseContent += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">Dear <?php echo $patientName ?>,</p>';

			appendPrintContent(baseContent);
	}

	function loadInvitationLetterPrintContent() {
<?php
	$changeContact = '';

				$serviceId = $event->episode->firm->serviceSpecialtyAssignment->service->id;
				$specialty = $event->episode->firm->serviceSpecialtyAssignment->specialty;

	// Generate contact name and telephone number
	if ($patient->isChild()) {
		if ($site->id == 1) { // City Road
			$changeContact = 'a nurse on 020 7566 2596';
		} else { // St. George's
			$changeContact = 'Naeela Butt on 020 8725 0060';
		}
	} else {
		switch ($site->id) {
			case 1:
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
				if ($serviceId == 4) { // Cataract
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
?>
		var content = '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">I have been asked to arrange your <?php
		if ($patient->isChild()) {
?>child&apos;s <?php
		}
?> admission for surgery under the care of <?php echo $consultantName ?>.';

		content += ' This is currently anticipated to be a <?php
			if ($operation->overnight_stay) {
				echo 'an overnight stay';
			} else {
				echo 'day case';
			}
		?> procedure.</p>';

		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">Please will you telephone <?php echo $changeContact ?> within 2 weeks of the date of this letter to discuss and agree a convenient date for this operation. If there is no reply, please leave a message and contact number on the answer phone.</p>';

		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">Should you<?php
		if ($patient->isChild()) {
?>r child<?php
		}
?> no longer require treatment please let me know as soon as possible.</p>';

		appendPrintContent(content);
	}

	function loadReminderLetterPrintContent() {
		var content = '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">I recently invited you to telephone to arrange a date for your <?php
		if ($patient->isChild()) {
?>child&apos;s <?php
		}
?> admission for surgery under the care of <?php echo $consultantName ?>. I have not yet heard from you.</p>';

		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">This is currently anticipated to be a <?php
			if ($operation->overnight_stay) {
				echo 'an overnight stay';
			} else {
				echo 'day case';
			}
		?> procedure.</p>';

		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">Please will you telephone <?php echo $changeContact ?> within 2 weeks of the date of this letter to discuss and agree a convenient date for this operation.</p>';

		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">Should you<?php
		if ($patient->isChild()) {
?>r child<?php
		}
?> no longer require treatment please let me know as soon as possible.</p>';

		appendPrintContent(content);
	}

	function loadScheduledLetterPrintContent() {
<?php
	if (!empty($operation->booking)) {

		// Get $refuseContact
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
																				case 14: // Strabismus
																								$refuseContact = 'Paediatrics and Strabismus Admission Coordinator on 020 7566 2258';
																								break;
					default:
						$refuseContact .= '020 7566 2206';
						break;
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
				if ($specialty->id == 7) { // Glaucoma
					$refuseContact = '020 7566 2020 and ask for Eileen Harper';
																	$healthContact = 'Eileen Harper on 020 7566 2020';
				} else {
					$refuseContact = '020 7566 2712 and ask for Linda Haslin';
																				$healthContact = 'Linda Haslin on 020 7566 2712';
				}
																break;
			case 7: // Potters Bar
				$refuseContact = '01707 646422 and ask for Potters Bar Admission Team';
																$healthContact = 'Potters Bar Admission Team on 01707 646422';
				break;
			case 9: // St Anns
				$refuseContact = '020 8211 8323 and ask for St Ann&apos;s Team';
																$healthContact = 'St Ann&apos;s Team on 020 8211 8323';
				break;
			case 5: // St George's
				$refuseContact = '020 8725 0060 and ask for Naeela Butt';
																$healthContact = 'Naeela Butt Team on 020 8725 0060';
			default:
				break;
		}
?>
		var content = '';

<?php
		$schedule = '<table style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;"><tr><td>Date of admission:</td><td>' . date('d M Y', strtotime($operation->booking->session->date)) . '</td></tr>';
		$schedule .= '<tr><td>Time to arrive:</td><td>' . $operation->booking->admission_time . '</td></tr>';
		$schedule .= '<tr><td>Date of surgery:</td><td>' . date('d M Y', strtotime($operation->booking->session->date)) . '</td></tr>';

		if ($patient->isChild()) {
			if ($operation->status == ElementOperation::STATUS_RESCHEDULED) {
?>
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">I am writing to inform you that the date for your child&apos;s eye operation has been changed from <?php echo date('d M Y', strtotime($cancelledBookings[0]->date)) ?>. The details now are:</p>';
<?php
			} else {
				if ($site->id == 5) { // St George's
?>
		 content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">On behalf of <?php echo $consultantName ?>, I am delighted to confirm the date you have agreed for your child&apos;s operation. The details are:</p>';
<?php
				} else { // City Road
?>
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">I am writing to confirm the date for your child&apos;s eye operation. The details are:</p>';
<?php
				}
			}
?>
		content += '<?php echo $schedule ?><tr><td>Location:</td><td><?php
			if ($site->id == 5) { // St George's
				echo 'St Georges Jungle Ward';
			} else { // City Road
				echo 'Richard Desmond&apos;s Children&apos;s Eye Centre (RDCEC)';
			}
		?></td></tr></table>';
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">To ensure your admission proceeds smoothly, please follow these instructions:<br />';
		content += '<ul style="font-family: sans-serif; font-size:10pt; margin:0 0 1.5em 0.5em;">';
<?php
			 if ($site->id != 5) { // City Road
?>
		content += '<li><b>Please contact the Children&apos;s Ward as soon as possible on 0207 566 2595 or 2596 to discuss pre-operative instructions</b></li>';
<?php
			}
?>
		content += '<li>Bring this letter with you on <?php echo date('d M Y', strtotime($operation->booking->session->date)) ?></li>';
		content += '<li>Please complete the attached in-patient questionnaire and bring it with you</li>';
<?php
			if ($site->id == 5) { // St Georges
?>
		content += '<li>Please go directly to Duke Elder Ward on level 5 of the Lanesborough wing at the time of admission.</li>';
<?php
			} else {
?>
		content += '<li>Please go directly to the Main Reception on level 5 of the RDCEC at the time of your child&apos;s admission.</li>';
<?php
			}
?>
		content += '</ul>';
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">If there has been any change in your child&apos;s general health, such as a cough or cold, any infection disease, or any other condition which might affect their fitness for operation, please telephone <?php
			if ($site->id == 5) { // St George's
				echo '020 8725 0060 and ask Naeela Butt for advice.';
			} else {
				echo '0207 566 2596 and ask to speak to a nurse';
			}
		?> for advice<p />';

		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">If you do not speak English, please arrange for an English speaking adult to stay with you until you reach the ward and have been seen by a Doctor.</p>';
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">It is very important that you let us know immediately if you are unable to keep this admission date. ';
<?php
			if ($site->id == 5) { // St George's
?>
		content += 'Please let us know by return of post, or if necessary, telephone Admission Department on 020 7566 2258.</p>';
<?php
			} else {
?>
		content += 'Please let us know by return of post, or if necessary, telephone 020 8725 0060 and ask for Naeela Butt.</p>';
<?php
			}
		} else {
			if ($operation->status == ElementOperation::STATUS_RESCHEDULED) {
?>
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">I am writing to inform you that the date for your eye operation has been changed from <?php echo date('d M Y', strtotime($cancelledBookings[0]->date)) ?>. The details now are:</p>';
<?php
			} else {
?>
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">On behalf of <?php echo $consultantName ?>, I am delighted to confirm the date of your operation. The details are:</p>';
<?php
			}
?>
		content += '<?php echo $schedule ?><tr><td>Ward:</td><td><?php
			if ($specialty->id == 13) { // Refractive laser
																echo 'Refractive waiting room - Cumberlidge Wing 4th Floor';
												} else {
																echo CHtml::encode($operation->booking->ward->name);
												}
								?></td></tr></table>';

		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">It is very important that you let us know immediately if you are unable to attend on this admission date. ';
<?php
			if ($site->id == 1 && $specialty->id != 13) { // City Road and not Refractive
?>
		content += 'You can do this by calling <?php echo $refuseContact ?><p />';
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">Please let us know if you have any change in your general health that may affect your surgery.</p>';
<?php
			} else {
?>
		content += 'Please let us know by return of post, or if necessary, telephone <?php echo $refuseContact ?>.<p />';
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">If there has been any change in your general health, such as a cough or cold, any infection disease, or any other condition which might affect your fitness for operation, please telephone <?php echo $healthContact ?> for advice.<p />';
<?php
			}
?>
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">If you do not speak English, please arrange for an English speaking adult to stay with you until you reach the ward and have been seen by a Doctor.</p>';
		content += '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">To ensure your admission proceeds smoothly, please follow these instructions:<br />';
		content += '<ul style="font-family: sans-serif; font-size:10pt; margin:0 0 1.5em 0.5em;"><li>Bring this letter with you on <?php echo date('d M Y', strtotime($operation->booking->session->date)) ?></li>';
		content += '<li>Please complete the attached in-patient questionnaire and bring it with you</li>';
		content += '<li>Please go directly to <?php
			if ($specialty->id == 13) { // Refractive laser
				echo 'Refractive waiting room - Cumberlidge Wing 4th Floor';
			} else {
				echo 'ward ' . CHtml::encode($operation->booking->ward->name);
			}
		?></li>';
		content += '<li>You must not drive yourself to or from hospital</li>';
		content == '<li>We would like to request that only 1 person should accompany you in order to ensure that adequate seating area is available for patients coming for surgery.</li>';
		content += '</ul>';
<?php
		}
	}
?>
		appendPrintContent(content);
	}

	function loadEndLetterPrintContent() {
		var content = '<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">Yours sincerely,<br /><br /><br />Admissions Officer</p></div></div> <!-- #letterTemplate --></div> <!-- #letters -->';
		content += '<div id="letterFooter" style="text-align:right; font-family: sans-serif; font-size:8pt;"><!--  letter footer -->Patron: Her Majesty The Queen<br />Chairman: Rudy Markham<br />Chief Executive: John Pelly<br /></div>';

		appendPrintContent(content);
	}


	function loadStartFormPrintContent() {
		var content = '<div style="page-break-after:always;"></div><div id="printForm" style="display:block; background:#000; font-size:7pt;"><div id="printFormTemplate">';

		content += '<table width="100%">';

		content += '<tr><td colspan="2" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td><td colspan="4" style="text-align:right; padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td></tr><tr><td colspan="2" width="50%" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;"> <!-- width control --><span class="title" style="font-size:13pt; font-weight: bold;">Admission Form</span></td>';
		content += '<td rowspan="4" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td>';
		content += '<td rowspan="4" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;"><?php echo $patientName ?><br /><?php echo $patientDetails ?></td></tr>';

		content += '<tr><td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">Hospital Number</td><td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;"><?php echo $patient->hos_num ?></td></tr>';
		content += '<tr><td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">DOB</td><td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;"><?php echo $patient->dob ?></td></tr>';
		content += '<tr><td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td><td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td></tr>';

		content += '</table>';

			appendPrintContent(content);

	}

	function loadMiddleFirmPrintContent() {

		var content = '<table width="100%">';

		content += '<tr><td width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Admitting Consultant:</strong></td> <!-- width control --><td width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo $consultantName ?></td>';
		content += '<td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Decision to admit date (or today&apos;s date):</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo date('d M Y', strtotime($operation->decision_date)) ?></td></tr>';

		content += '<tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">Service:</td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo CHtml::encode($event->episode->firm->serviceSpecialtyAssignment->specialty->name) ?></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">Telephone:</td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo CHtml::encode($patient->primary_phone) ?>&nbsp;</td></tr>';

		content += '<tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">Site:</td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo CHtml::encode($site->name) ?></td><td colspan="2" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">';
		content += '<table width="100%" class="subTableNoBorders" style="border:none;"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table></td></tr>';

		content += '<tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Person organising admission:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo $consultantName ?></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Dates patient unavailable:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td></tr>';

		content += '<tr><td colspan="2" style="border-bottom:1px dotted #000; border:1px solid #000; font-family: sans-serif; font-size:10pt;">Signature:</td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">Available at short notice:</td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td></tr>';

		content += '</table>';

		appendPrintContent(content);

		content = '<span class="subTitle" style="font-family: sans-serif; font-size:10pt;">ADMISSION DETAILS</span><table width="100%">';
		content += '<tr><td width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Urgency:</strong></td> <!-- width control --><td width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td>';
		content += '<td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Consultant to be present:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php
			if (empty($operation->consultant_required)) {
				echo 'No';
			} else {
				echo 'Yes';
			}
		?></td></tr>';

		content += '<tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Admission category:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php
												if ($operation->overnight_stay) {
																echo 'an overnight stay';
												} else {
																echo 'day case';
												} ?></td>';
<?php
												if (empty($operation->booking)) {
?>
		content += '<td colspan="2" rowspan="5" align="center" style="vertical-align:middle; font-family: sans-serif; font-size:10pt; border:1px solid #000; font-family: sans-serif; font-size:10pt;">';
		content += '<strong>Patient Added to Waiting List.<br />Admission Date to be arranged</strong></td></tr>';
<?php
						} else {
?>
		content += '<td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Operation date:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?></td></tr>';
<?php
						}
?>
		content += '<tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Diagnosis:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php

			$disorder = $operation->getDisorder();

			echo !empty($disorder) ? $operation->getEyeText() : 'Unknown';
			echo !empty($disorder) ? CHtml::encode($operation->getDisorder()) : ''
		?></td>';

<?php
												if (!empty($operation->booking)) {
?>
		content += '<td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Discussed with patient:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td>';
<?php
						}
?>
		content += '</tr><tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Intended procedure:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo CHtml::encode(implode(', ', $procedureList)) ?></td>';
<?php
												if (!empty($operation->booking)) {
?>
		content += '<td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Theatre session:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td>';
<?php
						}
?>
		content += '</tr><tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Eye:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo $operation->getEyeText() ?></td>';
<?php
												if (!empty($operation->booking)) {
?>
		content += '<td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Admission time:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo $operation->booking->admission_time ?></td>';
<?php
						}
?>
		content += '</tr><tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Total theatre time (mins):</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo CHtml::encode($operation->total_duration) ?></td>';
<?php
												if (!empty($operation->booking)) {
?>
		content += '<td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Proposed admission date:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?></td>';
<?php
						}
?>
		content += '</tr></table>';

		// Pre-op
		content += '<span class="subTitle" style="font-family: sans-serif; font-size:10pt;">PRE-OP ASSESSMENT INFORMATION</span><table width="100%">';
		content += '<tr><td width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Anaesthesia:</strong></td> <!-- width control --><td width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><?php echo $operation->getAnaestheticText() ?></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Likely to need anaesthetist review:</strong></td><td	width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td></tr>';

		content += '<tr><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Anaesthesia is:</strong></td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td><td style="border:1px solid #000; font-family: sans-serif; font-size:10pt;"><strong>Does the patient need to stop medication:</strong></td><td	width="25%" style="border:1px solid #000; font-family: sans-serif; font-size:10pt;">&nbsp;</td></tr>';

		content += '</table>';

		appendPrintContent(content);
	}

	function loadEndFormPrintContent() {
		var content = '<span class="subTitle" style="font-family: sans-serif; font-size:10pt;">COMMENTS</span>';

		content += '<table width="100%"><tr><td style="border:2px solid #666; height:7em; font-family: sans-serif; font-size:10pt;">&nbsp;</td></tr></table>';

		content += '</div> <!-- adminFormTemplate --></div> <!-- printForm -->';

			appendPrintContent(content);
	}

	$('#btn_print-invitation-letter').unbind('click').click(function() {

		clearPrintContent();

		loadStartLetterPrintContent();

		loadInvitationLetterPrintContent();

		loadEndLetterPrintContent();

		loadStartFormPrintContent();

		loadMiddleFirmPrintContent();

		loadEndFormPrintContent();

		printContent();
	});

	$('#btn_print-reminder-letter').unbind('click').click(function() {
		clearPrintContent();

		loadStartLetterPrintContent();

		loadReminderLetterPrintContent();

		loadEndLetterPrintContent();

		printContent();
	});

	$('#btn_print-letter').unbind('click').click(function() {
		clearPrintContent();

		loadStartLetterPrintContent();

		loadScheduledLetterPrintContent();

		loadEndLetterPrintContent();

								loadStartFormPrintContent();

								loadMiddleFirmPrintContent();

								loadEndFormPrintContent();

		printContent();
	});

</script>
