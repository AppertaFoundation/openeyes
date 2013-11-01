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
<section class="element element-data">
	<h3 class="data-title">Procedure<?php if (count($element->procedures) != 1) echo 's'?></h3>
	<ul class="data-value highlight important">
		<?php foreach ($element->procedures as $procedure) {
			echo "<li>{$element->eye->adjective} {$procedure->term}</li>";
		}?>
	</ul>
</section>

<section class="element element-data">
	<div class="row">
		<div class="large-6 column">
			<h3 class="data-title">Anaesthetic</h3>
			<div class="data-value">
				<?php echo $element->anaesthetic_type->name?>
			</div>
		</div>
		<div class="large-6 column">
			<h3 class="data-title">Consultant required?</h3>
			<div class="data-value"><?php echo $element->consultant_required ? 'Yes Consultant' : 'No Consultant'?></div>
		</div>
	</div>
</section>

<section class="element element-data">
	<div class="row">
		<div class="large-6 column">
			<h3 class="data-title">Post Operative Stay Required</h3>
			<div class="data-value"><?php echo $element->overnight_stay ? 'Yes Stay' : 'No Stay'?></div>
		</div>
		<div class="large-6 column">
			<h3 class="data-title">Decision Date</h3>
			<div class="data-value"><?php echo $element->NHSDate('decision_date') ?></div>
		</div>
	</div>
</section>

<section class="element element-data">
	<div class="row">
		<div class="large-6 column">
			<h3 class="data-title">Operation priority</h3>
			<div class="data-value"><?php echo $element->priority->name?>
			</div>
		</div>
		<div class="large-6 column">
			<?php if (!empty($element->comments)) { ?>
				<h3 class="data-title">Operation Comments</h3>
				<div class="data-value panel comments"><?php echo CHtml::encode($element->comments)?></div>
			<?php } ?>
		</div>
	</div>
</section>

<?php if ($element->booking) {?>
	<section class="element">
		<h3 class="element-title highlight">Booking Details</h3>
		<div class="element-data">
			<div class="row">
				<div class="large-6 column">
					<h3 class="data-title">List</h3>
					<div class="data-value">
						<?php $session = $element->booking->session ?>
						<?php echo $session->NHSDate('date') . ' ' . $session->TimeSlot . ', '.$session->FirmName; ?>
					</div>
				</div>
				<div class="large-6 column">
					<h3 class="data-title">Theatre</h3>
					<div class="data-value"><?php echo $session->TheatreName ?></div>
				</div>
			</div>
		</div>
	</section>

	<section class="element element-data">
		<div class="row">
			<div class="large-6 column">
				<h3 class="data-title">Admission Time</h3>
				<div class="data-value">
					<?php echo substr($element->booking->admission_time,0,5) ?>
				</div>
			</div>
		</div>
	</section>

	<div class="row">
		<div class="large-12 column">
			<div class="metadata">
				<span class="info">
					Booking created by
					<span class="user"><?php echo $element->booking->user->fullname ?></span>
					on <?php echo $element->booking->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($element->booking->created_date)) ?>
				</span>
				<span class="info">
					Booking last modified by <span class="user"><?php echo $element->booking->usermodified->fullname ?></span>
					on <?php echo $element->booking->NHSDate('last_modified_date') ?> at <?php echo date('H:i', strtotime($element->booking->last_modified_date)) ?>
				</span>
			</div>
		</div>
	</div>
<?php } ?>

<?php if (count($element->cancelledBookings)) { ?>
	<section class="element">
		<h3 class="element-title highlight">Cancelled Bookings</h3>
		<div class="element-data">
			<ul class="cancelled-bookings">
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
		</div>
	</section>
<?php }?>

<?php if ($element->status->name == 'Cancelled' && $element->operation_cancellation_date){?>
	<section class="element">
		<h3 class="element-title highlight">Cancellation details</h3>
		<div class="element-data">
			<div class="row">
				<div class="large-6 column">
					<div class="data-value">
						Cancelled on
						<?php echo $element->NHSDate('operation_cancellation_date') . ' by user ' . $element->cancellation_user->username . ' for reason: ' . $element->cancellation_reason->text?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php if ($element->cancellation_comment) {?>
		<section class="element element-data">
			<div class="row">
				<div class="large-6 column">
					<h3 class="data-title">Cancellation comments</h3>
					<div class="data-value panel comments">
						<?php echo str_replace("\n","<br/>",$element->cancellation_comment)?>
					</div>
				</div>
			</div>
		</section>
	<?php } ?>
<?php } ?>

<?php if ($element->erod) {?>
	<section class="element">
		<h3 class="element-title highlight">Earliest reasonable offer date</h3>
		<div class="element-data">
			<div class="row">
				<div class="large-12 column">
					<div class="data-value">
						<?php echo $element->erod->NHSDate('session_date').' '.$element->erod->timeSlot.', '.$element->erod->FirmName?>
					</div>
				</div>
			</div>
		</div>
	</section>
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
				$this->event_actions[] = EventAction::button("Print ".$element->letterType." letter", 'print-letter', $print_letter_options, array('id' => 'btn_print-letter', 'class'=>'button small'));
			}
		}
		if (BaseController::checkUserLevel(4)) {
			$this->event_actions[] = EventAction::link("Schedule now",
				Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/schedule/'.$element->event_id),
				array('level'=>'secondary'),
				array('id' => 'btn_schedule-now', 'class'=>'button small'));
		}
	} else {
		if ($this->canPrint()) {
			$print_letter_options = null;
			if (!$element->has_address) {
				$print_letter_options['disabled'] = true;
			}
			if (BaseController::checkUserLevel(3)) {
				$this->event_actions[] = EventAction::button("Print letter", 'print-letter', $print_letter_options, array('id' => 'btn_print-admissionletter','class'=>'small button'));
			}
		}
		if (BaseController::checkUserLevel(4) && $element->status->name != 'Completed') {
			$this->event_actions[] = EventAction::link("Reschedule now",
				Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/reschedule/'.$element->event_id),
				array('level' => 'secondary'),
				array('id' => 'btn_reschedule-now','class' => 'button small'));
			$this->event_actions[] = EventAction::link("Reschedule later",
				Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/booking/rescheduleLater/'.$element->event_id),
				array('level' => 'secondary'),
				array('id' => 'btn_reschedule-later','class' => 'button small'));
		}
	}
}
if (BaseController::checkUserLevel(4) && $element->status->name != 'Cancelled' && $element->status->name != 'Completed' && $element->event->episode->editable) {
	$this->event_actions[] = EventAction::link("Cancel operation",
		Yii::app()->createUrl('/'.$element->event->eventType->class_name.'/default/cancel/'.$element->event_id),
		array(),
		array('id' => 'btn_cancel-operation', 'class'=>'warning button small'));
}
?>
