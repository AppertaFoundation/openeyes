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
<?php
$row_count = 0;
$api = Yii::app()->moduleAPI->get('PatientTicketing');
$ticket = $api->getTicketForEvent($this->event);
$display_queue_assignment = $ticket ? $ticket->getDisplayQueueAssignment() : null;
$incomplete_steps = [];
$complete_or_current_steps_keys = [];
$queue_set_service = Yii::app()->service->getService('PatientTicketing_QueueSet');
$ticket_entries = [];
$non_ticket_entries = [];
?>
<div class="element-data full-width">
    <?php foreach ($element->entries as $entry) {
        if ($entry->isPatientTicket() && $ticket) {
            $ticket_entries[] = $entry;
        } else {
            $non_ticket_entries[] = $entry;
        }
    } ?>
    <?php if ($non_ticket_entries) { ?>
        <div class="cols-10">
            <table class="last-left large-text">
                <colgroup>
                    <col class="cols-1">
                </colgroup>
                <tbody>
                <?php foreach ($non_ticket_entries as $entry) { ?>
                    <tr>
                        <td><?= $row_count ? 'AND' : '' ?></td>
                        <td><?= $entry->getInfos(); ?></td>
                    </tr>
                    <?php $row_count++; ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    <?php if ($non_ticket_entries && $ticket_entries) { ?>
        <hr class="divider">
    <?php } ?>
    <?php foreach ($ticket_entries as $entry) { ?>
    <div class="flex-layout flex-top col-gap">
        <div class="cols-5">
            <table class="last-left">
                <colgroup>
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                    <th>Priority</th>
                    <?php if ($ticket->priority) { ?>
                        <td>
                            <span class="highlighter <?= $ticket->priority->colour ?>"><?= $ticket->priority->name ?></span>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <th>State</th>
                    <td><?= $ticket->getDisplayQueue()->name . ' (' . Helper::convertDate2NHS($display_queue_assignment->assignment_date) . ')' ?></td>
                </tr>
                <tr>
                    <th>Virtual Clinic</th>
                    <td><?= $queue_set_service->getQueueSetForQueue($ticket->current_queue->id)->name ?></td>
                </tr>
                </tbody>
            </table>
            <hr class="divider">
            <div class="oe-vc-mode in-element row">
                <ul class="vc-steps">
                    <?php foreach ($ticket->getNearestQueuesInStepOrder(2) as $step => $queue) {
                        $is_completed = $queue->id <= $ticket->current_queue->id;
                        $is_current = $queue->id === $ticket->current_queue->id; ?>
                        <?php if ($is_completed) {
                            $complete_or_current_steps_keys[$queue->id] = $step;
                            $queue_assignment = \OEModule\PatientTicketing\models\TicketQueueAssignment::model()->findByAttributes(['ticket_id' => $ticket->id, 'queue_id' => $queue->id]) ?>
                            <li class="completed">
                                <em><?= $queue_assignment->assignment_user->getFullName() ?></em>
                            </li>
                        <?php } else {
                            $incomplete_steps[$step] = $queue;
                        } ?>
                        <li class="<?= $is_current ? 'selected' : ($is_completed ? 'completed' : '') ?>">
                            <?= $step . '. ' . $queue->name; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="cols-7">
            <?php if ($ticket->hasHistory() || $ticket->hasRecordedQueueAssignments()) { ?>
                <?php foreach ($ticket->queue_assignments as $step => $old_assignment) {
                    $is_current_queue = $old_assignment->queue->id === $ticket->current_queue->id;
                    ?>
                    <div class="collapse-data">
                        <div class="collapse-data-header-icon <?= $is_current_queue ? 'collapse' : 'expand' ?>">
                            <?php echo $complete_or_current_steps_keys[$old_assignment->queue->id] . '. ' . $old_assignment->queue->name . ' -'; ?>
                            <?php if ($old_assignment->assignment_date) {
                                echo Helper::convertDate2NHS($old_assignment->assignment_date);
                            } ?>
                            <?php if ($old_assignment->queue->id <= $ticket->current_queue->id) {
                                echo '(' . $old_assignment->assignment_user->getFullName() . ')';
                            } ?>
                        </div>
                        <div class="collapse-data-content" style="display: <?= $is_current_queue ? 'block' : 'none' ?>">
                            <div class="vc-data">
                                <div class="flex-layout flex-top flex-left col-gap">
                                    <div class="cols-8">
                                        <?= $old_assignment->report ?>
                                    </div>
                                    <div class="cols-4">
                                            <span class="user-comment">
                                                <?php if ($old_assignment->notes) { ?>
                                                    <i class="oe-i comments small pad-right disabled"></i><br/>
                                                    <?= $old_assignment->notes ?>
                                                <?php } ?>
                                            </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php foreach ($incomplete_steps as $step => $queue) { ?>
                <div class="collapse-data">
                    <div class="collapse-data-header-icon expand">
                        <?= $step . '. ' . $queue->name ?> - <em class="fade">still to do</em>
                    </div>
                    <div class="collapse-data-content" style="display: none">
                        <div class="alert-box info">Virtual Clinic step not started yet</div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
    <hr class="divider">
    <table class="last-left large-text">
        <colgroup>
            <col class="cols-1">
        </colgroup>
        <tr>
            <td>Comments</td>
            <td><?= $element->comments ? $element->comments : '<span class="none">None</span>' ?></td>
        </tr>
    </table>
</div>
