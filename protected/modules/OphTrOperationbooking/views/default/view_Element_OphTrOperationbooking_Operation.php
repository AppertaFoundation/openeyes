<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<section class="element element-data">
    <h3 class="data-title">Procedure<?php if (count($element->procedures) != 1) echo 's' ?></h3>
    <ul class="data-value highlight important">
        <?php foreach ($element->procedures as $procedure) {
            echo "<li>{$element->eye->adjective} {$procedure->term}</li>";
        } ?>
    </ul>
</section>

<section class="element element-data">
    <div class="row">
        <div class="large-6 column">
            <h3 class="data-title">Consultant required?</h3>
            <?php
            if ($element->consultant) {
                $consultant_name = $element->consultant->ReversedFullName;
            } else {
                $consultant_name = 'Consultant';
            }
            ?>
            <div class="data-value"><?php echo $element->consultant_required ? "Yes, $consultant_name" : 'No Consultant' ?></div>
        </div>
        <?php if (!is_null($element->senior_fellow_to_do)): ?>
            <div class="large-6 column">
                <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('senior_fellow_to_do')) ?></h3>
                <div class="data-value"><?= $element->senior_fellow_to_do ? 'Yes' : 'No' ?></div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="large-6 column">
            <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('any_grade_of_doctor')) ?>?</h3>
            <div class="data-value"><?php echo $element->any_grade_of_doctor ? 'Yes' : 'No' ?></div>
        </div>
    </div>
    <div class="row">
        <div class="large-6 column">
            <h3 class="data-title">Anaesthetic</h3>
            <div class="data-value">
                <?= $element->getAnaestheticTypeDisplay(); ?>
            </div>
        </div>
        <div class="large-6 column">
            <?php if (!is_null($element->anaesthetist_preop_assessment)): ?>
                <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('anaesthetist_preop_assessment')) ?></h3>
                <div class="data-value"><?= $element->anaesthetist_preop_assessment ? 'Yes' : 'No' ?></div>
            <?php endif ?>
        </div>
    </div>
    <div class="row">
        <div class="large-6 column">
            <?php if ($element->anaesthetic_choice): ?>
                <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('anaesthetic_choice_id')) ?></h3>
                <div class="data-value"><?= $element->anaesthetic_choice->name ?></div>
            <?php endif ?>
        </div>
        <div class="large-6 column">
            <?php if (!is_null($element->stop_medication)): ?>
                <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('stop_medication')) ?></h3>
                <div class="data-value"><?= $element->stop_medication ? 'Yes' : 'No' ?></div>
                <?php if ($element->stop_medication): ?>
                    <div class="data-value panel comments"><?= Yii::app()->format->nText($element->stop_medication_details) ?></div>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
</section>

<section class="element element-data">
    <div class="row">
        <div class="large-6 column">
            <h3 class="data-title">Post Operative Stay Required</h3>
            <div class="data-value"><?php echo $element->overnight_stay ? 'Yes Stay' : 'No Stay' ?></div>
        </div>
        <div class="large-6 column">
            <h3 class="data-title">Decision Date</h3>
            <div class="data-value"><?php echo $element->NHSDate('decision_date') ?></div>
        </div>
    </div>
    <div class="row">
        <div class="large-6 column">
            <h3 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('site_id')) ?></h3>
            <div class="data-value"><?php echo $element->site->name ?></div>
        </div>
    </div>
    <div class="row">
        <div class="large-6 column">
            <?php if (!is_null($element->fast_track)): ?>
                <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('fast_track')) ?></h3>
                <div class="data-value"><?php echo $element->fast_track ? 'Yes' : 'No' ?></div>
            <?php endif ?>
        </div>
        <div class="large-6 column">
            <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('fast_track_discussed_with_patient')) ?></h3>
			<div class="data-value"><?php echo is_null($element->fast_track_discussed_with_patient) ? 'Not recorded' : ($element->fast_track_discussed_with_patient ? 'Yes' : 'No')?></div>
        </div>
    </div>

</section>

<section class="element element-data">
    <div class="row">
        <div class="large-6 column">
            <h3 class="data-title">Operation priority</h3>
            <div class="data-value"><?php echo $element->priority->name ?>
            </div>
        </div>
        <div class="large-6 column">
            <?php if (!empty($element->comments)) { ?>
                <h3 class="data-title">Operation Comments</h3>
                <div class="data-value panel comments"><?php echo $element->textWithLineBreaks('comments') ?></div>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="large-6 column">
            <h3 class="data-title">Admission category:</h3>
            <div class="data-value"><?php echo ($element->overnight_stay) ? 'An overnight stay' : 'Day case'?>
            </div>
        </div>
        <div class="large-6 column">
            <h3 class="data-title">Total theatre time (mins):</h3>
            <div class="data-value"><?php echo CHtml::encode($element->total_duration)?></div>
        </div>
    </div>
    <div class="row">
        <div class="large-6 column end">
            <?php if (!is_null($element->special_equipment)): ?>
                <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('special_equipment')) ?></h3>
                <div class="data-value"><?= $element->special_equipment ? 'Yes' : 'No' ?></div>
                <?php if ($element->special_equipment): ?>
                    <div class="data-value panel comments"><?= Yii::app()->format->nText($element->special_equipment_details) ?></div>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
</section>


<section class="element element-data">
    <div class="row">
        <?php
        if (Yii::app()->params['ophtroperationbooking_referral_link']) {
            ?>
            <div class="large-6 column">
                <h3 class="data-title">Referral</h3>
                <div class="data-value">
                    <?php if ($element->referral) {
                        echo $element->referral->getDescription();
                    } else {
                        echo 'No Referral Set';
                    } ?>
                </div>
                <?php if ($rtt = $element->getRTT()) { ?>
                    <div class="rtt-info">Clock Start - <?= Helper::convertDate2NHS($rtt->clock_start) ?> Breach: <?= Helper::convertDate2NHS($rtt->breach) ?></div>
                <?php } ?>
            </div>

            <?php
        }
        ?>
        <div class="large-6 column">
            <?php if (!empty($element->comments_rtt)) { ?>
                <h3 class="data-title">Operation RTT Comments</h3>
                <div class="data-value panel comments"><?php echo $element->textWithLineBreaks('comments_rtt') ?></div>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="large-6 column">
            <?php if ($element->organising_admission_user): ?>
                <h3 class="data-title"><?= CHtml::encode($element->getAttributeLabel('organising_admission_user_id')) ?></h3>
                <div class="data-value"><?= $element->organising_admission_user->getReversedFullName() ?></div>
            <?php endif ?>
        </div>
    </div>
</section>

<?php if ($element->booking && !$this->module->isTheatreDiaryDisabled()) { ?>
    <section class="element">
        <h3 class="element-title highlight">Booking Details</h3>
        <div class="element-data">
            <div class="row">
                <div class="large-6 column">
                    <h3 class="data-title">List</h3>
                    <div class="data-value">
                        <?php $session = $element->booking->session ?>
                        <?php echo $session->NHSDate('date') . ' ' . $session->TimeSlot . ', ' . $session->FirmName; ?>
                        <?php if ($warnings = $session->getWarnings()) { ?>
                            <div class="alert-box alert with-icon">Please note:
                                <ul>
                                    <?php foreach ($warnings as $warning) {
                                        echo '<li>' . $warning . '</li>';
                                    } ?>
                                </ul>
                            </div>
                        <?php } ?>
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
                    <?php echo substr($element->booking->admission_time, 0, 5) ?>
                </div>
            </div>
        </div>
    </section>

    <?php if ($element->booking->erod) { ?>
        <section class="element">
            <h3 class="element-title highlight">Earliest reasonable offer date</h3>
            <div class="element-data">
                <div class="row">
                    <div class="large-12 column">
                        <div class="data-value">
                            <?php echo $element->booking->erod->getDescription() ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>


    <div class="row">
        <div class="large-12 column">
            <div class="metadata">
				<span class="info">
					Operation scheduling created by
					<span class="user"><?php echo $element->booking->user->fullname ?></span>
					on <?php echo $element->booking->NHSDate('created_date') ?> at <?php echo date('H:i', strtotime($element->booking->created_date)) ?>
				</span>
                <span class="info">
					Operation scheduling last modified by <span class="user"><?php echo $element->booking->usermodified->fullname ?></span>
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
                            <?php echo date('H:i', strtotime($booking->session_start_time)); ?> -
                            <?php echo date('H:i', strtotime($booking->session_end_time)); ?></strong>,
                        in <strong><?php echo $booking->theatre->nameWithSite; ?></strong>.
                        Cancelled on <?php echo $booking->NHSDate('booking_cancellation_date'); ?>
                        by <strong><?php echo $booking->usercancelled->FullName; ?></strong>
                        due to <?php echo CHtml::encode($booking->cancellationReasonWithComment) ?>
                        <?php if ($booking->erod) { ?>
                            <br/><span class="erod">EROD was <?= $booking->erod->getDescription() ?></span>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </section>
<?php } ?>

<?php if ($element->status->name === 'Cancelled' && $element->operation_cancellation_date) { ?>
    <section class="element">
        <h3 class="element-title highlight">Cancellation details</h3>
        <div class="element-data">
            <div class="row">
                <div class="large-6 column">
                    <div class="data-value">
                        Cancelled on
                        <?php
                        echo $element->NHSDate('operation_cancellation_date') . ' by user ' . $element->cancellation_user->username .
                            ' for reason: ' . CHtml::encode($element->cancellation_reason->text);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($element->cancellation_comment) { ?>
        <section class="element element-data">
            <div class="row">
                <div class="large-6 column">
                    <h3 class="data-title">Cancellation comments</h3>
                    <div class="data-value panel comments">
                        <?php echo CHtml::encode($element->cancellation_comment) ?>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
<?php } ?>

<?php
$this->event_actions[] = EventAction::link(
    'Display Whiteboard',
    Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/whiteboard/view/' . $element->event_id),
    null,
    array('class' => 'small button', 'target' => '_blank')
);
if ($element->isEditable()) {

    $td_disabled = $this->module->isTheatreDiaryDisabled();

    $status = strtolower($element->status->name);

    if ((!$td_disabled && empty($element->booking)) || ($td_disabled && $status != 'scheduled')) {
        if ($element->letterType && $this->checkPrintAccess()) {
            $print_letter_options = null;
            if (!$element->has_gp || !$element->has_address) {
                $print_letter_options['disabled'] = true;
            }

            $this->event_actions[] = EventAction::button(
                'Print ' . $element->letterType . ' letter', 'print-letter', $print_letter_options,
                array('id' => 'btn_print-letter', 'class' => 'button small')
            );

            if ($element->letterType === 'Invitation') {
                $this->event_actions[] = EventAction::button('Print Admission form', 'print_admission_form', null, array('class' => 'small button'));
            }
        }
        if ($this->checkScheduleAccess() && !$td_disabled) {
            $this->event_actions[] = EventAction::link('Schedule now',
                Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/booking/schedule/' . $element->event_id),
                array('level' => 'secondary'),
                array('id' => 'btn_schedule-now', 'class' => 'button small'));
        }
    } else {
        if ($this->checkPrintAccess()) {
            $print_letter_options = null;
            if (!$element->has_address) {
                $print_letter_options['disabled'] = true;
            }
            $this->event_actions[] = EventAction::button(
                'Print letter', 'print-letter',
                $print_letter_options,
                array('id' => 'btn_print-admissionletter', 'class' => 'small button')
            );
            $this->event_actions[] = EventAction::button('Print admission form', 'print_admission_form', null, array('class' => 'small button'));
        }
        if ($this->checkScheduleAccess() && !$td_disabled) {
            $this->event_actions[] = EventAction::link(
                'Reschedule now',
                Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/booking/reschedule/' . $element->event_id),
                array('level' => 'secondary'),
                array('id' => 'btn_reschedule-now', 'class' => 'button small')
            );
        }
        if ($this->checkEditAccess()) {
            $this->event_actions[] = EventAction::link('Reschedule later',
                Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/booking/rescheduleLater/' . $element->event_id),
                array('level' => 'secondary'),
                array('id' => 'btn_reschedule-later', 'class' => 'button small'));
        }
    }
    if ($this->checkEditAccess()) {
        $this->event_actions[] = EventAction::link('Cancel operation',
            Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/default/cancel/' . $element->event_id),
            array(),
            array('id' => 'btn_cancel-operation', 'class' => 'warning button small'));
    }
}
?>
