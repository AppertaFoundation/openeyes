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
?>
<?php $this->beginContent('//patient/event_container', array('no_face' => true));
$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];
$primary_identifier_usage_type = Yii::app()->params['display_primary_number_usage_code'];
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'],
    $this->patient->id, $institution->id, $selected_site_id);
?>
    <div>
        <?php
        $this->event_actions[] = EventAction::link(
            'Cancel Scheduling',
            Yii::app()->createUrl('/OphTrOperationbooking/default/view/' . $this->operation->event_id),
            array(),
            array('id' => 'cancel_scheduling', 'class' => 'red warning')
        );

        $clinical = $clinical = $this->checkAccess('OprnViewClinical');

        $warnings = $this->patient->getWarnings($clinical);
        $this->title = ($operation->booking ? 'Re-schedule' : 'Schedule') . ' Operation'; ?>


        <?php if (isset($errors) && !empty($errors)) {
            $this->displayErrors($errors);
        } ?>

        <?php if ($warnings) { ?>
            <div class="alert-box warning">
                <i class="oe-i triangle"></i>
                <?php foreach ($warnings as $warn) { ?>
                    <?php echo $warn['long_msg']; ?>
                    <?php echo $warn['details'];
                } ?>
            </div>
        <?php } ?>


    </div>
    <section class="element edit full  edit-options">
        <header class="element-header">
            <h3 class="element-title">Options</h3>
        </header>

        <div class="element-fields full-width flex-layout flex-top col-gap">
            <div class="cols-6">
                <div class="alert-box info large-text">
                    <?php echo $this->patient->getDisplayName() ?>
                    (<?= PatientIdentifierHelper::getIdentifierValue($primary_identifier) ?>)
                    <?php $this->widget(
                        'application.widgets.PatientIdentifiers',
                        [
                            'patient' => $this->patient,
                            'show_all' => true
                        ]); ?>
                </div>
                <table class="cols-full last-left">
                    <tbody>
                    <?php if ($operation->booking) { ?>
                        <tr>
                            <td>
                                Operation duration:
                            </td>
                            <td>
                                <?php echo $operation->total_duration; ?> minutes
                            </td>
                        <tr>
                            <td>
                                Current schedule:
                            </td>
                            <td>
                                <?php $this->renderPartial('_session', array('operation' => $operation)); ?>
                            </td>
                        </tr>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>
                            Viewing the schedule for
                        </td>
                        <td>
                            <div class="alert-box info"><?php echo $firm->name ?></div>
                        </td>
                        <?php if (empty($sessions)) { ?>
                            <div class="alert-box alert">This <?php echo Firm::model()->contextLabel()?> has no scheduled sessions.</div>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>
                            Context Change
                        </td>
                        <td>
                            <div class="edit" id="firmSelect">
                                <div class="element-fields">
                                    <div class="data-group">
                                        <select id="firm_id" class="inline firm-switcher">
                                            <option value="">Select a different <?php echo Firm::model()->contextLabel()?></option>
                                            <option value="EMG">Emergency List</option>
                                            <?php foreach ($firm_list as $_firm) { ?>
                                                <option value="<?=$_firm->id ?>"><?=$_firm->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if (Yii::app()->params['ophtroperationbooking_referral_link']) { ?>
                        <tr>
                            <td>
                                <?=\CHtml::label(
                                    $operation->getAttributeLabel('referral_id') . ':',
                                    'referral_id'
                                ); ?>
                            </td>
                            <td>
                                <?php
                                if ($operation->canChangeReferral()) { ?>
                                    <?php
                                    $html_options = array(
                                        'options' => array(),
                                        'empty' => '- No valid referral available -',
                                        'nowrapper' => true,
                                    );
                                    $choices = $this->getReferralChoices();
                                    foreach ($choices as $choice) {
                                        if ($active_rtt = $choice->getActiveRTT()) {
                                            if (count($active_rtt) == 1) {
                                                $html_options['options'][(string)$choice->id] = array(
                                                    'data-clock-start' => Helper::convertDate2NHS($active_rtt[0]->clock_start),
                                                    'data-breach' => Helper::convertDate2NHS($active_rtt[0]->breach),
                                                );
                                            }
                                        }
                                    }
                                    echo CHtml::activedropDownList(
                                        $operation,
                                        'referral_id',
                                        CHtml::listData($this->getReferralChoices(), 'id', 'description'),
                                        $html_options,
                                        false,
                                        array('field' => 2)
                                    );
                                    ?>
                                    <span id="rtt-info" class="rtt-info" style="display: none">Clock start - <span
                                                id="rtt-clock-start"></span> Breach - <span id="rtt-breach"></span></span>
                                <?php } else { ?>
                                    <?php
                                    if ($operation->referral) {
                                        echo $operation->referral->getDescription();
                                    } else {
                                        echo 'No referral was set.';
                                    }
                                    ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="cols-6">
                <?php
                $initial_erod = ($operation->firstBooking) ? $operation->firstBooking->erod : null;
                $erod = $operation->calculateEROD($firm);
                if ($initial_erod || $erod) { ?>
                    <table class="cols-full last-left">
                        <tbody>
                        <tr>
                            <td>
                                <?= CHtml::encode($schedule_options->getAttributeLabel('schedule_options_id')) ?>:
                            </td>
                            <td class="large-text">
                                <?= CHtml::encode($schedule_options->schedule_options->name) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                EROD
                            </td>
                            <td>
                                <?php if ($erod) {
                                    echo $erod->getDescription();
                                } else {
                                    echo 'N/A';
                                }
                                if ($initial_erod) {
                                    echo ' <span class="initial-erod">Initially: ' . $initial_erod->getDescription();
                                } ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
</section>
<section class="element edit full  edit-select-theatre-date">
        <header class="element-header">
            <h3 class="element-title">Select theatre slot</h3>
        </header>

        <?php if (Yii::app()->user->hasFlash('info')) { ?>
            <div class="alert-box">
                <?php echo Yii::app()->user->getFlash('info'); ?>
            </div>
        <?php } ?>

        <h4>Select a session date:</h4>
        <div id="calendar">
            <div id="session_dates">
                <div id="details">
                    <?php echo $this->renderPartial('_calendar', array(
                        'operation' => $operation,
                        'date' => $date,
                        'firm' => $firm,
                        'selectedDate' => $selectedDate,
                        'sessions' => $sessions,
                    ), false, false); ?>
                </div>
            </div>
        </div>
</section>
<section class="element edit full  edit-select-theatre-date">
            <?php if ($theatres) { ?>
                <?php echo $this->renderPartial('_theatre_times', array(
                    'operation' => $operation,
                    'date' => $selectedDate,
                    'theatres' => $theatres,
                    'reschedule' => $operation->booking,
                    'firm' => $firm,
                    'selectedDate' => $selectedDate,
                    'selectedSession' => $session,
                ), false, false) ?>
            <?php } ?>
</section>
<section class="element edit full  edit-other-operations-in-this-session">
    <div id="sessionDetails">
            <?php if ($session) { ?>
                <?php echo $this->renderPartial('_list', array(
                    'operation' => $operation,
                    'session' => $session,
                    'bookings' => $bookings,
                    'reschedule' => $operation->booking,
                    'bookable' => $bookable,
                    'there_is_place_for_complex_booking' => $there_is_place_for_complex_booking,
                    'errors' => $errors,
                ), false, false) ?>
            <?php } ?>
    </div>
</section>
<?php $this->endContent(); ?>