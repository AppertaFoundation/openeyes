<?php
/**
 * OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/
?>
<h4 class="elementTypeName">Procedure<?php if (count($element->procedures) != 1) echo 's'?></h4>

<div class="eventHighlight priority">
	<h4><?php echo $element->eye->adjective?>
		<?php foreach ($element->procedures as $procedure) {
			echo "{$procedure->term}<br />";
		}
	?></h4>
</div>

<div class="cols2 clearfix">
	<div class="left">
		<h4>Anaesthetic</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->anaesthetic_type->name?></h4>
		</div>
	</div>

	<div class="right">
		<h4>Consultant required?</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->consultant_required ? 'Yes Consultant' : 'No Consultant'?></h4>
		</div>
	</div>

	<div class="left">
		<h4>Post Operative Stay Required</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->overnight_stay ? 'Yes Stay' : 'No Stay'?></h4>
		</div>
	</div>

	<div class="right">
		<h4>Decision Date</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->NHSDate('decision_date') ?></h4>
		</div>
	</div>

	<div class="left">
		<h4>Operation priority</h4>
		<div class="eventHighlight">
			<h4><?php echo $element->priority->name?></h4>
		</div>
	</div>

	<?php if (!empty($element->comments)) { ?>
	<div class="right">
		<h4>Operation Comments</h4>
		<div class="eventHighlight comments">
			<h4><?php echo CHtml::encode($element->comments)?></h4>
		</div>
	</div>
	<?php } ?>
</div>

<?php if ($element->booking) {?>
	<h3 class="subsection">Booking Details</h3>

	<div class="cols2">
		<div class="left">
			<h4>List</h4>
			<div class="eventHighlight">
				<?php $session = $element->booking->session ?>
				<h4 style="width: 460px;"><?php echo $session->NHSDate('date') . ' ' . $session->TimeSlot . ', '.$session->FirmName; ?></h4>
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
				<h4><?php echo substr($element->booking->admission_time,0,5) ?></h4>
			</div>
		</div>
	</div>

	<div class="metaData">
		<span class="info">
		Booking created by <span class="user"><?php echo $element->booking->user->fullname ?></span> on <?php echo $element->booking->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($element->booking->created_date)) ?>
		</span>
		<span class="info">
		Booking last modified by <span class="user"><?php echo $element->booking->usermodified->fullname ?></span> on <?php echo $element->booking->NHSDate('last_modified_date') ?> at <?php echo date('H:i', strtotime($element->booking->last_modified_date)) ?>
		</span>
	</div>
<?php } ?>

<?php if (count($element->cancelledBookings)) { ?>
	<h3 class="subsection">Cancelled Bookings</h3>
	<ul class="eventComments">
		<?php foreach ($element->cancelledBookings as $booking) { ?>
		<li>
			Originally scheduled for <strong><?php echo $booking->NHSDate('session_date'); ?>,
			<?php echo date('H:i',strtotime($booking->session_start_time)); ?> -
			<?php echo date('H:i',strtotime($booking->session_end_time)); ?></strong>,
			in <strong><?php echo $booking->theatre->nameWithSite; ?></strong>.
			Cancelled on <?php echo $booking->NHSDate('booking_cancellation_date'); ?>
			by <strong><?php echo $booking->usercancelled->FullName; ?></strong>
			due to <?php echo $booking->cancellationReasonWithComment; ?>
		</li>
		<?php }?>
	</ul>
<?php }?>

<?php if ($element->status->name == 'Cancelled' && $element->operation_cancellation_date) {?>
	<h3 class="subsection">Cancellation details</h3>
		<div class="eventHighlight">
			<h4>Cancelled on <?php echo $element->NHSDate('operation_cancellation_date') . ' by user ' . $element->cancellation_user->username . ' for reason: ' . $element->cancellation_reason->text?>
			</h4>
		</div>

	<?php if ($element->cancellation_comment) {?>
		<h4>Cancellation comments</h4>
		<div class="eventHighlight comments">
			<h4><?php echo str_replace("\n","<br/>",$element->cancellation_comment)?></h4>
		</div>
	<?php } ?>
<?php } ?>

<?php if ($element->erod) {?>
	<div>
		<h3 class="subsection">Earliest reasonable offer date</h3>
		<div class="eventHighlight">
			<h4><?php echo $element->erod->NHSDate('session_date').' '.$element->erod->timeSlot.', '.$element->erod->FirmName?></h4>
		</div>
	</div>
<?php }?>

<?php
if ($element->status->name != 'Cancelled' && $this->event->editable) {
	if (empty($element->booking)) {
		if ($element->letterType && $this->canPrint()) {
			$print_letter_options = null;
			if (!$element->has_gp || !$element->has_address) {
				$print_letter_options['disabled'] = true;
			}
			if (BaseController::checkUserLevel(3)) {
				$this->event_actions[] = EventAction::button("Print ".$element->letterType." letter", 'print-letter', $print_letter_options, array('id' => 'btn_print-letter'));
			}
		}
		if (BaseController::checkUserLevel(4)) {
			$this->event_actions[] = EventAction::link("Schedule now",
				Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/schedule/'.$element->event_id),
				array('colour' => 'green'),
				array('id' => 'btn_schedule-now'));
		}
	} else {
		if ($this->canPrint()) {
			$print_letter_options = null;
			if (!$element->has_address) {
				$print_letter_options['disabled'] = true;
			}
			if (BaseController::checkUserLevel(3)) {
				$this->event_actions[] = EventAction::button("Print letter", 'print-letter', $print_letter_options, array('id' => 'btn_print-admissionletter'));
			}
		}
		if (BaseController::checkUserLevel(4) && $element->status->name != 'Completed') {
			$this->event_actions[] = EventAction::link("Reschedule now",
				Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/reschedule/'.$element->event_id),
				array('colour' => 'green'),
				array('id' => 'btn_reschedule-now'));
			$this->event_actions[] = EventAction::link("Reschedule later",
				Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/rescheduleLater/'.$element->event_id),
				array('colour' => 'green'),
				array('id' => 'btn_reschedule-later'));
		}
	}
}
if (BaseController::checkUserLevel(4) && $element->status->name != 'Cancelled' && $element->status->name != 'Completed' && $element->event->episode->editable) {
	$this->event_actions[] = EventAction::link("Cancel operation",
		Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/default/cancel/'.$element->event_id),
		array('colour' => 'red'),
		array('id' => 'btn_cancel-operation'));
}
?>
