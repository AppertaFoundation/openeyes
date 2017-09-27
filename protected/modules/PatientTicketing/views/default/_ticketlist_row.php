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
?>

<?php $t_svc = Yii::app()->service->getService($this::$TICKET_SERVICE); ?>

<tr data-ticket-id="<?= $ticket->id?>" data-ticket-info="<?= CHtml::encode($ticket->getInfoData()) ?>">
	<td><?= $ticket->current_queue->name ?></td>
	<td><a href="<?= $ticket->getSourceLink() ?>"><?= $ticket->patient->hos_num.' - '
            .(($ticket->patient->nhs_num) ? $ticket->patient->nhs_num.' - ' : '')
            .$ticket->patient->getHSCICName()
            .' ('.($ticket->patient->isDeceased() ? 'Deceased' : $ticket->patient->getAge()).')'; ?></a></td>
	<td <?php if ($ticket->priority) {
    ?>style="color: <?= $ticket->priority->colour ?>"<?php 
}?>><?= $ticket->priority ? $ticket->priority->name : '-' ?></td>
	<td><?= Helper::convertDate2NHS($ticket->created_date)?></td>
	<td><?= $ticket->getTicketFirm() ?></td>
	<td><?= $ticket->user->getFullName() ?></td>
	<td><?= $ticket->report ? $ticket->report : '-'; ?></td>
	<td class="forceNoWrap"><?= nl2br($ticket->getNotes()) ?></td>
	<!-- Ownership functionality not required at the moment.
	<td><?= $ticket->assignee ? $ticket->assignee->getFullName() : '-'?></td>
	 -->
	<td class="actions">
		<?php
        if ($can_process) {
            if (!$ticket->is_complete()) {
                /*
                Ownership functionality is not required at the moment. It's expected that this will take place as
                part of the "move" functionality

                if ($ticket->assignee) {
                    if ($ticket->assignee_user_id == Yii::app()->user->id) {
                        ?><button id="release" class="tiny ticket-release">Release</button><?php
                    }
                }
                else {
                    ?><button id="take" class="tiny ticket-take">Take</button><?php
                }
                */
                ?>
				<a href="<?= Yii::app()->createURL('/PatientTicketing/default/startTicketProcess/', array('ticket_id' => $ticket->id)); ?>" class="button tiny"><?= $t_svc->getTicketActionLabel($ticket) ?></a>
		<?php }
        }?>
		<?php if ($ticket->hasHistory()) {?>
			<button class="tiny ticket-history">History</button>
			<?php if ($this->checkAccess('Patient Tickets admin')) {?>
				<button class="tiny undo-last-queue-step">Undo last step</button>
			<?php }?>
		<?php }?>
	</td>
</tr>
