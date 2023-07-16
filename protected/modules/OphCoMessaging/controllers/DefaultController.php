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

namespace OEModule\OphCoMessaging\controllers;

use OELog;
use OEModule\OphCoMessaging\components\MailboxSearch;
use OEModule\OphCoMessaging\components\OphCoMessaging_API;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;
use User;

class DefaultController extends \BaseEventTypeController
{
    private const ACTION_TYPE_MYMESSAGE = 'ManageMyMessage';
    private const ACTION_TYPE_MARKMESSAGE = 'MarkMyMessage';

    protected static $action_types = array(
        'markread' => self::ACTION_TYPE_MARKMESSAGE,
        'markunread' => self::ACTION_TYPE_MARKMESSAGE,
        'addcomment' => self::ACTION_TYPE_MYMESSAGE,
        'checkuseroutofoffice' => self::ACTION_TYPE_CREATE,
        'autocompleteMailbox' => self::ACTION_TYPE_FORM,
    );

    /**
     * @var \OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message
     */
    protected $message_el;

    /**
     * @var bool
     */
    public $show_comment_form = false;

    /**
     * Make sure user has clinical access and the user is the recipient of the message.
     *
     * @return bool
     */
    public function checkManageMyMessageAccess()
    {
        return $this->checkAccess('OprnViewClinical') && ($this->isIntendedRecipient() || $this->isSender() || $this->isCopiedToUser());
    }

    /**
     * Make sure user has clinical access and the user is the recipient of the message.
     *
     * @return bool
     */
    public function checkMarkMyMessageAccess()
    {
        return $this->checkAccess('OprnViewClinical') && (
            $this->isIntendedRecipient()
            || $this->isSender(\Yii::app()->user)
            || $this->isCopiedToUser()
        );
    }

    /**
     * Convenience wrapper to retrieve event view URL (this should probably be somewhere in core).
     *
     * @return mixed
     */
    public function getEventViewUrl()
    {
        return \Yii::app()->createUrl('/' . $this->getModule()->name . '/Default/view/' . $this->event->id);
    }

    public function initActionView()
    {
        parent::initActionView();
        if (@$_GET['comment']) {
            $this->show_comment_form = true;
        }
    }

    /**
     * Set up event etc on the controller.
     *
     * @throws \CHttpException
     */
    public function initActionMarkRead()
    {
        $this->initWithEventId(@$_GET['id']);
    }

    /**
     * Mark the event message as read.
     *
     * @throws \Exception
     */
    public function actionMarkRead()
    {
        $mailbox = 
            Mailbox::model()->findByPk(\Yii::app()->request->getParam('mailbox_id')) ?? 
            User::model()->findByPk(\Yii::app()->user->id)->personalMailbox;

        $el = $this->getMessageElement();

        $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $el->id, 'mailbox_id' => $mailbox->id]);
        $recipient->marked_as_read = true;
        $recipient->save();

        // if ($el->comments && $this->canMarkCommentRead($el->last_comment)) {
        //     $this->markCommentRead($el->last_comment);
        // } else {
        //     if ($el->for_the_attention_of->mailbox->isUserAssociatedWith(\User::model()->findByPk(\Yii::app()->user->id))) {
        //         $this->markMessageRead($el);
        //     }
        //     $this->markCopyToRead($el);
        // }

        if (!isset($_GET['noRedirect']) || !$_GET['noRedirect']) {
            $this->redirectAfterAction();
        } else {
            $exam_api = new OphCoMessaging_API();
            $this->renderJSON($exam_api->updateMessagesCount(\Yii::app()->user));
        }
    }

    /**
     * Setup event etc on the controller.
     *
     * @throws \CHttpException
     */
    public function initActionMarkUnread()
    {
        $this->initWithEventId(@$_GET['id']);
    }

    /**
     * Mark the message event as unread.
     *
     * @param $id
     *
     * @throws \Exception
     */
    public function actionMarkUnread($id)
    {
        $mailbox = 
            Mailbox::model()->findByPk(\Yii::app()->request->getParam('mailbox_id')) ?? 
            User::model()->findByPk(\Yii::app()->user->id)->personalMailbox;

        $el = $this->getMessageElement();

        $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $el->id, 'mailbox_id' => $mailbox->id]);
        $recipient->marked_as_read = true;
        $recipient->save();

        // $el = $this->getMessageElement();

        // if ($el->comments) {
        //     $el->last_comment->marked_as_read = false;
        //     $el->last_comment->save();
        // } else {
        //     $transaction = \Yii::app()->db->beginTransaction();

        //     $recipients = OphCoMessaging_Message_Recipient::model()
        //                 ->forReceivedByUser(\Yii::app()->user->id)
        //                 ->forElement($el->id)
        //                 ->findAll();

        //     try {
        //         foreach ($recipients as $recipient) {
        //             $recipient->marked_as_read = false;
        //             $recipient->save();
        //         }

        //         $this->updateEvent();

        //         $this->event->audit('event', 'marked unread');

        //         \Yii::app()->user->setFlash('success', '<a href="' . $this->getEventViewUrl() . "\">{$this->event_type->name}</a> marked as unread.");

        //         $transaction->commit();
        //     } catch (\Exception $e) {
        //         $transaction->rollback();
        //         throw $e;
        //     }
        // }
        $this->redirectAfterAction();
    }

    /**
     * Set up event etc on the controller.
     *
     * @throws \CHttpException
     */
    public function initActionAddComment()
    {
        $this->initWithEventId(@$_GET['id']);
        $this->setOpenElementsFromCurrentEvent('view');
    }

    /**
     * @param $id
     *
     * @throws \CHttpException
     * @throws \Exception
     */
    public function actionAddComment($id)
    {
        $element = $this->getMessageElement();

        // if (isset($element->last_comment)) {
        //     $element->last_comment->marked_as_read = 1;
        //     $element->last_comment->save();
        // }

        $comment = new OphCoMessaging_Message_Comment();

        $comment->comment_text = isset($_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment']['comment_text']) ? $_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment']['comment_text'] : null;
        $comment->mailbox_id = isset($_POST['comment_reply_mailbox']) ? $_POST['comment_reply_mailbox'] : null;
        $comment->element_id = $element->id;

        if (!$comment->validate()) {
            $this->show_comment_form = true;

            $this->action = new \CInlineAction($this, 'view');
            $errors = array('Comment' => array());
            foreach ($comment->getErrors() as $err) {
                $errors['Comment'] = array_values($err);
            }
            $this->render('view', array(
                'errors' => $errors,
                'comment' => $comment,
            ));
        } else {
            $transaction = \Yii::app()->db->beginTransaction();

            try {
                $recipients = 
                    OphCoMessaging_Message_Recipient::model()
                        ->findAllByAttributes(['element_id' => $element->id]);

                foreach ($recipients as $recipient) {
                    //mark the recipient as unread for each mailbox that didn't send the comment, and read for the one that did
                    $recipient_is_comment_sender = ($recipient->mailbox_id === $comment->mailbox_id);
                    $recipient->marked_as_read = !$recipient_is_comment_sender;
                    $recipient->save();
                }

                $element->save();
                $comment->save();

                $this->event->audit('event', 'Reply added');
                $transaction->commit();

                \Yii::app()->user->setFlash('success', 'Comment added to record');
            } catch (\Exception $e) {
                $transaction->rollback();
                throw $e;
            }

            $this->redirectAfterAction();
        }
    }

    /**
     * Send the JSON message with alternate user if the recipient is out of office
     */
    public function actionCheckUserOutOfOffice()
    {
        if (isset($_POST['mailbox_id'])) {
            return $this->renderJSON(\UserOutOfOffice::model()->checkUserOutOfOfficeViaMailbox($_POST['mailbox_id']));
        }
    }

    public function actionAutocompleteMailbox()
    {
        $term = \Yii::app()->request->getParam('term');
        $res = array();

        if (\Yii::app()->request->isAjaxRequest && !empty($term)) {
            $term = strtolower($term);

            $criteria = new \CDbCriteria();
            $criteria->compare("LOWER(name)", $term, true);

            $mailboxes = Mailbox::model()->active()->findAll($criteria);

            foreach ($mailboxes as $mailbox) {
                $res[] = ['id' => $mailbox->id, 'label' => $mailbox->name];
            }
        }

        echo \CJSON::encode($res);
    }

    /**
     * Convenience function for performing redirect once a message has been manipulated.
     */
    protected function redirectAfterAction()
    {
        $return_url = @$_GET['returnUrl'] ?? @$_POST['returnUrl'] ?? $this->getEventViewUrl();
        $this->redirect($return_url);
    }

    /**
     * Determine if the given user (or current if none given) is the intended recipient of the message
     * that is being viewed.
     *
     * @TODO: Use Service Layer?
     *
     * @param \OEWebUser $user
     *
     * @return bool
     */
    protected function isIntendedRecipient(\OEWebUser $user = null)
    {
        if (is_null($user)) {
            $user = \Yii::app()->user;
        }

        if ($el = $this->getMessageElement()) {
            return OphCoMessaging_Message_Recipient::model()
                ->forReceivedByUser($user->id)
                ->forElement($el->id)
                ->exists('primary_recipient <> 0');
        }

        return false;
    }

    protected function isCopiedToUser(\OEWebUser $user = null)
    {
        if (is_null($user)) {
            $user = \Yii::app()->user;
        }

        if ($el = $this->getMessageElement()) {
            if ($el->cc_enabled) {
                return OphCoMessaging_Message_Recipient::model()
                    ->forReceivedByUser($user->id)
                    ->forElement($el->id)
                    ->exists('primary_recipient = 0');
            }
        }

        return false;
    }

    protected function copiedToUserMarkedRead(\OEWebUser $user = null)
    {
        if (is_null($user)) {
            $user = \Yii::app()->user;
        }

        if ($el = $this->getMessageElement()) {
            return OphCoMessaging_Message_Recipient::model()
                ->forReceivedByUser($user->id)
                ->forElement($el->id)
                ->exists('primary_recipient = 0 AND marked_as_read <> 0');
        }
    }


    /**
     * Determine if the given user (or current if none given) is the sender of the message
     * that is being viewed.
     *
     * @TODO: Use Service Layer?
     *
     * @param \OEWebUser $user
     *
     * @return bool
     */
    protected function isSender(\OEWebUser $user = null)
    {
        if (is_null($user)) {
            $user = \Yii::app()->user;
        }

        if ($el = $this->getMessageElement()) {
            return Mailbox::model()
                ->forUser($user->id)
                ->exists('t.id = :sender_id', [':sender_id' => $el->sender_mailbox_id]);
        }

        return false;
    }

    /**
     * @TODO: Use Service Layer?
     */
    public function canComment(\OEWebUser $user = null)
    {
        if (is_null($user)) {
            $user = \Yii::app()->user;
        }
        $messageElement = $this->getMessageElement();

        $canComment = !empty(array_intersect($messageElement->getAllInvolvedMailboxIds(), MailboxSearch::getAllMailboxesForUser($user->id)));

        // $canComment = (
        //         (!$messageElement->comments && $this->isIntendedRecipient($user) && !$this->isSender($user) && $messageElement->message_type->reply_required)
        //         || (($this->isIntendedRecipient($user) || $this->isSender($user))
        //         && $messageElement->comments
        //         && !$messageElement->last_comment->marked_as_read
        //         && $messageElement->last_comment->created_user_id != $user->getId()
        //         && $messageElement->message_type->reply_required)
        //     );

        return $canComment;
    }

    /**
     * Convenience wrapper function.
     *
     * @return \OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message
     */
    protected function getMessageElement()
    {
        if (!$this->message_el) {
            $this->message_el = $this->event->getElementByClass('\OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message');
        }

        return $this->message_el;
    }

    /**
     * @param $el
     *
     * @return bool
     */
    public function canMarkMessageRead($el, $mailbox)
    {
        $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $el->id, 'mailbox_id' => $mailbox->id]);

        return isset($recipient) && !$recipient->marked_as_read;

        // if ($el->cc_enabled && $this->isCopiedToUser()) {
        //     return $this->canMarkCopyToRead($el);
        // } elseif (
        //     $this->isIntendedRecipient()
        //     && !$el->for_the_attention_of->marked_as_read
        // ) {
        //     return true;
        // }

        // return false;
    }

    /**
     * @param $el
     *
     * @throws \Exception
     */
    public function markMessageRead($el, $mailbox)
    {
        // $el->for_the_attention_of->marked_as_read = true;

        $transaction = \Yii::app()->db->beginTransaction();

        try {
            $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $el->id, 'mailbox_id' => $mailbox->id]);
            $recipient->marked_as_read = true;
            $recipient->save();

            // $el->for_the_attention_of->save();
            $this->updateEvent();

            $this->event->audit('event', 'marked read');

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canMarkMessageUnread($el, $mailbox)
    {
        $recipient = OphCoMessaging_Message_Recipient::model()->findByAttributes(['element_id' => $el->id, 'mailbox_id' => $mailbox->id]);

        return isset($recipient) && $recipient->marked_as_read;

        // $user = \Yii::app()->user;

        // if ($el->comments) {
        //     return false;
        // } elseif (
        //     ($this->isIntendedRecipient() && $el->for_the_attention_of->marked_as_read)
        //           || ($this->isCopiedToUser($user) && $this->copiedToUserMarkedRead($user))
        // ) {
        //     return true;
        // } else {
        //     return false;
        // }
    }

    // public function canMarkCopyToRead($el)
    // {
    //     return OphCoMessaging_Message_Recipient::model()
    //         ->forReceivedByUser(\Yii::app()->user->id)
    //         ->forElement($el->id)
    //         ->exists('primary_recipient = 0 AND marked_as_read = 0');
    // }

    // public function markCopyToRead($el)
    // {
    //     $cc_recipient = OphCoMessaging_Message_Recipient::model()
    //                   ->forReceivedByUser(\Yii::app()->user->id)
    //                   ->forElement($el->id)
    //                   ->find('primary_recipient = 0');

    //     if ($cc_recipient) {
    //         $transaction = \Yii::app()->db->beginTransaction();

    //         try {
    //             $cc_recipient->marked_as_read = true;
    //             $cc_recipient->save();

    //             $this->event->audit('event', 'marked read by copied to user');

    //             $transaction->commit();
    //         } catch (\Exception $e) {
    //             $transaction->rollback();
    //             throw $e;
    //         }
    //     }
    // }

    /**
     * Extend the parent method to set event issue data based on user selection.
     */
    protected function updateEventInfo()
    {
        parent::updateEventInfo();
        $this->updateEventIssues();
    }

    /**
     * Set the urgent issue (or otherwise) at the event level.
     */
    protected function updateEventIssues()
    {
        // This logic is slightly wonky because there's not an interface to check for
        // a specific issue on the event, but the assumption is that no issue is raised on the
        // message event asides from urgency
        if ($this->getMessageElement()->urgent) {
            if (!$this->event->hasIssue()) {
                $this->event->addIssue('Urgent');
            }
        } else {
            if ($this->event->hasIssue()) {
                $this->event->deleteIssue('Urgent');
            }
        }
    }

    /**
     * Set the event info text without the open_elements attribute being set.
     */
    protected function updateEvent()
    {
        $this->event->info = $this->getMessageElement()->infotext;
        $this->updateEventIssues();
        $this->event->save();
    }

    public function actionAutoComplete($term)
    {
        $res = array();
        if (\Yii::app()->request->isAjaxRequest && !empty($term)) {
            $term = strtolower($term);

            $criteria = new \CDbCriteria();
            $criteria->compare("LOWER(name)", $term, true);

            foreach (Mailbox::model()->findAll($criteria) as $mailbox) {
                $res[] = array('id' => $mailbox->id, 'label' => $mailbox->name);
            }
        }
        echo \CJSON::encode($res);
    }
}
