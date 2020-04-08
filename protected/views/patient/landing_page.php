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
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
$correspondence_api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
// Removing the unnecessary canViewSummary parameter from the localisation, this needs to be implemented properly by creating a new role - CERA590
$allow_clinical = Yii::app()->user->checkAccess('OprnViewClinical');

?>

<?php if ($no_episodes && $allow_clinical) { ?>
    <div class="oe-sem-no-events">
        <h3>No Events</h3>
        <div class="alert-box alert">
            There are currently no events for this patient.<br>Click the "Add Event" button to begin recording events.
        </div>
        <nav class="sidebar-header">
            <?php if ($this->checkAccess('OprnCreateEpisode')) { ?>
                <button id="add-event" class="button green add-event" type="button">Add Event</button>
            <?php } else { ?>
                <button class="button add-event disabled">You have View Only rights and cannot create events</button>
            <?php } ?>
        </nav>
    </div>
    <?php $this->renderPartial('//patient/add_new_event', array(
        'button_selector' => '#add-event',
        'episodes' => array(),
        'context_firm' => $this->firm,
        'patient_id' => $this->patient->id,
        'event_types' => EventType::model()->getEventTypeModules(),
    ));?>
<?php } else if ($allow_clinical) { ?>
    <nav class="event-header no-face">
        <i class="oe-i-e large i-Patient"></i>
        <h2 class="event-header-title">Patient Overview</h2>
        <?php $this->renderPartial('//patient/event_actions'); ?>
    </nav>

    <?php $this->beginContent('//patient/episodes_container', [
        'css_class' => isset($cssClass) ? $cssClass : '',
        'episode' => isset($current_episode) ? $current_episode : ''
    ]);

    ?>

    <div class="flex-layout flex-top">
        <div class="patient-overview">
            <?php
            $visualAcuityRight = $exam_api->getLetterVisualAcuityRight($patient);
            $visualAcuityLeft = $exam_api->getLetterVisualAcuityLeft($patient); ?>

            <table class="standard last-right">
                <tbody>

                <?php if ($visualAcuityLeft || $visualAcuityRight) {
                    $lDate = $exam_api->getLetterVisualAcuityDate($patient, 'left');
                    $rDate = $exam_api->getLetterVisualAcuityDate($patient, 'right');

                    if ($lDate == $rDate) { ?>
                        <tr>
                            <td>
                                <ul class="inline-list">
                                    <li>R <?= $visualAcuityRight ?: 'NA'; ?>
                                        <?= $visualAcuityRight ? $exam_api->getLetterVAMethodName($patient, 'right') : '' ?></li>
                                    <li>L <?= $visualAcuityLeft ?: 'NA' ?>
                                        <?= $visualAcuityLeft ? $exam_api->getLetterVAMethodName($patient, 'left') : '' ?></li>
                                </ul>
                            </td>
                            <td>
                                <small class="fade"><span class="oe-date"><?= Helper::convertDate2NHS($rDate); ?></span>
                                </small>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td>
                                R <?= $visualAcuityRight ?: 'NA'; ?>
                                <?= $visualAcuityRight ? $exam_api->getLetterVAMethodName($patient, 'right') : '' ?>
                            </td>
                            <td>
                                <small class="fade"><span
                                            class="oe-date"><?= $visualAcuityRight ? Helper::convertDate2NHS($rDate) : '' ?></span>
                                </small>
                            </td>
                        <tr>
                            <td>
                                L <?= $visualAcuityLeft ?: 'NA' ?>
                                <?= $visualAcuityLeft ? $exam_api->getLetterVAMethodName($patient, 'left') : '' ?>
                            </td>
                            <td>
                                <small class="fade"><span
                                            class="oe-date"><?= $visualAcuityLeft ? Helper::convertDate2NHS($lDate) : '' ?></span>
                                </small>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td>VA:</td>
                        <td>
                            <small class="fade">NA</small>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <?php
                    $leftRefraction = $correspondence_api->getLastRefraction($patient, 'left');
                    $rightRefraction = $correspondence_api->getLastRefraction($patient, 'right');
                    if ($leftRefraction !== null || $rightRefraction !== null) { ?>
                        <td>
                            <ul class="inline-list">
                                <li>R <?= $rightRefraction ?: 'NA' ?></li>
                                <li>L <?= $leftRefraction ?: 'NA' ?></li>
                            </ul>
                        </td>
                        <td>
                            <small class="fade"><span
                                        class="oe-date"><?= Helper::convertDate2NHS($correspondence_api->getLastRefractionDate($patient)) ?></span>
                            </small>
                        </td>
                    <?php } else { ?>
                        <td>Refraction:</td>
                        <td>
                            <small class="fade">NA</small>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <?php if ($patient->getCviSummary()[0] !== 'Unknown') { ?>
                        <td> CVI Status: <?= $patient->getCviSummary()[0]; ?> </td>
                        <td>
                            <small class="fade"><span
                                        class="oe-date"><?= $patient->getCviSummary()[1] && $patient->getCviSummary()[1] !== '0000-00-00' ? \Helper::convertDate2HTML($patient->getCviSummary()[1]) : 'N/A' ?></span>
                            </small>
                        </td>
                    <?php } else { ?>
                        <td>CVI Status:</td>
                        <td>
                            <small class="fade"><span class="oe-date">NA</span></small>
                        </td>

                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="patient-overview">
            <table class="standard">
                <tbody>
                <?php foreach ($events as $event) :
                    $event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/'; ?>
                    <tr>
                        <td>
                            <?= $event->getEventIcon() ?>
                        </td>
                        <td>
                            <a href="<?php echo $event_path . $event->id ?>"
                               data-id="<?php echo $event->id ?>"><?php echo $event->getEventName() ?></a>
                        </td>
                        <td><?= $event->user->title . " " . $event->user->first_name . " " . $event->user->last_name ?></td>
                        <td>
                            <small class="fade oe-date">
                                <?php if ($event->created_date !== $event->last_modified_date || $event->created_date !== $event->event_date) {
                                    echo 'Updated: '; ?>
                                    <span class="oe-date">
                                        <?= $event->NHSDateAsHTML('last_modified_date'); ?>
                                    </span>
                                <?php } else {
                                    echo 'Created: '; ?>
                                    <span class="oe-date">
                                        <?= $event->event_date ? $event->NHSDateAsHTML('event_date') : $event->NHSDateAsHTML('created_date') ?>
                                    </span>
                                <?php } ?>
                            </small>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex-layout flex-top col-gap-small">
        <div class="cols-half">
            <section class="element view full view-xxx" id="idg-ele-view-eye-diagnoses">
                <header class="element-header"><h3 class="element-title">Eye Diagnoses</h3></header>
                <div class="element-data full-width">
                    <div class="data-value">
                        <table>
                            <colgroup>
                                <col class="cols-8">
                                <col>
                            </colgroup>
                            <tbody>
                            <?php
                            $ophthalmic_diagnoses = $this->patient->getOphthalmicDiagnosesSummary();
                            if (count($ophthalmic_diagnoses) === 0) { ?>
                                <tr>
                                    <td>
                                        <div class="nil-recorded">Nil recorded</div>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php foreach ($ophthalmic_diagnoses as $ophthalmic_diagnosis) {
                                list($side, $name, $date, $event_id) = explode('~', $ophthalmic_diagnosis, 4); ?>
                                <tr>
                                    <td><strong><?= $name ?></strong></td>
                                    <td class="nowrap">
                                        <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                                        <span class="oe-date"><?= $date ?></span>
                                    </td>
                                    <td>
                                        <?php if (isset($event_id) && $event_id) { ?>
                                            <a href="/OphCiExamination/default/view/<?= $event_id ?>"><i class="oe-i direction-right-circle small pad"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <section class="element view full view-xxx" id="idg-ele-view-eye-procedures">
                <header class="element-header"><h3 class="element-title">Eye Procedures</h3></header>
                <div class="element-data full-width">
                    <div class="data-value">
                        <?php $this->widget(\OEModule\OphCiExamination\widgets\PastSurgery::class, array(
                            'patient' => $this->patient,
                            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                        )); ?>
                    </div>
                </div>
            </section>
            <section class="element view full view-xxx" id="idg-ele-view-eye-medications">
                <header class="element-header"><h3 class="element-title">Eye Medications</h3></header>
                <div class="element-data full-width">
                    <div class="data-value">
                        <?php $this->widget(\OEModule\OphCiExamination\widgets\HistoryMedications::class, array(
                            'patient' => $this->patient,
                            'mode' => BaseEventElementWidget::$PATIENT_LANDING_PAGE_MODE,
                        )); ?>
                    </div>
                </div>
            </section>
        </div>

        <div class="cols-half">
            <section class="element view full view-xxx" id="idg-ele-view-management-summaries">
                <header class="element-header"><h3 class="element-title">Management Summaries</h3></header>
                <div class="element-data full-width">
                    <table class="management-summaries">
                        <tbody>
                        <?php $summaries = $exam_api->getManagementSummaries($patient);
                        if (sizeof($summaries) != 0) {
                            foreach ($summaries as $summary) { ?>
                                <tr>
                                    <td><?= $summary->service ?></td>
                                    <td><?= $summary->comments ?></td>
                                    <td class="fade">
                                        <span class="oe-date">
                                            <span class="day"><?= $summary->date[0] ?></span>
                                            <span class="month"><?= $summary->date[1] ?></span>
                                            <span class="year"><?= $summary->date[2] ?></span>
                                        </span>
                                            </td>
                                            <td><i class="oe-i info small pro-left js-has-tooltip"
                                                         data-tooltip-content="<?= $summary->user ?>"></i></td>
                                        </tr>
                                    <?php }
                        } ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <section class="element view full view-xxx" id="idg-ele-view-appointments">
                        <header class="element-header"><h3 class="element-title">Appointments</h3></header>
                        <div class="element-data full-width">
                            <div class="data-value">
                                <?php $this->widget('Appointment', ['patient' => $this->patient]) ?>
                            </div>
                        </div>
                    </section>
                    <section class="element view full ">
                        <header class="element-header">
                            <h3 class="element-title">Problems &amp; Plans</h3>
                        </header>
                        <div class="element-data full-width">
                            <?php $this->widget('application.widgets.PlansProblemsWidget', ['allow_save' => false, 'patient_id' => $this->patient->id, 'is_popup' => false]); ?>
                        </div>
                    </section>
                </div>
            </div>
            <?php
            $this->endContent();
} else { ?>
    <main class="oe-home">
        <div class="oe-error-message">
            <div class="message">
                <h1>OpenEyes</h1>
                <h2>Forbidden</h2>
                <div class="alert-box error">
                    <strong>You do not have permission to access this page</strong>
                </div>          </div>
        </div>
    </main>
<?php }?>


