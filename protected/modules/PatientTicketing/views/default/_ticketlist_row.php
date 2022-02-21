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

<?php $t_svc = Yii::app()->service->getService($this::$TICKET_SERVICE);
$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];?>
<tr data-ticket-id="<?= $ticket->id ?>" data-ticket-info="<?= CHtml::encode($ticket->getInfoData()) ?>">
    <td><?= $ticket->current_queue->name ?></td>
    <td><a href="<?= $ticket->getSourceLink() ?>">

            <?= $ticket->patient->getHSCICName() ?>
            <small>(<?php echo($ticket->patient->isDeceased() ? 'Deceased' : $ticket->patient->getAge()); ?>)
            </small>
            <br/>
            <small>
                <?php $display_primary_number_usage_code = Yii::app()->params['display_primary_number_usage_code'];
                $display_secondary_number_usage_code = Yii::app()->params['display_secondary_number_usage_code'];
                $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $ticket->patient->id, $institution->id, $selected_site_id); ?>
                <span class="fade"><?= PatientIdentifierHelper::getIdentifierPrompt($primary_identifier); ?></span>
                <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier); ?>
                <?php $this->widget(
                    'application.widgets.PatientIdentifiers',
                    [
                        'patient' => $ticket->patient,
                        'show_all' => true,
                        'tooltip_size' => 'small'
                    ]); ?>
            </small>
            <br/>
            <small>
                <?php $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $ticket->patient->id, $institution->id, $selected_site_id); ?>
                <span class="fade"><?= PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier); ?></span>
                <?= PatientIdentifierHelper::getIdentifierValue($secondary_identifier); ?>
            </small>
        </a>
    </td>
    <td>
        <i class="oe-i circle-<?php echo $ticket->priority ? $ticket->priority->colour : '' ?> small pad selected"></i>
    </td>
    <td><span class="oe-date"><?= Helper::convertMySQL2HTML($ticket->created_date) ?></span></td>
    <td>
        <?php $ticket_context = $ticket->getTicketFirm(); ?>
        <?= $ticket->getTicketFirm() ?><br>
        <small class="fade"><?= $ticket->user->getFullName() ?></small>
    </td>
    <td>
        <div class="clinic-info scroll-content">
        <?= $ticket->report ? preg_replace('/^(<br \/>)/', '', $ticket->formattedReport) : '-'; ?>
        </div>
    </td>
    <td>
        <div class="scroll-content">
            <?= nl2br($ticket->getNotes()) ?>
        </div>
    </td>
    <td class="actions">
        <ul>
            <?php if ($can_process && !$ticket->is_complete()) : ?>
                <li>
                    <a class="button blue hint"
                       href="<?= Yii::app()->createURL('/PatientTicketing/default/startTicketProcess/', array(
                           'ticket_id' => $ticket->id,
                       )); ?>">
                        <?= $t_svc->getTicketActionLabel($ticket) ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($ticket->hasHistory()) : ?>
                <li class="button ticket-history">History</li>
                <?php if ($this->checkAccess('Patient Tickets admin')) : ?>
                    <li class="button undo-last-queue-step">Undo last step</li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </td>
</tr>
