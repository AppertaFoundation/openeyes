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

/**
 * @var $element Element_OphTrOperationbooking_Operation
 */
?>
    <section class="element view full priority view-procedures">
        <header class="element-header">
            <h3 class="element-title">Procedure<?= count($element->procedures) !== 1 ? 's' : null ?> & OPCS codes</h3>
        </header>
        <div class="element-data full-width">
            <div class="cols-10">
                <table class="priority-text last-left">
                    <tbody>
                    <?php foreach ($element->procedures as $procedure) : ?>
                        <tr>
                            <td>
                                <span class="priority-text">
                                    <?php echo $element->eye->adjective ?>
                                    <?php echo $procedure->term ?>
                                </span>
                            </td>
                            <td>
                                <span class="priority-text">
                                    <?= implode(', ', array_map(static function ($x) {
                                        return $x->name;
                                    }, $procedure->opcsCodes)) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="element view full  view-information">
        <header class="element-header">
            <h3 class="element-title">Information</h3>
        </header>
        <div class="element-data full-width flex-layout flex-top col-gap">
            <div class="cols-6">
                <table class="label-value last-left">
                    <tbody>
                    <tr>
                        <td>
                            <div class="data-label">Complexity</div>
                        </td>
                        <td>
                            <div class="data-value"><?= $element->complexityCaption?></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="data-label">Consultant required?</div>
                        </td>
                        <td>
                            <?php
                            if ($element->consultant) {
                                $consultant_name = $element->consultant->ReversedFullName;
                            } else {
                                $consultant_name = 'Consultant';
                            }
                            ?>
                            <div class="data-value"><?php echo $element->consultant_required ? "Yes, $consultant_name" : 'No Consultant' ?></div>
                        </td>
                    </tr>
                    <tr>
                        <?php if ($element->senior_fellow_to_do !== null) : ?>
                            <td>
                                <div class="data-label">
                                    <?= CHtml::encode($element->getAttributeLabel('senior_fellow_to_do')) ?>
                                </div>
                            </td>
                            <td>
                                <div class="data-value"><?= $element->senior_fellow_to_do ? 'Yes' : 'No' ?></div>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php if (isset($element->any_grade_of_doctor)) { ?>
                    <tr>
                        <td>
                            <div class="data-label">
                                <?= CHtml::encode($element->getAttributeLabel('any_grade_of_doctor')) ?>?
                            </div>
                        </td>
                        <td>
                            <div class="data-value">
                                <?php echo $element->any_grade_of_doctor ? 'Yes' : 'No' ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>
                            <div class="data-label">Anaesthetic</div>
                        </td>
                        <td>
                            <div class="data-value">
                                <?= $element->getAnaestheticTypeDisplay() ?>
                            </div>
                        </td>
                    </tr>

                    <?php if ($element->anaesthetic_choice) : ?>
                        <tr>
                            <td>
                                <div class="data-label">
                                    <?= CHtml::encode($element->getAttributeLabel('anaesthetic_choice_id')) ?>
                                </div>
                            </td>
                            <td>
                                <div class="data-value"><?= $element->anaesthetic_choice->name ?></div>
                            </td>
                        </tr>
                    <?php endif ?>
                    <?php if ($element->stop_medication !== null) : ?>
                        <tr>
                            <td>
                                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('stop_medication')) ?></div>
                            </td>
                            <td>
                                <div class="data-value"><?= $element->stop_medication ? 'Yes' : 'No' ?></div>
                                <?php if ($element->stop_medication) : ?>
                                    <div class="data-value panel comments">
                                        <?= Yii::app()->format->nText($element->stop_medication_details) ?>
                                    </div>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
            <div class="cols-6">
                <table class="label-value last-left">
                    <tbody>
                    <tr>
                        <td>
                            <div class="data-label">Decision Date</div>
                        </td>
                        <td>
                            <div class="data-value">
                                <?php echo $element->NHSDate('decision_date') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('site_id')) ?></div>
                        </td>
                        <td>
                            <div class="data-value"><?php echo $element->site->name ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="data-label">Operation priority</div>
                        </td>
                        <td>
                            <div class="data-value"><?php echo $element->priority->name ?></div>
                        </td>
                    </tr>
                    <?php if (!empty($element->comments)) { ?>
                        <tr>
                            <td>
                                <div class="data-label">Operation Comments</div>
                            </td>
                            <td>
                                <div class="data-value panel comments">
                                    <?php echo $element->textWithLineBreaks('comments') ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>
                            <div class="data-label">Admission category:</div>
                        </td>
                        <td>
                            <div class="data-value">
                                <?php echo ($element->overnight_stay) ? 'An overnight stay' : 'Day case' ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="data-label">Total theatre time (mins):</div>
                        </td>
                        <td>
                            <div class="data-value">
                                <?= CHtml::encode($element->total_duration) ?>
                            </div>
                        </td>
                    </tr>
                    <?php if ($element->special_equipment !== null) : ?>
                        <tr>
                            <td>
                                <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('special_equipment')) ?></div>
                            </td>
                            <td>
                                <div class="data-value"><?= $element->special_equipment ? 'Yes' : 'No' ?></div>
                                <?php if ($element->special_equipment) : ?>
                                    <div class="data-value panel comments">
                                        <?= Yii::app()->format->nText($element->special_equipment_details) ?>
                                    </div>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endif ?>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="element view full  view-information">
        <header class="element-header">
            <h3 class="element-title">Comments</h3>
        </header>
        <table class="cols-10">
            <colgroup>
                <col class="cols-4">
            </colgroup>
            <tbody>
            <?php if (Yii::app()->params['ophtroperationbooking_referral_link']) { ?>
                <tr>
                    <td>
                        <h3 class="data-label">Referral</h3>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php if ($element->referral) {
                                echo $element->referral->getDescription();
                            } else {
                                echo 'No Referral Set';
                            } ?>
                        </div>
                        <?php if ($rtt = $element->getRTT()) { ?>
                            <div class="rtt-info">Clock Start -
                                <?= Helper::convertDate2NHS($rtt->clock_start) ?>
                                Breach: <?= Helper::convertDate2NHS($rtt->breach) ?>
                            </div>
                        <?php } ?>
                    </td>
                    <td></td>
                </tr>
            <?php } ?>
            <?php if (!empty($element->comments_rtt)) { ?>
                <tr>
                    <td>
                        Operation RTT Comments
                    </td>
                    <td>
                        <div class="data-value panel comments">
                            <?php echo $element->textWithLineBreaks('comments_rtt') ?>
                        </div>
                    </td>
                    <td></td>
                </tr>
            <?php } ?>
            <?php if ($element->organising_admission_user) : ?>
                <tr>
                    <td>
                        <?= CHtml::encode($element->getAttributeLabel('organising_admission_user_id')) ?>
                    </td>
                    <td>
                        <div class="data-value">
                            <?= $element->organising_admission_user->getReversedFullName() ?>
                        </div>
                    </td>
                    <td></td>
                </tr>
            <?php endif; ?>
            <?php if (!$this->module->isTheatreDiaryDisabled() && !$this->module->isGoldenPatientDisabled()) : ?>
                <tr>
                    <td>
                        <h3 class="data-title">
                            <?= CHtml::encode($element->getAttributeLabel('is_golden_patient')) ?>
                        </h3>
                    </td>
                    <td>
                        <div class="data-value">
                            <?= $element->is_golden_patient ? 'Yes' : 'No' ?>
                        </div>
                    </td>
                </tr>
            <?php endif ?>
            </tbody>
        </table>
    </section>

<?php if ($element->booking && !$this->module->isTheatreDiaryDisabled()) { ?>
    <section class="element">
        <h3 class="element-title highlight">Booking Details</h3>
        <div class="element-data">
            <table>
                <tbody>
                <tr>
                    <td>
                        <h3 class="data-title">List</h3>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php $session = $element->booking->session ?>
                            <?php echo $session->NHSDate('date') . ' ' . $session->TimeSlot . ', ' . $session->FirmName; ?>
                        </div>
                    </td>
                </tr>
                <?php
                $warnings = $session->getWarnings();
                if ($warnings) { ?>
                    <div class="alert-box alert with-icon">Please note:
                        <ul>
                            <?php foreach ($warnings as $warning) {
                                echo '<li>' . $warning . '</li>';
                            } ?>
                        </ul>
                    </div>
                <?php } ?>
                <tr>
                    <td>
                        <h3 class="data-title">Theatre</h3>
                    </td>
                    <td>
                        <div class="data-value"><?php echo $session->TheatreName ?></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="element element-data flex-layout">
        <h3 class="data-title cols-2">Admission Time</h3>
        <div class="data-value">
            <?php echo substr($element->booking->admission_time, 0, 5) ?>
        </div>
    </section>

    <?php if ($element->booking->erod) { ?>
        <section class="element">
            <h3 class="element-title highlight">Earliest reasonable offer date</h3>
            <div class="element-data">
                <div class="cols-12 column">
                    <div class="data-value">
                        <?php echo $element->booking->erod->getDescription() ?>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>


    <div class="data-group">
        <div class="cols-12 column">
            <div class="metadata">
                <span class="info">
                    Operation scheduling created by
                    <span class="user"><?php echo $element->booking->user->fullname ?></span>
                    on <?php echo $element->booking->NHSDate('created_date') ?>
                    at <?php echo date('H:i', strtotime($element->booking->created_date)) ?>
                </span>
                <span class="info">
                    Operation scheduling last modified by
                           <span class="user"><?php echo $element->booking->usermodified->fullname ?></span>
                    on <?php echo $element->booking->NHSDate('last_modified_date') ?>
                    at <?php echo date('H:i', strtotime($element->booking->last_modified_date)) ?>
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
                    </li>
                    <li>
                        Cancelled on <?php echo $booking->NHSDate('booking_cancellation_date'); ?>
                        by <strong><?php echo $booking->usercancelled->FullName; ?></strong>
                        due to <?= CHtml::encode($booking->cancellationReasonWithComment) ?>
                        <?php if ($booking->erod) { ?>
                            <br/><span class="erod">EROD was <?= $booking->erod->getDescription() ?></span>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </section>
<?php } ?>

<?php if (($element->status->name === 'Cancelled' || $element->status->name === 'Requires rescheduling')
    && $element->operation_cancellation_date) { ?>
    <section class="element flex-layout">
        <h3 class="element-title highlight cols-2">Cancellation details</h3>
        <div class="element-data cols-10">
            <div class="data-value">
                Cancelled on
                <?php echo $element->NHSDate('operation_cancellation_date') . ' by ' . $element->cancellation_user->getFullName() .
                    ' for reason: ' . CHtml::encode($element->cancellation_reason->text); ?>
            </div>
        </div>
    </section>

    <?php if ($element->cancellation_comment) { ?>
        <section class="element element-data flex-layout">
            <h3 class="data-title cols-2">Cancellation comments</h3>
            <div class="data-value panel comments cols-10">
                <?= CHtml::encode($element->cancellation_comment) ?>
            </div>
        </section>
    <?php } ?>
<?php } ?>

<?php
$whiteboard_display_mode = SettingMetadata::model()->getSetting('opbooking_whiteboard_display_mode');

if ($whiteboard_display_mode === 'CURRENT') {
    $this->event_actions[] = EventAction::link(
        'Whiteboard',
        '#',
        null,
        array('class' => 'small button', 'id' => 'js-display-whiteboard', 'data-id' => $element->event_id)
    );
    $this->event_actions[] = EventAction::link(
        'Close Whiteboard',
        '#',
        null,
        array('class' => 'small button', 'id' => 'js-close-whiteboard', 'data-id' => $element->event_id)
    );
} else {
    $this->event_actions[] = EventAction::link(
        'Whiteboard',
        Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/whiteboard/view/' . $element->event_id),
        null,
        array('class' => 'small button', 'target' => '_blank')
    );
}

if ($element->isEditable()) {
    $td_disabled = $this->module->isTheatreDiaryDisabled();

    $status = strtolower($element->status->name);

    if ($status === 'on-hold') {
        $this->event_actions[] = EventAction::link(
            'Take off hold',
            Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/default/putOffHold/' . $element->event_id),
            null,
            array('class' => 'small button', 'id' => 'js-put-off-hold')
        );
    } else {
        $this->event_actions[] = EventAction::link(
            'Place on hold',
            Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/default/putOnHold/' . $element->event_id),
            null,
            array('class' => 'small button', 'id' => 'js-put-on-hold')
        );
    }

    if ((!$td_disabled && empty($element->booking)) || ($td_disabled && $status !== 'scheduled')) {
        if ($element->letterType && $this->checkPrintAccess()) {
            $print_letter_options = null;
            if (!$element->has_gp || !$element->has_address) {
                $print_letter_options['disabled'] = true;
            }

            $this->event_actions[] = EventAction::button(
                'Print ' . $element->letterType . ' letter',
                'print-letter',
                $print_letter_options,
                array('id' => 'btn_print-letter', 'class' => 'button small')
            );

            if ($element->letterType === 'Invitation') {
                $this->event_actions[] = EventAction::button('Print Admission form', 'print_admission_form', null, array('class' => 'small button'));
            }
        }
        if (!$td_disabled && $this->checkScheduleAccess()) {
            $this->event_actions[] = EventAction::link(
                'Schedule now',
                Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/booking/schedule/' . $element->event_id),
                array('level' => 'secondary'),
                array('id' => 'btn_schedule-now', 'class' => 'button small')
            );
        }
    } else {
        if ($this->checkPrintAccess()) {
            $print_letter_options = null;
            if (!$element->has_address) {
                $print_letter_options['disabled'] = true;
            }
            $this->event_actions[] = EventAction::button(
                'Print letter',
                'print-letter',
                $print_letter_options,
                array('id' => 'btn_print-admissionletter', 'class' => 'small button')
            );
            $this->event_actions[] = EventAction::button('Print admission form', 'print_admission_form', null, array('class' => 'small button'));
        }
        if (!$td_disabled && $this->checkScheduleAccess()) {
            $this->event_actions[] = EventAction::link(
                'Reschedule now',
                Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/booking/reschedule/' . $element->event_id),
                array('level' => 'secondary'),
                array('id' => 'btn_reschedule-now', 'class' => 'button small')
            );
        }
        if ($this->checkEditAccess()) {
            $this->event_actions[] = EventAction::link(
                'Reschedule later',
                Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/booking/rescheduleLater/' . $element->event_id),
                array('level' => 'secondary'),
                array('id' => 'btn_reschedule-later', 'class' => 'button small')
            );
        }
    }
    if ($this->checkEditAccess()) {
        $this->event_actions[] = EventAction::link(
            'Cancel operation',
            Yii::app()->createUrl('/' . $element->event->eventType->class_name . '/default/cancel/' . $element->event_id),
            array(),
            array('id' => 'btn_cancel-operation', 'class' => 'warning button small')
        );
    }
}
?>
