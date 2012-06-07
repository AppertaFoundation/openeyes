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

// Calculate next letter to be printed
$letterTypes = ElementOperation::getLetterOptions();
$letterType = ($operation->getDueLetter() !== null && isset($letterTypes[$operation->getDueLetter()])) ? $letterTypes[$operation->getDueLetter()] : false;
$has_gp = ($operation->getDueLetter() != ElementOperation::LETTER_GP || $operation->event->episode->patient->gp);
$patient = $this->event->episode->patient;
$has_address = (bool) $patient->correspondAddress;

if ($letterType == false && $operation->getLastLetter() == ElementOperation::LETTER_GP) {
	$letterType = 'Refer to GP';
}

?>
<span style="display: none;" id="header_text"><?php if (isset($session)) {?>Operation: <?php echo $session->NHSDate('date') ?>, <?php echo $operation->event->user->first_name.' '.$operation->event->user->last_name?><?php }else{?>Operation: <?php echo $status?>, <?php echo $operation->event->user->first_name.' '.$operation->event->user->last_name?><?php }?></span>

<h3 class="withEventIcon" style="background:transparent url(/img/_elements/icons/event/medium/treatment_operation.png) center left no-repeat;">Operation (<?php echo $operation->getStatusText()?>)</h3>

<?php $this->renderPartial('//base/_messages'); ?>

<?php if (!$has_gp) { ?>
<div class="alertBox">
	Patient has no GP, please correct in PAS before printing GP letter.
</div>
<?php } ?>
<?php if (!$has_address) { ?>
<div class="alertBox">
	Patient has no address, please correct in PAS before printing letter.
</div>
<?php } ?>

<?php if ($operation->event->hasIssue()) {?>
	<div class="issueBox">
		<?php echo $operation->event->getIssueText()?>
	</div>
<?php }?>

<!-- Details -->

<h4>Diagnosis</h4>
<div class="eventHighlight big">
	<?php $disorder = $operation->getDisorder(); ?>
	<h4><?php echo !empty($disorder) ? $operation->getDisorderEyeText() : 'Unknown' ?> <?php echo !empty($disorder) ? $operation->getDisorder() : 'Unknown' ?></h4>
</div>

<h4>Operation</h4>
<div class="eventHighlight priority">
	<h4><?php echo $operation->eye->name?> 
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

<div class="cols2 clearfix">

<div class="left">
<h4>Anaesthetic</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->anaesthetic_type->name?></h4>
</div>
</div>

<div class="right">
<h4>Consultant required?</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->consultant_required ? 'Yes Consultant' : 'No Consultant'?></h4>
</div>
</div>

<div class="left">
<h4>Post Operative Stay Required</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->overnight_stay ? 'Yes Stay' : 'No Stay'?></h4>
</div>
</div>

<div class="right">
<h4>Decision Date</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->NHSDate('decision_date') ?></h4>
</div>
</div>

<div class="left">
<h4>Operation priority</h4>
<div class="eventHighlight">
	<h4><?php echo $operation->priority->name?></h4>
</div>
</div>

<?php if (!empty($operation->comments)) { ?>
<div class="right">
<h4>Operation Comments</h4>
	<div class="eventHighlight comments">
		<h4><?php echo $operation->comments?></h4>
	</div>
</div>
<?php } ?>

</div>

<div class="metaData">
<span class="info">
Operation created by <span class="user"><?php echo $operation->event->user->fullname ?></span> on <?php echo $operation->event->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($operation->event->created_date)) ?>
</span>
<span class="info">
Operation last modified by <span class="user"><?php echo $operation->event->usermodified->fullname ?></span> on <?php echo $operation->event->NHSDate('last_modified_date') ?> at <?php echo date('H:i', strtotime($operation->event->last_modified_date)) ?>
</span>
</div>

<?php if (!empty($operation->booking)) { ?>
<!-- Booking -->
<h3 class="subsection">Booking Details</h3>

<div class="cols2">
<div class="left">
<h4>List</h4>
<div class="eventHighlight">
<?php $session = $operation->booking->session ?>
<h4><?php echo $session->NHSDate('date') . ' ' . $session->TimeSlot . ', '.$session->FirmName; ?></h4>
</div>
</div>

<div>
<h4>Theatre</h4>
<div class="eventHighlight">
<h4><?php echo $session->TheatreName ?></h4>
</div>
</div>

<div>
<h4>Admission Time</h4>
<div class="eventHighlight">
<h4><?php echo substr($operation->booking->admission_time,0,5) ?></h4>
</div>
</div>
</div>

<div class="metaData">
<span class="info">
Booking created by <span class="user"><?php echo $operation->booking->user->fullname ?></span> on <?php echo $operation->booking->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($operation->booking->created_date)) ?>
</span>
<span class="info">
Booking last modified by <span class="user"><?php echo $operation->booking->usermodified->fullname ?></span> on <?php echo $operation->booking->NHSDate('last_modified_date') ?> at <?php echo date('H:i', strtotime($operation->booking->last_modified_date)) ?>
</span>
</div>

<?php } ?>

<?php if (count($cancelledBookings)) { ?>
	<h3 class="subsection">Cancelled Bookings</h3>
	<ul class="eventComments">
		<?php foreach($cancelledBookings as $cb) { ?>
		<li>
			Originally scheduled for <strong><?php echo $cb->NHSDate('date'); ?>,
			<?php echo date('H:i',strtotime($cb->start_time)); ?> -
			<?php echo date('H:i',strtotime($cb->end_time)); ?></strong>,
			in <strong><?php echo $cb->theatre->NameWithSite; ?></strong>.
			Cancelled on <?php echo $cb->NHSDate('cancelled_date'); ?>
			by <strong><?php echo $cb->user->FullName; ?></strong>
			due to <?php echo $cb->ReasonWithComment; ?>
		</li>
		<?php } ?>
	</ul>
<?php } ?>

<?php if ($operation->status == $operation::STATUS_CANCELLED && !empty($operation->cancellation)) {
	$co = $operation->cancellation;
?>
<h3 class="subsection">Cancellation details</h3>
	<div class="eventHighlight">
		<h4>Cancelled on <?php echo $co->NHSDate('cancelled_date') . ' by user ' . $co->user->username . ' for reason: ' . $co->cancelledReason->text; ?>
		</h4>
	</div>

<?php if ($co->cancellation_comment) {?>
	<h4>Cancellation comments</h4>
	<div class="eventHighlight comments">
		<h4><?php echo str_replace("\n","<br/>",$co->cancellation_comment)?></h4>
	</div>
<?php } ?>

<?php } ?>

<?php if ($operation->status != $operation::STATUS_CANCELLED && $this->event->editable) { ?>
<!-- editable -->
<div style="margin-top:40px; text-align:center;">
	<?php
	if (empty($operation->booking)) {
	// The operation hasn't been booked yet
	if($letterType) {
		if($has_gp && $has_address) {
	?>
	<button type="submit" class="classy blue venti" value="submit" id="btn_print-invitation-letter"><span class="button-span button-span-blue">Print <?php echo $letterType ?> letter</span></button>
	<?php } else {
		// Patient has no GP defined or doesn't have an address ?>
	<button type="submit" class="classy disabled venti" value="submit" disabled="disabled"><span class="button-span">Print <?php echo $letterType ?> letter</span></button>
	<?php } } ?>
	<button type="submit" class="classy green venti" value="submit" id="btn_schedule-now"><span class="button-span button-span-green">Schedule now</span></button>
	<?php } else { // The operation has been booked ?>
	<?php if($has_address) { ?>
	<button type="submit" class="classy blue venti" value="submit" id="btn_print-letter"><span class="button-span">Print letter</span></button>
	<?php } else { ?>
	<button type="submit" class="classy disabled venti" value="submit" disabled="disabled"><span class="button-span button-span-blue">Print letter</span></button>
	<?php } ?>
	<button type="submit" class="classy green venti" value="submit" id="btn_reschedule-now"><span class="button-span button-span-green">Reschedule now</span></button>
	<button type="submit" class="classy green venti" value="submit" id="btn_reschedule-later"><span class="button-span button-span-green">Reschedule later</span></button>
	<?php } ?>
	<button type="submit" class="classy red venti" value="submit" id="btn_cancel-operation"><span class="button-span button-span-red">Cancel operation</span></button>
</div>
<?php } ?>

<script type="text/javascript">

	$('#btn_schedule-now').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			window.location.href = '/booking/schedule?operation=<?php echo $operation->id?>';
		}

		return false;
	});

	$('#btn_cancel-operation').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			window.location.href = '/booking/cancelOperation?operation=<?php echo $operation->id?>';
		}

		return false;
	});

	$('#btn_reschedule-now').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			window.location.href = '/booking/reschedule?operation=<?php echo $operation->id?>';
		}

		return false;
	});

	$('#btn_reschedule-later').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			window.location.href = '/booking/rescheduleLater?operation=<?php echo $operation->id?>';
		}

		return false;
	});

	$('#btn_print-invitation-letter').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			printUrl('/waitingList/printletters?confirm=1&operations[]='+<?php echo $operation->id ?>);
			enableButtons();
		}
	});

	$('#btn_print-letter').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			clearPrintContent();
			appendPrintContent($('#printcontent_admissionletter').html());
			printContent();
			enableButtons();
		}
	});
</script>
<?php if($operation->booking) { ?>
<div id="printcontent_admissionletter" style="display: none;">
<?php
	// TODO: This needs moving to a controller so we can pull it in using an ajax call
	// Only render the letter if the patient has an address
	if($has_address) {
		$admissionContact = $operation->getAdmissionContact();
		$site = $operation->booking->session->theatre->site;
		$firm = $operation->booking->session->firm;
		$emergency_list = false;
		if(!$firm) {
			$firm = $operation->event->episode->firm;
			$emergency_list = true;
		}
		$this->renderPartial("/letters/admission_letter", array(
			'site' => $site,
			'patient' => $patient,
			'firm' => $firm,
			'emergencyList' => $emergency_list,
			'operation' => $operation,
			'refuseContact' => $admissionContact['refuse'],
			'healthContact' => $admissionContact['health'],
			'cancelledBookings' => $cancelledBookings,
		));
		$this->renderPartial("/letters/break");
		$this->renderPartial("/letters/admission_form", array(
			'operation' => $operation, 
			'site' => $site,
			'patient' => $patient,
			'firm' => $firm,
			'emergencyList' => $emergency_list,
		));
	}
?>
</div>
<?php } ?>
