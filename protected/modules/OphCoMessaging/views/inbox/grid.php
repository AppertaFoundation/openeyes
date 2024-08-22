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
 *
 * @var CActiveDataProvider $messages
 */

use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;

$sort_field = 'send_date';
$sort_direction = 'ascend';
$sort_uri = $mailbox ? "?mailbox={$mailbox->id}&messages={$message_type}&sort=" : "?messages={$message_type}&sort=";
$default_sort_arg = '.desc';

if (isset($_GET['sort'])) {
    if (!strpos($_GET['sort'], '.desc')) {
        $sort_field = str_replace('.asc', '', $_GET['sort']);
        $sort_direction = 'ascend';
    } else {
        $sort_field = str_replace('.desc', '', $_GET['sort']);
        $sort_direction = 'descend';
    }
}

$date_class = 'column-sort' . ($sort_field === 'send_date' ? " $sort_direction active" : '');
$user_class = 'column-sort' . ($sort_field === 'sender_mailbox_name' ? " $sort_direction active" : '');

$date_sort_uri = $sort_uri . 'send_date';
if ($sort_field === 'send_date') {
    if (isset($_GET['sort']) && !strpos($_GET['sort'], '.desc')) {
        $date_sort_uri .= '.desc';
    }
} else {
    $date_sort_uri .= $default_sort_arg;
}

$user_sort_uri = $sort_uri . 'sender_mailbox_name';
if ($sort_field === 'sender_mailbox_name') {
    if (isset($_GET['sort']) && !strpos($_GET['sort'], '.desc')) {
        $user_sort_uri .= '.desc';
    }
} else {
    $user_sort_uri .= $default_sort_arg;
}

$message_data = $recipient_messages->getData();
$recipient_messages->setTotalItemCount(isset($message_data[0]) ? $message_data[0]['total_message_count'] : 0);
$recipient_messages->pagination->itemCount = $recipient_messages->getTotalItemCount();

// if $recipient_messages is empty, show a "no messages" message
if (!$recipient_messages->totalItemCount) {
    ?>
    <div class="alert-box info strong">
        <b>No messages in this folder.</b>
    </div>
    <?php
    return;
}
?>


<table class="standard messages highlight-rows" data-test="messages-table">
    <colgroup>
        <col class="cols-4">
        <col class="cols-icon">
        <col class="cols-1">
        <col class="cols-1">
        <col class="cols-3">
        <col class="cols-3">
        <col class="cols-1">
    </colgroup>
    <thead>
        <tr>
            <th>Patient</th>
            <th></th>
            <th>Type</th>
            <th>
                <a href="<?= $date_sort_uri ?>" class="<?= $date_class ?>">Date</a>
            </th>
            <th>
                <a href="<?= $user_sort_uri ?>" class="<?= $user_class ?>">Sender</a>
            </th>
            <th colspan="2">
                <!-- Pagination -->
                <?php $this->widget(
                    'LinkPager',
                    ['pages' => $recipient_messages->pagination]
                ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($message_data as $message) {
            $message_recipient = $message['recipient_mailbox_name'];
            $message_text = $message['display_text'];
            $message_date = $message['send_date'];
            $message_object = Element_OphCoMessaging_Message::model()->findByPk($message['element_id']);
            $message_sender = $message['sender_mailbox_personal'] ?
                $message['sender_mailbox_name'] :
                "{$message['sender_title']} {$message['sender_first_name']} {$message['sender_last_name']} (via {$message['sender_mailbox_name']})";

            $patient = Patient::model()->findByPk($message['patient_id']);

            $link_url = Yii::app()->createURL("/OphCoMessaging/default/view/", array("id" => $message['event_id'], "mailbox_id" => $message['user_mailbox_id']));
            ?>
        <tr class="<?= ($mailbox && $message['marked_as_read_by_user'] ? 'read' : 'unread') ?>" data-event-id="<?= $message['event_id'] ?>" data-mailbox-id="<?=$message['user_mailbox_id']?>">
            <td>
            <?php $this->renderPartial(
                'application.widgets.views.PatientMeta',
                array('patient' => $patient, 'coreapi' => $coreapi, 'link_url' => $link_url)
            ); ?>
            </td>
            <td class="urgent-status nowrap">
                <?php if (!$message['user_mailbox_personal']) { ?>
                    <i class="oe-i team small pad active js-has-tooltip" data-tooltip-content="Group message: <?= $message_recipient ?>"></i>
                <?php } ?>
                <?php if ($message['urgent']) { ?>
                    <i class="oe-i status-urgent small js-has-tooltip" data-tooltip-content="Urgent!"></i>
                <?php } ?>
                <?php
                if ($message['reply_required']) {
                    if (!empty($message['latest_comment_id'])) {
                        echo '<i class="oe-i status-query-reply small js-has-tooltip" data-tt-type="basic" data-tooltip-content="Query reply" data-test="home-mailbox-message-reply-required"></i>';
                    } else {
                        echo '<i class="oe-i status-query small js-has-tooltip" data-tt-type="basic" data-tooltip-content="Query" data-test="home-mailbox-message-reply-required"></i>';
                    }
                }
                ?>
            </td>
            <td class="message-status nowrap" data-test="home-mailbox-message-sub-type">
                <?php
                if (!($message['user_primary_recipient'] || $message['user_original_sender'])) {
                    echo '<i class="oe-i duplicate small pad-right no-click"></i>';
                }
                if ($message['message_type_name'] === 'Query') {
                    echo 'Qry.';
                } else {
                    echo substr($message['message_type_name'], 0, 3) . ".";
                }
                ?>
            </td>
            <td><?= Helper::convertMySQL2NHS($message_date) ?></td>
            <td data-test="message-sender">
                <?= $message_sender ?>
                <div class="sent-to-mailbox" data-test="message-recipient">
                    <?= $message_recipient ?>
                </div>
            </td>
            <td class="js-message">
                <div class="message" data-test="home-mailbox-message-text">
                <?= Yii::app()->format->text(str_replace("\n", " // ", rtrim($message_text))) ?>
                    <div class="expand-message">
                        <i class="oe-i expand small js-expand-message"></i>
                    </div>
                </div>
            </td>
            <td class="nowrap">
                <?php
                if (!$message['marked_as_read_by_user']) {
                    if ($message['user_mailbox_personal']) : ?>
                        <i class="oe-i save small pad js-has-tooltip js-mark-as-read-btn" data-tooltip-content="Mark as read" data-test="mark-as-read-btn"></i>
                    <?php else : ?>
                        <i class="oe-i save-team small pad js-has-tooltip js-mark-as-read-btn" data-tooltip-content="Mark as read for all team members" data-test="mark-as-read-btn"></i>
                    <?php endif;
                } ?>
                <a href="<?= $link_url ?>">
                    <i class="oe-i direction-right-circle small pad"></i>
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
