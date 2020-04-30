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
$display_queue = $ticket->getDisplayQueueAssignment();
$incomplete_steps = [];
?>
<div class="element-data full-width">
    <?php foreach ($element->entries as $entry) { ?>
        <div class="flex-layout flex-top col-gap">
            <div class="cols-4">
                <table class="last-left">
                    <colgroup>
                        <col class="cols-4">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Priority</th>
                            <?php if ($entry->isPatientTicket() && $ticket && $ticket->priority) {?>
                                <td>
                                    <span class="highlighter <?= $ticket->priority->colour ?>"><?= $ticket->priority->name ?></span>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th>Review</th>
                            <td><?= $ticket->getDisplayQueue()->name . ' (' . Helper::convertDate2NHS($ticket->getDisplayQueueAssignment()->assignment_date) . ')' ?></td>
                        </tr>
                        <tr>
                            <th>Clinic Report</th>
                            <td><?= $display_queue->report ?></td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td><?= $display_queue->notes ?></td>
                        </tr>
                    </tbody>
                </table>
                <hr class="divider">
                <div class="oe-vc-mode in-element row">
                    <ul class="vc-steps">
                        <?php foreach ($ticket->getNearestQueuesInStepOrder(2) as $step => $queue) { ?>
                            <li class="<?= $queue->id === $ticket->current_queue->id ? 'selected' : '' ?>">
                                <?php if ($queue->id < $ticket->current_queue->id) {
                                    echo $step . '. <del>' . $queue->name . '</del>';
                                } else {
                                    echo $step . '. ' . $queue->name;
                                } ?>
                                <?php if ($queue->id <= $ticket->current_queue->id) {
                                    echo '(' . $queue->usermodified->getFullName() . ')';
                                } else {
                                    $incomplete_steps[$step] = $queue;
                                }?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="cols-7">
                <?php if ($ticket->hasHistory() || $ticket->hasRecordedQueueAssignments()) { ?>
                    <?php foreach ($ticket->queue_assignments as $step => $old_assignment) { ?>
                        <div class="collapse-data">
                            <div class="collapse-data-header-icon collapse">
                                <?php if ($old_assignment->queue->id < $ticket->current_queue->id) {
                                    echo $step + 1 . '. <del>' . $old_assignment->queue->name . '</del>';
                                } else {
                                    echo $step + 1 . '. ' . $old_assignment->queue->name;
                                } ?>
                                <?php if ($old_assignment->assignment_date) {
                                    echo Helper::convertDate2NHS($old_assignment->assignment_date);
                                } ?>
                                <?php if ($old_assignment->queue->id <= $ticket->current_queue->id) {
                                    echo '(' . $old_assignment->queue->usermodified->getFullName() . ')';
                                }?>
                            </div>
                            <div class="collapse-data-content" style="display: block">
                                <div class="vc-data">
                                    <div class="flex-layout flex-top flex-left col-gap">
                                        <div class="cols-7">
                                            <?= $old_assignment->report ?>
                                        </div>
                                        <div class="cols-5">
                                            <span class="user-comment">
                                                <i class="oe-i comments small-icon pad-right disabled"></i>
                                                <?= $old_assignment->notes ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php }  ?>
                <?php foreach ($incomplete_steps as $step => $queue) { ?>
                    <div class="collapse-data">
                        <div class="collapse-data-header-icon expand">
                            <?= $step  . '. ' . $queue->name ?> - <em class="fade">still to do</em>
                        </div>
                        <div class="collapse-data-content" style="display: none">
                            <div class="alert-box info">Virtual Clinic step not started yet</div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
