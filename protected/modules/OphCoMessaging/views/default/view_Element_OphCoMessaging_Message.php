<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
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

use OEModule\OphCoMessaging\models\Mailbox;

?>

<?php
if (!isset($new_comment)) {
    // ensure we have base comment object
    $new_comment = new \OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment();
}

$mailbox = 
    Mailbox::model()->findByPk(\Yii::app()->request->getParam('mailbox_id')) ?? 
    \User::model()->findByPk(\Yii::app()->user->id)->personalMailbox;
?>

<div class="element-data full-width">
    <div class="flex-t">
        <div class="cols-5">
            <table>
                <colgroup>
                    <col class="cols-5" />
                </colgroup>
                <tbody>
                    <tr>
                        <th>Sender</th>
                        <td>
                            <span class="priority-text"><?= $element->sender->name; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Date sent</th>
                        <td>
                            <?= Helper::convertDate2NHS($element->event->event_date) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Recipient</th>
                        <td data-test="message-primary-recipient-mailbox-name">
                            <?= $element->for_the_attention_of->mailbox->name ?></div>
                        </td>
                    </tr>
                    <?php if ($element->cc_enabled) { ?>
                    <tr>
                        <th>CC'd</th>
                        <td data-test="message-cc-recipient-mailbox-names">
                            <?php
                                $cc_names = [];

                            foreach ($element->cc_recipients as $recipient) {
                                $cc_prefix = $recipient->mailbox->is_personal ? '' : '<i class="oe-i team medium pad-r no-click"></i>';

                                $cc_names[] = $cc_prefix . $recipient->formattedName();
                            }

                                echo implode(', ', $cc_names);
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                <td><?=\CHtml::encode($element->getAttributeLabel('message_type_id')) ?></td>
                <td data-test="message-type"><?= $element->message_type ? $element->message_type->name : 'None' ?></td>
            </tr>
                    
                    <?php if ($element->urgent) { ?>
                    <tr>
                        <th>Priority</th>
                        <td>
                            <i class="oe-i status-urgent no-hover small pad-right"></i>
                            <span class="highlighter orange" data-test="message-urgent-indicator">Urgent message</span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="cols-6">
            <div class="msg-reader">
                <div class="missive">
                    <?= Yii::app()->format->Ntext(preg_replace("/\n/", "", preg_replace('/(\s{4})\s+/', '$1', $element->message_text))) ?>
                    <div class="read-status" data-test="read-status">
                        <?= count($element->read_by_recipients) === 0 ? 'Unread' : 'Read by: ' . $element->getReadByLine() ?>
                    </div>
                </div>
                <?php if (empty($element->comments) && $this->canMarkMessageRead($element, $mailbox)) { ?>
                <div class="change-msg-status">
                    <a class="button" href="<?= Yii::app()->createUrl("{$this->getModule()->name}/Default/markRead/?id={$this->event->id}&mailbox_id={$mailbox->id}") ?>" data-test="mark-as-read">
                        <i class="oe-i save small pad-r"></i>
                        Mark message as read for <?= $mailbox->name ?>
                    </a>
                </div>
                <?php } elseif (empty($element->comments) && $this->canMarkMessageUnread($element, $mailbox)) { ?>
                <div class="change-msg-status">
                    <a class="button" href="<?= Yii::app()->createUrl("{$this->getModule()->name}/Default/markUnread/?id={$this->event->id}&mailbox_id={$mailbox->id}") ?>" data-test="mark-as-unread">
                        <i class="oe-i save small pad-r"></i>
                        Mark message as unread for <?= $mailbox->name ?>
                    </a>
                </div>
                <?php } ?>
            </div>

            <?php foreach ($element->comments as $comment) {
                    $sender_mailbox = $comment->sender_mailbox;
                    $reply_sender_label = $comment->user->getFullName();
                if (isset($sender_mailbox) && !$sender_mailbox->is_personal) {
                    $reply_sender_label .= " (via {$sender_mailbox->name})";
                }

                $is_latest_comment = (int) $comment->id === (int) $element->last_comment->id;
                ?>
            <div class="msg-reply">
                <?= $reply_sender_label ?>, <?= Helper::convertMySQL2NHS($comment->created_date) ?>
            </div>
            <div class="msg-reader">
                <div class="missive">
                <?= Yii::app()->format->Ntext(preg_replace("/\n/", "", preg_replace('/(\s{4})\s+/', '$1', $comment->comment_text))) ?>
                    <div class="read-status" data-test="read-status"><?= $comment->marked_as_read ? ('Read by: ' . $comment->usermodified->getFullName()) : 'Unread' ?></div>
                </div>
                <?php if ($is_latest_comment && $this->canMarkMessageRead($element, $mailbox)) { ?>
                    <div class="change-msg-status">
                        <a class="button" href="<?= Yii::app()->createUrl("{$this->getModule()->name}/Default/markRead/?id={$this->event->id}&mailbox_id={$mailbox->id}") ?>" data-test="mark-as-read">
                            <i class="oe-i save small pad-r"></i>Mark message as read for <?= $mailbox->name ?>
                        </a>
                    </div>
                <?php } elseif ($is_latest_comment && $this->canMarkMessageUnread($element, $mailbox)) { ?>
                    <div class="change-msg-status">
                        <a class="button" href="<?= Yii::app()->createUrl("{$this->getModule()->name}/Default/markUnread/?id={$this->event->id}&mailbox_id={$mailbox->id}") ?>" data-test="mark-as-unread">
                            <i class="oe-i save small pad-r"></i>Mark message as unread for <?= $mailbox->name ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <?php } ?>

            <?php if ($this->canComment()) { ?>
                <?php
                $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                    'id' => 'comment-form',
                    'action' => Yii::app()->createUrl('/' . $this->getModule()->name . '/Default/AddComment', array('id' => $this->event->id)),
                    'enableAjaxValidation' => false,
                    'layoutColumns' => array(
                        'label' => 2,
                        'field' => 10,
                    ),
                )); ?>
                <hr class="divider" />
                <div class="msg-reply">Your reply … <small>(can not be edited once sent)</small>
                <div class="reply-mailbox">Replying as 
                    <?=
                        \CHtml::dropDownList(
                            'mailbox_id',
                            isset($mailbox) ? $mailbox->id : null,
                            \CHtml::listData(
                                array_merge(
                                    Mailbox::model()->forUser(\Yii::app()->user->id)->forMessageSender($element->id)->findAll(),
                                    Mailbox::model()->forUser(\Yii::app()->user->id)->forMessageRecipients($element->id)->findAll()
                                ),
                                'id',
                                'name'
                            ),
                            []
                        );
                    ?>
                </div>
                <div class="highlighter small-row">
                    <b>Messages are part of the patient record and cannot be edited once sent.</b>
                </div>
                <div class="msg-editor">
                    <?=
                        \CHtml::activeTextArea(
                            $new_comment,
                            'comment_text',
                            [
                                'class' => 'cols-full increase-text autosize msg-write js-editor-area',
                                'placeholder' => 'Your reply...',
                                'rows' => 1,
                                'data-test' => 'your-reply'
                            ]
                        )
                    ?>
                    <div class="msg-preview js-preview-area" style="display: none"></div>
                    <div class="msg-actions js-preview-action">
                        <button class="blue hint js-preview-message" type="button" data-test="preview-and-check">Preview & check</button>
                    </div>
                    <div class="msg-actions js-edit-or-send-actions" style="display: none">
                        <button class="blue hint js-edit-message" type="button">Edit message</button>
                        <button class="green hint" type="submit" data-test="send-reply">Send message</button>
                        </div>
                    </div>
                </div>
                <?php $this->endWidget() ?>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    function splitLinesIntoBRsIn(intoContainer, text)
    {
        const lines = text.split('\n');

        intoContainer.empty();

        for (line of lines) {
        intoContainer.append(document.createTextNode(line));
        intoContainer.append('<br />');
        }
    }

    $(document).ready(function() {
        $('.js-preview-action').click(function() {
            splitLinesIntoBRsIn($('.js-preview-area'), $('.js-editor-area').val())

            $('.js-preview-action, .js-editor-area').hide();
            $('.js-edit-or-send-actions, .js-preview-area').show();
    });

        $('.js-edit-message').click(function() {
            $('.js-preview-action, .js-editor-area').show();
            $('.js-edit-or-send-actions, .js-preview-area').hide();
        });
     });
</script>
