<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php $this->beginContent('//patient/event_container', array('no_face' => true)); ?>
    <div>
        <?php
        $clinical = $clinical = $this->checkAccess('OprnViewClinical');

        $warnings = $this->patient->getWarnings($clinical);
        $this->title = ($operation->booking ? 'Re-schedule' : 'Schedule') . ' Operation'; ?>

        <div class="alert-box alert with-icon" style="display: <?php if (!is_array($errors)) {
            echo 'none';
        } ?>">
            <p>Please fix the following input errors:</p>
            <ul>
                <?php if (is_array($errors)) {
                foreach ($errors as $errors2) {
                    foreach ($errors2 as $error) { ?>
                        <li><?php echo $error ?></li>
                    <?php }
                } ?>
            </ul>
            <?php } else { ?>
                <li>&nbsp;</li>
                </ul>
            <?php } ?>
        </div>

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
        <div class="element-actions">
        <span class="js-remove-element">
            <i class="oe-i remove-circle"></i>
        </span>
        </div>
        <div class="element-fields full-width flex-layout flex-top col-gap">
            <div class="cols-6">
                <div class="alert-box info large-text">
                    <?php echo $this->patient->getDisplayName() ?>
                    (<?php echo $this->patient->hos_num ?>)
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
                                <?=\CHtml::label($operation->getAttributeLabel('referral_id') . ':',
                                    'referral_id'); ?>
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
                                    echo CHtml::activedropDownList($operation, 'referral_id',
                                        CHtml::listData($this->getReferralChoices(), 'id', 'description'), $html_options, false,
                                        array('field' => 2));
                                    ?>
                                    <span id="rtt-info" class="rtt-info" style="display: none">Clock start - <span
                                                id="rtt-clock-start"></span> Breach - <span id="rtt-breach"></span></span>
                                <? } else { ?>
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
        <div class="element-actions">
        <span class="js-remove-element">
		    <i class="oe-i remove-circle"></i>
		</span>
        </div>

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
                    'errors' => $errors,
                ), false, false) ?>
            <?php } ?>
    </div>
</section>
<?php $this->endContent(); ?>