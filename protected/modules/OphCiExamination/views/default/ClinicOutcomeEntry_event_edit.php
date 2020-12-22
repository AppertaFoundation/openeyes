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

<?php
if (!isset($values)) {
    $values = array(
        'status_id' => $entry->status_id,
        'status' => $entry->getStatusLabel(),
        'followup_quantity' => $entry->followup_quantity ? $entry->followup_quantity : null,
        'followup_period_id' => $entry->followup_period_id,
        'followup_period' => $entry->getPeriodLabel(),
        'followup_comments' => $entry->followup_comments,
        'followup_comments_display' => $entry->getDisplayComments(),
        'role_id' => $entry->role_id,
        'role' => $entry->getRoleLabel(),
    );
}
?>

<tr id="<?= $model_name ?>_entries_<?= $row_count ?>" class="row-<?= $row_count ?>" data-key="<?= $row_count ?>"
    data-status="<?= $values['status_id'] ?>">
    <td <?= $patient_ticket ? 'style="vertical-align:top"' : '' ?>>
        <?php if ($entry->id) { ?>
        <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?= $entry->id?>" />
        <?php } ?>
        <input type="hidden" name="<?= $field_prefix ?>[status_id]" value="<?= $values['status_id'] ?>"/>
        <?php if (!$patient_ticket) { ?>
            <input type="hidden" name="<?= $field_prefix ?>[followup_quantity]"
                   value="<?= $values['followup_quantity'] ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[followup_period_id]"
                   value="<?= $values['followup_period_id'] ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[followup_comments]"
                   value="<?= $values['followup_comments'] ?>"/>
            <input type="hidden" name="<?= $field_prefix ?>[role_id]" value="<?= $values['role_id'] ?>"/>
        <?php } ?>
        <?= isset($condition_text) ? $condition_text : "{{condition_text}}"; ?>
    </td>
    <td>
        <?php if (!$patient_ticket) {
            echo $values['status'] . ' ' . $values['followup_quantity'] . ' ' . $values['followup_period'] . $values['role'] . $values['followup_comments_display'];
        } elseif ($patient_ticket && $ticket_api) { ?>
            <div data-queue-assignment-form-uri="<?= $ticket_api->getQueueAssignmentFormURI() ?>"
                 id="div_<?= $model_name ?>_patientticket">
                <!-- TODO, this should be pulled from the ticketing module somehow -->
                <?php if ($ticket) { ?>
                    <span class="field-info">Already Referred to Virtual Clinic:</span><br/>
                    <div class="row divider">
                        <?= $ticket->getDisplayQueue()->name . ' (' . Helper::convertDate2NHS($ticket->getDisplayQueueAssignment()->assignment_date) . ')' ?>
                    </div>
                    <h3>Clinic Info</h3>
                    <?= $ticket->report ? preg_replace('/^(<br \/>)/', '', $ticket->report) : '-'; ?>
                    <input type="hidden" name="patientticket_queue" value="<?= $ticket->current_queue->id ?>"/>
                    <input type="hidden" name="patientticketing__priority" value="<?= $ticket->priority_id ?>"/>
                <?php } else { ?>
                    <fieldset class="flex-layout">
                        Virtual Clinic:
                        <div class="cols-3">
                            <?php if (count($queues) === 0) { ?>
                                <span>No valid Virtual Clinics available</span>
                            <?php } elseif (count($queues) === 1) {
                                echo reset($queues);
                                $qid = key($queues);
                                $_POST['patientticket_queue'] = $qid;
                                ?>
                                <input type="hidden" name="patientticket_queue" value="<?= $qid ?>"/>
                            <?php } else {
                                echo CHtml::dropDownList(
                                    'patientticket_queue',
                                    \Yii::app()->request->getParam('patientticket_queue', ''),
                                    $queues,
                                    array('empty' => 'Select', 'nowrapper' => true, 'options' => array())
                                );
                            } ?>
                        </div>
                        <div class="cols-1">
                            <i class="oe-i spinner" style="display: none;"></i>
                        </div>
                    </fieldset>
                    <div id="queue-assignment-placeholder">
                        <?php if (isset($_POST['patientticket_queue']) && !empty($_POST['patientticket_queue'])) {
                            $this->widget(
                                $ticket_api::$QUEUE_ASSIGNMENT_WIDGET,
                                array('queue_id' => $_POST['patientticket_queue'], 'label_width' => 3, 'data_width' => 5)
                            );
                        } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </td>
    <td style="vertical-align:top">
        <i class="oe-i trash"></i>
    </td>
</tr>
