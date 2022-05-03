<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var \OEModule\PatientTicketing\models\Ticket $ticket
 *
 * @var \OEModule\PatientTicketing\services\PatientTicketing_TicketService $t_svc
 */
?>

<?php
    $t_svc = Yii::app()->service->getService($this::$TICKET_SERVICE);
    $institution = Institution::model()->getCurrent();
    $selected_site_id = Yii::app()->session['selected_site_id'];

    $display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
    $display_secondary_number_usage_code = SettingMetadata::model()->getSetting('display_secondary_number_usage_code');
    $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $ticket->patient->id, $institution->id, $selected_site_id);
    $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $ticket->patient->id, $institution->id, $selected_site_id);
?>

<tr class="divider" data-ticket-id="<?= $ticket->id ?>" data-ticket-info="<?= CHtml::encode($ticket->getInfoData()) ?>">
    <td>
        <i class="oe-i circle-<?php echo $ticket->priority ? $ticket->priority->colour : '' ?> small pad selected"></i>
    </td>
    <td><?= $ticket->current_queue->name?></td>
    <td>
        <div class="oe-patient-meta">
            <div class="patient-name">
                <a href="<?= $ticket->getSourceLink() ?>"><?= $ticket->patient->getHSCICName() ?></a>
            </div>
            <div class="patient-details">
                <div class="hospital-number">
                    <span><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier); ?></span>
                    <a href="<?= $ticket->getSourceLink() ?>">
                        <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier); ?>
                    </a>
                </div>
                <div class="nhs-number">
                    <span><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier); ?></span>
                    <?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier); ?>
                </div>
            </div>
            <div class="patient-gender">
                <em>Gen</em>
                <?=$ticket->patient->getGenderString();?>
            </div>
            <div class="patient-age">
                <em>Age</em>
                <?=($ticket->patient->isDeceased() ? 'Deceased' : $ticket->patient->getAge()); ?>
            </div>
        </div>
    </td>
    <td>
        <div class="small-row">
            <?=$ticket->current_queue_assignment->consultant->fullNameAndTitle ?? 'Consultant not recorded'; /* probably older events */?><br>
        </div>
        <div class="small-row"><?= Helper::convertDate2NHS($ticket->created_date) ?><br>
        <div class="small-row">
            <small><?= \CHtml::encode($ticket->event->site->name ?? 'Site not recorded'); ?></small>
    </td>
    <td>
        <div class="flex-t col-gap">
            <div class="clinic-info scroll-content flex-fill-2">
                <?= $ticket->report ? preg_replace('/^(<br \/>)/', '', $ticket->formattedReport) : '-'; ?>
            </div>
            <div class="scroll-content flex-fill">
                <em><?= nl2br($ticket->getNotes()) ?></em>
            </div>
        </div>
        <div class="actions small-row">
            <?php if ($can_process && !$ticket->is_complete()) : ?>
                    <a class="button blue hint"
                       href="<?= Yii::app()->createURL('/PatientTicketing/default/startTicketProcess/', array(
                           'ticket_id' => $ticket->id,
                       )); ?>">
                        <?= $t_svc->getTicketActionLabel($ticket) ?>
                    </a>
            <?php endif; ?>

            <?php if ($ticket->hasHistory()) : ?>
                <button class="blue hint js-ticket-history" type="button">History</button>
                <?php if ($this->checkAccess('Patient Tickets admin')) : ?>
                    <button class="blue hint js-undo-last-queue-step" type="button">Undo last step</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </td>
</tr>
