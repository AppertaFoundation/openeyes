<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

if (!$reschedule) {
    echo CHtml::form(Yii::app()->createUrl('/OphTrOperationbooking/booking/schedule/' . $operation->event->id . '?firm_id=' . $_GET['firm_id'] . '&date=' . $_GET['date'] . '&day=' . $_GET['day'] . '&session_id=' . $_GET['session_id']), 'post', array('id' => 'bookingForm'));
} else {
    echo CHtml::form(Yii::app()->createUrl('/OphTrOperationbooking/booking/reschedule/' . $operation->event->id . '?firm_id=' . $_GET['firm_id'] . '&date=' . $_GET['date'] . '&day=' . $_GET['day'] . '&session_id=' . $_GET['session_id']), 'post', array('id' => 'bookingForm'));
}
?>
<header class="element-header">
    <h3 class="element-title">Other operations in this session:
        (<?php echo abs($session->availableMinutes) . " min {$session->minuteStatus}";
        if ($session->isProcedureCountLimited()) {
            echo ', ' . $session->getAvailableProcedureCount() . '/' . $session->getMaxProcedureCount() . ' procedures left';
        }
        if ($session->isComplexBookingCountLimited()) {
            echo ', ' . $session->getAvailableComplexBookingCount() . '/' . $session->getMaxComplexBookingCount() . ' complex bookings left';
        } ?>)
    </h3>
</header>

<div class="element-fields full-width">
    <table class="standard" id="appointment_list">
        <thead>
        <tr>
            <th>Operation list overview</th>
            <th>Date: <?php echo Helper::convertDate2NHS($session['date']); ?></th>
            <th>Anaesthetic type</th>
            <th>Session time: <?php echo substr($session['start_time'], 0, 5) . ' - '
                    . substr($session['end_time'], 0, 5); ?></th>
            <th>Admission time</th>
            <th>Comments</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $counter = 1;
        foreach ($bookings as $booking) { ?>
            <tr>
                <td><?php echo $counter ?>
                    . <?php echo $booking->operation->event->episode->patient->getDisplayName() ?></td>

                <td>
                    <?php
                    $procedures = [];
                    foreach ($booking->operation->procedures as $procedure) {
                        $icon = $booking->operation->complexity ? OEHtml::icon('circle-' . Element_OphTrOperationbooking_Operation::$complexity_colors[$booking->operation->complexity], ['class' => 'small pad']) : '';
                        $eye = "[" . Eye::methodPostFix($booking->operation->eye_id) . "] ";
                        $procedures[] = $icon . $eye . $procedure->term;
                    }

                    echo empty($procedures) ? 'No procedures' : implode('<br />', $procedures);
                    ?>
                </td>
                <td><?php echo $booking->operation->getAnaestheticTypeDisplay() ?></td>
                <td><?php echo "{$booking->operation->total_duration} minutes"; ?></td>
                <td><?php echo $booking->admission_time ?></td>
                <td><?=\CHtml::encode($booking->operation->comments) ?></td>
            </tr>
            <?php ++$counter;
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="6">
                <div class="alert-box info">
                    <?php echo ($counter - 1) . ' booking';
                    if (($counter - 1) != 1) {
                        echo 's';
                    }
                    if ($bookable) {
                        echo ' currently scheduled';
                    } else {
                        echo ' were scheduled';
                    }
                    ?>
                </div>
            </th>
        </tr>
        </tfoot>
    </table>
</div>
<?php if ($bookable) { ?>
    <div class="flex-layout flex-top col-gap">
        <div class="cols-6">
            <table class="standard">
                <tbody>
                <tr>
                    <td>
                        Ward
                    </td>
                    <td>
                        <?=\CHtml::dropDownList('Booking[ward_id]', @$_POST['Booking']['ward_id'], $operation->getWardOptions($session), array('class' => 'cols-full')) ?>
                        <span id="Booking_ward_id_error"></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        Admission Time
                    </td>
                    <td>
                        <input type="text" id="Booking_admission_time" name="Booking[admission_time]" class="cols-full"
                               autocomplete="<?php echo SettingMetadata::model()->getSetting('html_autocomplete') ?>"
                               value="<?=\CHtml::encode($_POST['Booking']['admission_time']) ?>" size="6"/>
                        <span id="Booking_admission_time_error"></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        Session Comments
                    </td>
                    <td>
                    <textarea id="Session_comments" name="Session[comments]"
                              class="cols-full autosize"><?=\CHtml::encode($_POST['Session']['comments']) ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
        if ($reschedule) {
            echo CHtml::hiddenField('booking_id', $operation->booking->id);
        }
        echo CHtml::hiddenField('Booking[element_id]', $operation->id);
        echo CHtml::hiddenField('Booking[session_id]', $session['id']);
        if ($operation->canChangeReferral()) {
            echo CHtml::hiddenField('Operation[referral_id]', $operation->referral_id);
        }
        ?>
        <div class="cols-6">
            <table class="standard">
                <tbody>
                <?php if ($reschedule) { ?>
                    <tr>
                        <td>
                                <?=\CHtml::label('<strong>Reschedule Reason:</strong> ', 'cancellation_reason'); ?>
                        </td>
                        <td>
                            <?php if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
                                $listIndex = 3;
                            } else {
                                $listIndex = 2;
                            } ?>
                            <?=\CHtml::dropDownList(
                                'cancellation_reason',
                                '',
                                OphTrOperationbooking_Operation_Cancellation_Reason::getReasonsByListNumber($listIndex),
                                [
                                    'empty' => 'Select a reason',
                                    //how nice would be use the an activeDropDownList with cancellation_reason_id
                                    //with the built in error adding feature ... but for some reason all over the
                                    //OpBooking only "cancellation_reason" is used, this is definitely needs to be refactored
                                    'class' => $operation->getError('cancellation_reason_id') ? 'error ' . $operation->id : '',
                                ]
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?=\CHtml::label('<strong>Reschedule Comments:</strong> ', 'cancellation_comment'); ?>
                        </td>
                        <td>
                            <textarea name="cancellation_comment" class="cols-full autosize"><?=\CHtml::encode(@$_POST['cancellation_comment']) ?></textarea>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>
                        <?=\CHtml::label('<strong>Operation Comments:</strong>', 'operation_comments'); ?>
                    </td>
                    <td>
                        <textarea id="operation_comments" name="Operation[comments]" class="cols-full autosize"><?=\CHtml::encode($_POST['Operation']['comments']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?=\CHtml::label('<strong>RTT Comments:</strong>', 'rtt_comments'); ?>
                    </td>
                    <td>
                        <textarea id="operation_comments" name="Operation[comments_rtt]" class="cols-full autosize"><?=\CHtml::encode($_POST['Operation']['comments_rtt']) ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="highlighter large-text">
        Date/Time currently selected:<?php echo Helper::convertDate2NHS($session['date']); ?>, <?php echo substr($session['start_time'], 0, 5) . ' - ' . substr($session['end_time'], 0, 5); ?>
    </div>
    <div class="data-group">
        <button type="submit" class="large green hint" id="confirm_slot" data-there-is-place-for-complex-booking="<?= $session->isTherePlaceForComplexBooking($operation) ? "true" : "false" ?>">Confirm slot</button>
        <button type="button" class="large red hint" id="cancel_scheduling"><?php echo 'Cancel ' . ($reschedule ? 're-' : '') . 'scheduling';?></button>
    </div>
    <?php
    echo CHtml::endForm();
    ?>
<?php } ?>
