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

$sort_field = 'event_date';
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

$date_class = 'column-sort' . ($sort_field === 'event_date' ? " $sort_direction active" : '');
$user_class = 'column-sort' . ($sort_field === 'user' ? " $sort_direction active" : '');

$date_sort_uri = $sort_uri . 'event_date';
if ($sort_field === 'event_date') {
    if (isset($_GET['sort']) && !strpos($_GET['sort'], '.desc')) {
        $date_sort_uri .= '.desc';
    } else {
        $date_sort_uri .= '.asc';
    }
} else {
    $date_sort_uri .= $default_sort_arg;
}

$user_sort_uri = $sort_uri . 'user';
if ($sort_field === 'user') {
    if (isset($_GET['sort']) && !strpos($_GET['sort'], '.desc')) {
        $user_sort_uri .= '.desc';
    } else {
        $user_sort_uri .= '.asc';
    }
} else {
    $user_sort_uri .= $default_sort_arg;
}

// if $messages is empty, show a "no sessages" message
if (!$messages->totalItemCount) {
    ?>
    <div class="alert-box info strong">
        <b>No messages in this folder.</b>
    </div>
    <?php
    return;
}
?>


<table class="standard messages highlight-rows">
    <colgroup>
        <col class="cols-4">
        <col class="cols-icon">
        <col class="cols-1">
        <col class="cols-1">
        <col class="cols-2">
        <col class="cols-4">
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
                    ['pages' => $messages->pagination]
                ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($messages->getData() as $message) {
            /**
             * @var Element_OphCoMessaging_Message $message
             */
            if ($defer_to_comments && isset($message->last_comment)) {
                $comment = $message->last_comment;

                $message_date = $comment->created_date;
                $message_text = $comment->comment_text;
                $message_sender = $comment->user->getFullName();
                $comment_count = count($message->comments);

                // If there is more than one comment, the recipient of the latest reply
                // will be the user who wrote the second to latest reply.
                // Otherwise if there is only one comment, it can only be a reply to the original sender.
                $message_recipient = $comment_count > 1
                                   ? $message->comments[$comment_count - 2]->user->getFullName()
                                   : $message->sender->name;
            } else {
                $message_date = $message->event->event_date;
                $message_text = $message->message_text;
                $message_sender = $message->sender->name;
                $message_recipient = $message->for_the_attention_of->mailbox->name;
            }

            $link_url = Yii::app()->createURL("/OphCoMessaging/default/view/", array("id" => $message->event_id));
            ?>
        <tr class="<?= ($mailbox && $message->getMarkedRead($mailbox, \Yii::app()->user)) ? 'read' : 'unread' ?>" data-event-id="<?= $message->event_id ?>">
            <td>
            <?php $this->renderPartial(
                'application.widgets.views.PatientMeta',
                array('patient' => $message->event->episode->patient, 'coreapi' => $coreapi, 'link_url' => $link_url)
            ); ?>
            </td>
            <td class="urgent-status nowrap">
                <?php if (!$message->for_the_attention_of->mailbox->is_personal) { ?>
                    <i class="oe-i team small pad active js-has-tooltip" data-tooltip-content="Group message: <?= $message_recipient ?>"></i>
                <?php } ?>
                <?php if ($message->urgent) { ?>
                    <i class="oe-i status-urgent small js-has-tooltip" data-tooltip-content="Urgent!"></i>
                <?php } ?>
                <?php
                if ($message->message_type->reply_required) {
                    if (isset($message->last_comment)) {
                        echo '<i class="oe-i status-query-reply small js-has-tooltip" data-tt-type="basic" data-tooltip-content="Query reply" data-test="home-mailbox-message-reply-required"></i>';
                    } else {
                        echo '<i class="oe-i status-query small js-has-tooltip" data-tt-type="basic" data-tooltip-content="Query" data-test="home-mailbox-message-reply-required"></i>';
                    }
                }
                ?>
            </td>
            <td class="message-status nowrap" data-test="home-mailbox-message-sub-type">
                <?php if (count($message->cc_recipients) > 1) {
                    $copied_users = $message->cc_recipients;
                    foreach ($copied_users as $copied_user) {
                        if ($mailbox && $copied_user->mailbox_id != $message->for_the_attention_of && $copied_user->mailbox_id == $mailbox->id) {
                            echo '<i class="oe-i duplicate small pad-right no-click"></i>';
                        }
                    }
                }
                if ($message->message_type->name === 'Query') {
                    echo 'Qry.';
                } else {
                    echo substr(\OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType::model()->findByPk($message->message_type_id)->name, 0, 3) . ".";
                }
                ?>
            </td>
            <td><?= Helper::convertMySQL2NHS($message_date) ?></td>
            <td>
                <?= $message_sender ?>
                <div class="sent-to-mailbox">
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
                $copied_users = $message->recipients;
                $cc_is_unread = false;
                foreach ($copied_users as $copied_user) {
                    if ($copied_user->marked_as_read === '0') {
                        $cc_is_unread = true;
                        break;
                    }
                }
                if (
                    $cc_is_unread || (
                        isset($message->last_comment)
                        && $message->last_comment->marked_as_read === '0'
                        && $message->last_comment->created_user_id != \Yii::app()->user->id
                    )
                ) {
                    if ($mailbox) {
                        if ($mailbox->is_personal) { ?>
                            <i class="oe-i save small pad js-has-tooltip js-mark-as-read-btn" data-tooltip-content="Mark as read"></i>
                        <?php } else { ?>
                            <i class="oe-i save-team small pad js-has-tooltip js-mark-as-read-btn" data-tooltip-content="Mark as read for all team members"></i>
                        <?php }
                    }
                } ?>
                <a href="<?= $link_url ?>">
                    <i class="oe-i direction-right-circle small pad"></i>
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
