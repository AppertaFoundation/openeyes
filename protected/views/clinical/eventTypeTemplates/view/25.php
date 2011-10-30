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
?>

<?php if ($operation->status != $operation::STATUS_CANCELLED && $editable) {
?>
<!-- editable -->
<?php
	if (empty($operation->booking)) {
		$isAdmissionLetter = true;

		// The operation hasn't been booked yet?>
		<div style="margin-top:40px; text-align:center;">
			<button type="submit" value="submit" class="wBtn_print-invitation-letter ir" id="btn_print-invitation-letter">Print invitation letter</button>
			<button type="submit" value="submit" class="wBtn_print-reminder-letter ir" id="btn_print-reminder-letter">Print reminder letter</button>
			<button type="submit" value="submit" class="wBtn_print-gp-refer-back-letter ir" id="btn_print-gp-refer-back-letter">Print GP refer back letter</button>
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
	$patient = $event->episode->patient;
	$consultant = $event->episode->firm->getConsultant();

?>

	function loadBaseLetterPrintContent() {
		var baseContent = '<div id="letters"><div id="letterTemplate"><div id="l_address">';
		baseContent += '<table width="100%"><tr><td style="text-align:left;" width="50%"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></td><td style="text-align:right;"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td></tr>';
		baseContent += '<tr><td colspan="2" style="text-align:right;">';
		baseContent += '<?php

			foreach (array('name', 'address1', 'address2', 'address3', 'postcode') as $field) {
				if (!empty($site->$field)) {
					echo $site->$field . '<br />';
				}
			}

			echo '<br />Tel ' . $site->telephone . '<br />';
			echo 'Fax: ' . $site->fax . '</td></tr>';
		?>';

		baseContent += '<tr><td colspan="2" style="text-align:left;"><?php

			$patientName = $patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name;

			if ($patient->isChild()) {
				$patientName = 'Parent/Guardian of ' . $patientName;
			}

			echo $patientName;
		?>';

		baseContent += '<?php

			foreach (array('address1', 'address2', 'city', 'county', 'postcode') as $field) {
				if (!empty($patient->address->$field)) {
					echo $patient->address->$field . '<br />';
				}
			}

			echo $patient->address->country->name . '<br />';
		?></td></tr>';

		baseContent += '<tr><td colspan="2" style="text-align:right;"><?php echo date('F j Y') ?></td></tr></table></div>';



		baseContent += '<div id="l_content"><p><strong>Hospital number reference: <?php echo $patient->hos_num ?><?php
			if (!empty($patient->nhs_num)) {
				echo '<br />NHS number: ' . $patient->nhs_num . '</strong>';
			}
		?><p />';

		baseContent += '<p>Dear <?php echo $patientName ?>,</p>';

  		appendPrintContent(baseContent);
	}

	function loadEndLetterPrintContent() {
		appendPrintContent('<p>Yours sincerely,<br /><br /><br /><br /><br />Admissions Officer</p></div></div> <!-- #letterTemplate --></div> <!-- #letters -->');
	}

	$('#btn_print-invitation-letter').unbind('click').click(function() {
		alert('button clicked');

		clearPrintContent();
		loadBaseLetterPrintContent();

		var content = '<p>I have been asked to arrange your <?php
		if ($patient->isChild()) {
?>child&apos;s <?php
		}
?> admission for surgery under the care of <?php

		if (empty($consultant)) {
			echo 'CONSULTANT';
		} else {
			$contact = $consultant->contact;

			echo $contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name;
		} ?>.';

		content += ' This is currently anticipated to be a <?php
			if ($operation->overnight_stay) {
				echo 'an overnight stay';
			} else {
				echo 'day case';
			}
		?> procedure.</p>';

		content += '<p>Please will you telephone CONTACT within TIME LIMIT of the date of this letter to discuss and agree a convenient date for this operation. If there is no reply, please leave a message and contact number on the answer phone.</p>';

		content += '<p>Should you<?php
		if ($patient->isChild()) {
?>r child<?php
		}
?> no longer require treatment please let me know as soon as possible.</p>';

		appendPrintContent(content);

		loadEndLetterPrintContent();

		printContent();
	});
</script>
