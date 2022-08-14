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
 * @var $session OphTrOperationbooking_Operation_Session
 * @var $theatre OphTrOperationbooking_Operation_Theatre
 * @var $coreapi CoreAPI
 * @var $ward_id int
 */
?>
<div style="display: none;" id="infoBox_<?php echo $session->id ?>">
    <div class="cols-12 column">
        <div class="alert-box success with-icon">
            <strong>Session updated!</strong>
        </div>
    </div>
</div>

<?php $this->beginWidget('CActiveForm', array(
    'id' => $session->id,
    'action' => Yii::app()->createUrl('/OphTrOperationbooking/theatreDiary/saveSession'),
    'enableAjaxValidation' => false,
));

$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];
$display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');

$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(
    $display_primary_number_usage_code,
    $institution->id,
    $selected_site_id
);

$minutes_status = ($session->availableMinutes > 0);
$proc_status = (!$session->isProcedureCountLimited() || $session->getAvailableProcedureCount() > 0);
$there_is_place_for_complex_booking = (!$session->isComplexBookingCountLimited() || $session->getAvailableComplexBookingCount() > 0);
$status = ($minutes_status && $proc_status && $session->available);
$active_bookings = $session->getActiveBookingsForWard($ward_id);

?>
<div class="action_options diaryViewMode" data-id="<?php echo $session->id ?>" style="float: right;">
    <img id="loader_<?php echo $session->id ?>"
         src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading..."
         style="margin-right: 5px; margin-bottom: 4px; display: none;"/>
</div>

<div class="schedule-group">
    <div class="edit-group-btn">
        <button class="js-update-session theatre" style="display: none">Update session</button>&nbsp;
        <button class="js-cancel-update theatre" style="display: none">Cancel edit</button>
        <button class="js-edit-session theatre">Edit</button>
    </div>

    <div class="theatre-overview <?= $session->available ? 'available' : 'full' ?>">

        <div class="theatre-details">
            <div class="date"><?= date('D, j F Y', strtotime($session->date)) ?></div>
            <div class="time">  <?= date("H:i", strtotime($session->start_time)) ?>
                -
                <?= date("H:i", strtotime($session->end_time)) ?></div>
            <div class="context"> <?php echo $session->firm ? $session->firm->name : 'Emergency List' ?>
                <?php echo $session->firm ? 'for (' . $session->firm->serviceSubspecialtyAssignment->subspecialty->name . ')' : '' ?></div>
            <div class="theatre"><?php echo $theatre->name . ' (' . $theatre->site->name . ')' ?></div>

            <div class="session-unavailable" style="display:none">
                <label class="highlight">
                    <!-- reverse checkbox logic : "session is available" vs "session is UNavailable" -->
                    <input value="1" class="js-hidden-available" name="available_<?=$session->id?>" type='hidden'<?=$session->available ? '' : ' disabled';?>>
                    <input value="0" class="js-hidden-unavailable" name="available_<?=$session->id?>" type='hidden'<?=!$session->available ? '' : ' disabled';?>>
                    <input type="checkbox" class="session-available" <?=!$session->available ? 'checked' : '';?>> Session is unavailable
                </label>

                <?=\Chtml::activeDropDownList($session, 'unavailablereason_id', \CHtml::listData($session->getUnavailableReasonList(), 'id', 'name'), [
                    'empty' => 'Select',
                    'class' => 'unavailable-reasons cols-full',
                    'nowrapper' => true,
                    'name' => "unavailablereason_id_{$session->id}",
                    'id' => "unavailablereason_id_{$session->id}",
                    'style' => 'display:' . ($session->available ?  'none' : 'block'),
                ]);?>
            </div>
        </div>

        <div class="theatre-state">
            <div class="session-time-left">
                <div class="minutes" data-available-minutes="<?=$session->availableMinutes ?>" data-full="<?=abs($session->availableMinutes) ?>">
                    <?php if (!$session->available) : ?>
                        Session unavailable
                    <?php elseif ($minutes_status) :?>
                        <?=$session->availableMinutes ?> mins available
                    <?php else : ?>
                        Full (<?=abs($session->availableMinutes) ?> minutes overbooked)
                    <?php endif; ?>
                </div>
                <div class="session-max" <?= $session->isProcedureCountLimited() ? '' : "style='display: none;'" ?>>
                    <div class="max-limit js-diaryViewMode js-max-procedures-val" data-current-procedure-count="<?= count($active_bookings) ?>">Max <?= $session->getMaxProcedureCount() ?> patients</div>
                    <div class="max-limit js-diaryEditMode" style="display:none">
                        Max <input class="cols-2" type="text" name="max_procedures_<?=$session->id;?>" id="max_procedures_<?=$session->id;?>" value="<?= $session->getMaxProcedureCount() ?>"> patients
                    </div>
                    <div class="overbooked js-max-patients">
                        <?php if ($session->getAvailableProcedureCount() >= 0) { ?>
                            <span class="bookings-num"><?= $session->getAvailableProcedureCount() ?></span> available
                        <?php } else { ?>
                            Overbooked by <span class="bookings-num highlighter warning">
                                <?= abs($session->getAvailableProcedureCount()) ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="session-max" <?= $session->isComplexBookingCountLimited() ? '' : "style='display: none;'" ?>>
                    <div class="max-limit js-diaryViewMode js-max-complex-bookings-value"
                         data-current-max-complex-count="<?= $session->getComplexBookingCount() ?>">Max <?= $session->getMaxComplexBookingCount() ?> complex bookings
                    </div>
                    <div class="max-limit js-diaryEditMode" style="display:none">
                         Max <input class="cols-2" type="text" name="max_complex_bookings_<?=$session->id;?>" id="max_complex_bookings_<?=$session->id;?>" value="<?= $session->getMaxComplexBookingCount() ?>"> complex bookings
                    </div>
                    <div class="overbooked js-max-complex-booking">
                        <?php if ($session->getAvailableComplexBookingCount() >= 0) { ?>
                            <span class="complex-bookings-num"><?= $session->getAvailableComplexBookingCount() ?></span> available
                        <?php } else { ?>
                            Overbooked by <span class="complex-bookings-num highlighter warning">
                                <?= abs($session->getAvailableComplexBookingCount()) ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="session-unavailable-reason" style="display:<?=!$session->available ? 'block' : 'none';?>">
                    <?=$session->unavailablereason->name ?? 'No reason provided';?>
                </div>
            </div>

            <div class="specialists">
                <?php if ($this->checkAccess('OprnEditTheatreSessionDetails')) : ?>
                    <?php $this->renderPartial('_session_edit_side_bar', ['session' => $session]); ?>
                <?php endif; ?>
            </div><!-- specialists -->
        </div>
    </div>

    <?php $tabe_time = microtime(true); ?>
    <table class="theatre-bookings">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
            <col>
            <col class="cols-6">
        <tbody id="tbody_<?php echo $session->id ?>" class="ui-sortable">

        <?php if (!count($active_bookings)) : ?>
            <tr>
                <td rowspan="4"><b class="increase-text">No bookings</b></td>
            </tr>
        <?php endif; ?>

        <?php foreach ($active_bookings as $booking) : ?>
            <?php $patient = $booking->operation->event->episode->patient; ?>

            <?php echo $this->renderPartial('_booking_table_row', [
                'booking' => $booking,
                'time' => substr($booking->admission_time, 0, 5),
                'total_duration' => $booking->operation->total_duration,
                'patient' => $patient,
                'show_patient_summary_popup' => $show_patient_summary_popup,
                'operation' => $booking->operation,
                'session' => $session,
                'event' => $booking->operation->event,
                'biometry' => OphTrOperationbooking_Whiteboard::model()->recentBiometry($patient),
                'coreapi' => $coreapi,
                'consent_event' => (function () use ($booking) {
                    $criteria = new CDbCriteria();
                    $criteria->addCondition('booking_event_id = :booking_event_id');
                    $criteria->params[':booking_event_id'] = $booking->operation->event->id;
                    $criteria->order = 'created_date DESC';
                    return Element_OphTrConsent_Procedure::model()->find($criteria);
                })(),
            ]); ?>

        <?php endforeach; ?>
        </tbody>
        <tfoot id="tfoot_<?php echo $session->id ?>">
        <tr>
            <td colspan="4">
                <div class="session-comments">

                    <?php if (empty($session->comments)) : ?>
                        <span class="user-comment fade js-comments-view js-diaryViewMode">No session list comments</span>
                    <?php else : ?>
                        <i class="js-diaryViewMode oe-i comments-who small pad-right js-has-tooltip"
                               data-tt-type="basic"
                               data-tooltip-content="User comment">
                            </i>
                        <span class="user-comment js-comments-view js-diaryViewMode"><?= CHtml::encode($session->comments); ?></span>
                    <?php endif; ?>

                    <textarea rows="3"
                              class="cols-full js-comments-edit js-diaryEditMode"
                              placeholder="Session list comments"
                              style="display: none;"
                              name="comments_<?=$session->id ?>"
                              spellcheck="false"><?= CHtml::encode($session->comments) ?></textarea>
                </div>
            </td>
        </tr>
        </tfoot>
    </table> <!-- table rendered: <?= microtime(true) - $tabe_time; ?>s -->

    <div class="data-group">
        <div style="display: none;" data-id="<?php echo $session->id ?>" class="diaryEditMode">
            <i id="loader2_<?php echo $session->id ?>" class="spinner" style="display:none"></i>

            <button type="submit" class="secondary small theatre"
                    id="btn_edit_session_save_<?php echo $session->id ?>"><span
                        class="button-span button-span-green">Save changes to session</span></button>
            <button type="submit" class="warning small theatre"
                    id="btn_edit_session_cancel_<?php echo $session->id ?>"><span
                        class="button-span button-span-red">Cancel</span></button>
        </div>
    </div>

</div>
<?php $this->endWidget() ?>
