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

?>
<!-- Details -->
<h3>Operation (<?php echo $operation->getStatusText()?>)</h3>

<h4>Diagnosis</h4>
<div class="eventHighlight">
	<?php $disorder = $operation->getDisorder(); ?>
	<h4><?php echo !empty($disorder) ? $operation->getDisorder() : 'Unknown' ?></h4>
</div>

<h4>Diagnosis Eye</h4>
<div class="eventHighlight">
        <h4><?php echo !empty($disorder) ? $operation->getEyeText() : 'Unknown' ?></h4>
</div>

<h4>Operation</h4>
<div class="eventHighlight">
	<h4><?php
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


<h4>Operation Eye</h4>
<div class="eventHighlight">
        <h4><?php echo $operation->getEyeText(); ?></h4>
</div>

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
		date('jS F, Y', strtotime($session->date)) . ', ' . $firmName
?></h4>
</div>
<?php
}

if (count($cancelledBookings)) {
?>
<h4>Cancelled Bookings</h4>
<div class="eventHighlight"><h4>
<?php
	foreach ($cancelledBookings as $cb) {
		echo 'Scheduled for ' . $cb->start_time . ' - ' . $cb->end_time . ', ' . $cb->date;
		echo ', ' . $cb->theatre->name . ' (' . $cb->theatre->site->name . ') ';
		echo ', cancelled on ' . $cb->cancelled_date . ' by user ' . $cb->user->username . ' for reason: ' . $cb->cancelledReason->text . '<br />';
	}
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

		$consultantName = htmlspecialchars($contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name, ENT_QUOTES);
	}

	$patient = $event->episode->patient;
	$patientDetails = '';
	foreach (array('address1', 'address2', 'city', 'county', 'postcode') as $field) {
		if (!empty($patient->address->$field)) {
			$patientDetails .= htmlspecialchars($patient->address->$field, ENT_QUOTES) . '<br />';
		}
	}

	if (!empty($patient->address->country->name)) {
		$patientDetails .= htmlspecialchars($patient->address->country->name, ENT_QUOTES) . '<br />';
	}

	$patientName = htmlspecialchars($patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name, ENT_QUOTES);

	if ($patient->isChild()) {
		$patientName = htmlspecialchars('Parent/Guardian of ', ENT_QUOTES) . $patientName;
	}
?>

	function loadStartLetterPrintContent() {
		var baseContent = '<div id="letters"><div id="letterTemplate"><div id="l_address">';
		baseContent += '<table width="100%"><tr><td style="text-align:left;" width="50%"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></td><td style="text-align:right;"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td></tr>';
		baseContent += '<tr><td colspan="2" style="text-align:right;">';
		baseContent += '<?php

			foreach (array('name', 'address1', 'address2', 'address3', 'postcode') as $field) {
				if (!empty($site->$field)) {
					echo htmlspecialchars($site->$field, ENT_QUOTES) . '<br />';
				}
			}

			echo '<br />Tel ' . htmlspecialchars($site->telephone, ENT_QUOTES) . '<br />';
			echo 'Fax: ' . htmlspecialchars($site->fax, ENT_QUOTES) . '</td></tr>';
		?>';

		baseContent += '<tr><td colspan="2" style="text-align:left;"><?php echo $patientName ?>';

		baseContent += '<?php echo $patientDetails ?></td></tr>';

		baseContent += '<tr><td colspan="2" style="text-align:right;"><?php echo date('d/m/Y') ?></td></tr></table></div>';



		baseContent += '<div id="l_content"><p><strong>Hospital number reference: <?php echo $patient->hos_num ?><?php
			if (!empty($patient->nhs_num)) {
				echo '<br />NHS number: ' . $patient->nhs_num . '</strong>';
			}
		?><p />';

		baseContent += '<p>Dear <?php echo $patientName ?>,</p>';

  		appendPrintContent(baseContent);
	}

	function loadInvitationLetterPrintContent() {
<?php
	$changeContact = '';

	// Generate contact name and telephone number
	if ($patient->isChild()) {
		if ($site->id == 1) { // City Road
			$changeContact = 'a nurse on 020 7566 2596';
		} else { // St. George's
			$changeContact = 'Naeela Butt on 020 8725 0060';
		}	
	} else {
		$serviceId = $event->episode->firm->serviceSpecialtyAssignment->service->id;
		$specialty = $event->episode->firm->serviceSpecialtyAssignment->specialty;

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
		var content = '<p>I have been asked to arrange your <?php
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

		content += '<p>Please will you telephone <?php echo $changeContact ?> within 2 weeks of the date of this letter to discuss and agree a convenient date for this operation. If there is no reply, please leave a message and contact number on the answer phone.</p>';

		content += '<p>Should you<?php
		if ($patient->isChild()) {
?>r child<?php
		}
?> no longer require treatment please let me know as soon as possible.</p>';

		appendPrintContent(content);
	}

	function loadReminderLetterPrintContent() {
		var content = '<p>I recently invited you to telephone to arrange a date for your <?php
		if ($patient->isChild()) {
?>child&apos;s <?php
		}
?> admission for surgery under the care of <?php echo $consultantName ?>. I have not yet heard from you.</p>';

		content += '<p>This is currently anticipated to be a <?php
			if ($operation->overnight_stay) {
				echo 'an overnight stay';
			} else {
				echo 'day case';
			}
		?> procedure.</p>';

		content += '<p>Please will you telephone <?php echo $changeContact ?> within 2 weeks of the date of this letter to discuss and agree a convenient date for this operation.</p>';

		content += '<p>Should you<?php
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
		$schedule = '<table><tr><td>Date of admission:</td><td>' . date('l F j, Y', strtotime($operation->booking->session->date)) . '</td></tr>';
		$schedule .= '<tr><td>Time to arrive:</td><td>' . $operation->booking->admission_time . '</td></tr>';
		$schedule .= '<tr><td>Date of surgery:</td><td>' . date('l F j, Y', strtotime($operation->booking->session->date)) . '</td></tr>';

		if ($patient->isChild()) {
			if ($operation->status == ElementOperation::STATUS_RESCHEDULED) {
?>
		content += '<p>I am writing to inform you that the date for your child&apos;s eye operation has been changed from <?php echo $cancelledBookings[0]->date ?>. The details now are:</p>';
<?php
			} else {
				if ($site->id == 5) { // St George's
?>
		 content += '<p>On behalf of <?php echo $consultantName ?>, I am delighted to confirm the date you have agreed for your child&apos;s operation. The details are:</p>';
<?php
				} else { // City Road
?>
		content += '<p>I am writing to confirm the date for your child&apos;s eye operation. The details are:</p>';
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
		content += '<p>To ensure your admission proceeds smoothly, please follow these instructions:<br />';
<?php
			 if ($site->id != 5) { // City Road
?>
		content += '<ul><li><b>Please contact the Children&apos;s Ward as soon as possible on 0207 566 2595 or 2596 to discuss pre-operative instructions</b></li>';
<?php
			}
?>
		content += '<li>Bring this letter with you on <?php echo date('l F j, Y', strtotime($operation->booking->session->date)) ?></li>';
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
		content += '<li>
		content += '</ul>';
		content += '<p>If there has been any change in your child&apos;s general health, such as a cough or cold, any infection disease, or any other condition which might affect their fitness for operation, please telephone <?php
			if ($site->id == 5) { // St George's
				echo '020 8725 0060 and ask Naeela Butt for advice.';
			} else {
				echo '0207 566 2596 and ask to speak to a nurse';
			}
		?> for advice<p />';

		content += '<p>If you do not speak English, please arrange for an English speaking adult to stay with you until you reach the ward and have been seen by a Doctor.</p>';
		content += '<p>It is very important that you let us know immediately if you are unable to keep this admission date. ';
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
		content += '<p>I am writing to inform you that the date for your eye operation has been changed from <?php echo $cancelledBookings[0]->date ?>. The details now are:</p>';
<?php
			} else {
?>
		content += '<p>On behalf of <?php echo $consultantName ?>, I am delighted to confirm the date of your operation. The details are:</p>';
<?php
			}
?>
		content += '<?php echo $schedule ?><tr><td>Ward:</td><td><?php 
			if ($specialty->id == 13) { // Refractive laser
                                echo 'Refractive waiting room - Cumberlidge Wing 4th Floor';
                        } else {    
                                echo htmlspecialchars($operation->booking->ward->name, ENT_QUOTES);
                        }           
                ?></td></tr></table>';

		content += '<p>It is very important that you let us know immediately if you are unable to attend on this admission date. ';
<?php
			if ($site->id == 1 && $specialty->id != 13) { // City Road and not Refractive
?>
		content += 'You can do this by calling <?php echo $refuseContact ?><p />';
		content += '<p>Please let us know if you have any change in your general health that may affect your surgery.</p>';
<?php
			} else {
?>
		contact += 'Please let us know by return of post, or if necessary, telephone <?php echo $refuseContact ?>.<p />';
		content += '<p>If there has been any change in your general health, such as a cough or cold, any infection disease, or any other condition which might affect your fitness for operation, please telephone <?php echo $healthContact ?> for advice.<p />';
<?php
			}
?>
		content += '<p>If you do not speak English, please arrange for an English speaking adult to stay with you until you reach the ward and have been seen by a Doctor.</p>';
		content += '<p>To ensure your admission proceeds smoothly, please follow these instructions:<br />';
		content += '<ul><li>Bring this letter with you on <?php echo date('l F j, Y', strtotime($operation->booking->session->date)) ?></li>';
		content += '<li>Please complete the attached in-patient questionnaire and bring it with you</li>';
		content += '<li>Please go directly to <?php 
			if ($specialty->id == 13) { // Refractive laser
				echo 'Refractive waiting room - Cumberlidge Wing 4th Floor';
			} else {
				echo 'ward ' . htmlspecialchars($operation->booking->ward->name, ENT_QUOTES);
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
		var content = '<p>Yours sincerely,<br /><br /><br /><br /><br />Admissions Officer</p></div></div> <!-- #letterTemplate --></div> <!-- #letters -->';
		content += '<div id="letterFooter"><!--  letter footer -->Patron: Her Majesty The Queen<br />Chairman: Rudy Markham<br />Chief Executive: John Pelly<br /></div>';

		appendPrintContent(content);
	}


	function loadStartFormPrintContent() {
		var content = '<div id="printForm"><div id="printFormTemplate">';

		content += '<table width="100%">';

		content += '<tr><td colspan="2" style="border:none;">&nbsp;</td><td colspan="4" style="text-align:right; border:none;"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td></tr><tr><td colspan="2" width="50%"> <!-- width control --><span class="title">Admission Form</span></td>';
		content += '<td rowspan="4">Patient Name,<br />Address<br />Address<br /></td>';
		content += '<td rowspan="4"><?php echo $patientName ?><br /><?php echo $patientDetails ?></td></tr>';

		content += '<tr><td>Hospital Number</td><td><?php echo $patient->hos_num ?></td></tr>';
		content += '<tr><td>DOB</td><td><?php echo $patient->dob ?></td></tr>';
		content += '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';

		content += '</table>';

  		appendPrintContent(content);

	}

	function loadMiddleFirmPrintContent() {

		var content = '<table width="100%">';

		content += '<tr><td width="25%"><strong>Admitting Consultant:</strong></td> <!-- width control --><td width="25%"><?php echo $consultantName ?></td>';
		content += '<td><strong>Decision to admit date (or today&apos;s date):</strong></td><td><?php echo $operation->decision_date ?></td></tr>';

		content += '<tr><td>Service:</td><td><?php echo htmlspecialchars($event->episode->firm->serviceSpecialtyAssignment->specialty->name, ENT_QUOTES) ?></td><td>Telephone:</td><td><?php echo htmlspecialchars($patient->primary_phone) ?></td></tr>';

		content += '<tr><td>Site:</td><td><?php echo htmlspecialchars($site->name, ENT_QUOTES) ?></td><td colspan="2">';
		content += '<table width="100%" class="subTableNoBorders"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table></td></tr>';

		content += '<tr><td><strong>Person organising admission:</strong></td><td><?php echo $consultantName ?></td><td><strong>Dates patient unavailable:</strong></td><td>&nbsp;</td></tr>';

		content += '<tr><td colspan="2" style="border-bottom:1px dotted #000;">Signature:</td><td>Available at short notice:</td><td>&nbsp;</td></tr>';

		content += '</table>';

		appendPrintContent(content);

		content = '<span class="subTitle">ADMISSION DETAILS</span><table width="100%">';
		content += '<tr><td width="25%"><strong>Urgency:</strong></td> <!-- width control --><td width="25%">&nbsp;</td>';
		content += '<td><strong>Consultant to be present:</strong></td><td><?php
			if (empty($operation->consultant_required)) {
				echo 'No';
			} else {
				echo 'Yes';
			}
		?></td></tr>';

		content += '<tr><td>Admission category:</td><td><?php
                        if ($operation->overnight_stay) {
                                echo 'an overnight stay';
                        } else {
                                echo 'day case';
                        } ?></td><td colspan="2" rowspan="5" align="center" style="vertical-align:middle;">';
		content += '<strong>Patient Added to Waiting List.<br />Admission Date to be arranged</strong></td></tr>';

		content += '<tr><td><strong>Diagnosis:</strong></td><td><?php

			$disorder = $operation->getDisorder();

			echo !empty($disorder) ? $operation->getEyeText() : 'Unknown';
			echo !empty($disorder) ? htmlspecialchars($operation->getDisorder(), ENT_QUOTES) : ''
		?></td></tr>';

		content += '<tr><td><strong>Intended procedure:</strong></td><td><?php echo htmlspecialchars(implode(', ', $procedureList), ENT_QUOTES) ?></td></tr>';

		content += '<tr><td><strong>Eye:</strong></td><td><?php echo $operation->getEyeText() ?></td></tr>';

		content += '<tr><td><strong>Total theatre time (mins):</strong></td><td><?php echo htmlspecialchars($operation->total_duration, ENT_QUOTES) ?></td></tr>';

		content += '</table>';

		// Pre-op
		content += '<span class="subTitle">PRE-OP ASSESSMENT INFORMATION</span><table width="100%">';
		content += '<tr><td width="25%"><strong>Anaesthesia:</strong></td> <!-- width control --><td width="25%">&nbsp;</td><td><strong>Likely to need anaesthetist review:</strong></td><td>&nbsp;</td></tr>';

		content += '<tr><td><strong>Anaesthesia is:</strong></td><td>&nbsp;</td><td><strong>Does the patient need to stop medication:</strong></td><td>&nbsp;</td></tr>';

		content += '</table>';

		appendPrintContent(content);
	}

	function loadEndFormPrintContent() {
		var content = '<span class="subTitle">COMMENTS</span>';

		content += '<table width="100%"><tr><td style="border:2px solid #666; height:7em;">&nbsp;</td></tr></table>';

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

		loadEndFormPrintContent()

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

		printContent();
	});

</script>
