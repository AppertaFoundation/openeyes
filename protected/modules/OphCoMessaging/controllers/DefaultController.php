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

use OEModule\OphCoMessaging\components\OphCoMessaging_API;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_CopyTo_Users;

class DefaultController extends \BaseEventTypeController
{
    const ACTION_TYPE_MYMESSAGE = 'ManageMyMessage';
    const ACTION_TYPE_MARKMESSAGE = 'MarkMyMessage';

    protected static $action_types = array(
        'userfind' => self::ACTION_TYPE_CREATE,
        'markread' => self::ACTION_TYPE_MARKMESSAGE,
        'markunread' => self::ACTION_TYPE_MARKMESSAGE,
        'addcomment' => self::ACTION_TYPE_MYMESSAGE,
        'checkuseroutofoffice' => self::ACTION_TYPE_CREATE,
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
        return $this->checkAccess('OprnViewClinical') && ($this->isIntendedRecipient() || $this->isSender(\Yii::app()->user) || $this->isCopiedToUser());
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

    /**
     * Duplicated from the admin controller to give a user list.
     *
     * @TODO: There's a method on the UserController that could be used, so would be worth consolidating)
     */
    public function actionUserFind()
    {
        $res = array();
        if (\Yii::app()->request->isAjaxRequest && !empty($_REQUEST['search'])) {
            $criteria = new \CDbCriteria();
            $criteria->compare('LOWER(username)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(first_name)', strtolower($_REQUEST['search']), true, 'OR');
            $criteria->compare('LOWER(last_name)', strtolower($_REQUEST['search']), true, 'OR');
            $words = explode(' ', $_REQUEST['search']);
            if (count($words) > 1) {
                // possibly slightly verbose approach to checking first and last name combinations
                // for searches
                $first_criteria = new \CDbCriteria();
                $first_criteria->compare('LOWER(first_name)', strtolower($words[0]), true);
                $first_criteria->compare('LOWER(last_name)', strtolower(implode(' ', array_slice($words, 1, count($words) - 1))), true);
                $last_criteria = new \CDbCriteria();
                $last_criteria->compare('LOWER(first_name)', strtolower($words[count($words) - 1]), true);
                $last_criteria->compare('LOWER(last_name)', strtolower(implode(' ', array_slice($words, 0, count($words) - 2))), true);
                $first_criteria->mergeWith($last_criteria, 'OR');
                $criteria->mergeWith($first_criteria, 'OR');
            }

            foreach (\User::model()->findAll($criteria) as $user) {
                $res[] = array(
                    'id' => $user->id,
                    'label' => $user->getFullNameAndTitle(),
                    'value' => $user->getFullName(),
                    'username' => $user->username,
                );
            }
        }
        echo \CJSON::encode($res);
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
        $el = $this->getMessageElement();

        if ($el->comments && $this->canMarkCommentRead($el)) {
            $this->markCommentRead($el);
        } else {
            if ($el->for_the_attention_of_user_id === \Yii::app()->user->id) {
                $this->markMessageRead($el);
            }
            $this->markCopyToRead($el);
        }

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
        $el = $this->getMessageElement();

        if ($el->comments) {
            $el->last_comment->marked_as_read = false;
            $el->last_comment->save();
        } else {
            if ($el->for_the_attention_of_user_id == \Yii::app()->user->id) {
                $el->marked_as_read = false;
            }
            if ($el->copyto_users) {
                $copiedto_users = $el->copyto_users;
                foreach ($copiedto_users as $copiedto_user) {
                    if ($copiedto_user->user_id == \Yii::app()->user->id) {
                        $copiedto_user->marked_as_read = false;
                        $copiedto_user->save();
                        break;
                    }
                }
            }
            $transaction = \Yii::app()->db->beginTransaction();
            try {
                $el->save();
                $this->updateEvent();

                $this->event->audit('event', 'marked unread');

                \Yii::app()->user->setFlash('success', '<a href="' . $this->getEventViewUrl() . "\">{$this->event_type->name}</a> marked as unread.");

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }
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

        if (isset($element->last_comment)) {
            $element->last_comment->marked_as_read = 1;
            $element->last_comment->save();
        }

        $comment = new OphCoMessaging_Message_Comment();
        \OELog::log(print_r($_POST, true));
        $comment->comment_text = @$_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment']['comment_text'];
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
                $element->marked_as_read = true;
                $element->save();
                $comment->save();
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
        if (isset($_POST['user_id'])) {
            return $this->renderJSON(\UserOutOfOffice::model()->checkUserOutOfOffice($_POST['user_id']));
        }
    }

    /**
     * Convenience function for performing redirect once a message has been manipulated.
     */
    protected function redirectAfterAction()
    {
        if (!$return_url = @$_GET['returnUrl']) {
            if (!$return_url = @$_POST['returnUrl']) {
                $return_url = $this->getEventViewUrl();
            }
        }
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
            if ($el->for_the_attention_of_user_id == $user->getId()) {
                return true;
            }
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
                foreach ($el->copyto_users as $copied_user) {
                    if ($copied_user->user_id === $user->getId()) {
                        return true;
                    }
                }
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
            foreach ($el->copyto_users as $copied_user) {
                if ($copied_user->user_id === $user->getId()) {
                    return $copied_user->marked_as_read;
                }
            }
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
            if ($el->user->id == $user->getId()) {
                return true;
            }
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
        $canComment = $messageElement->message_type->reply_required && ((!$messageElement->comments && $this->isIntendedRecipient($user) && !$this->isSender($user))
                || (($this->isIntendedRecipient($user) || $this->isSender($user))
                && $messageElement->comments
                && !$messageElement->last_comment->marked_as_read
                && $messageElement->last_comment->created_user_id != $user->getId()));

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
     * @return bool
     */
    public function canMarkRead()
    {
        $el = $this->getMessageElement();
        if ($el->cc_enabled && $this->isCopiedToUser()) {
            return $this->canMarkCopyToRead($el);
        } elseif ($el->comments) {
            return $this->canMarkCommentRead($el);
        } else {
            return $this->canMarkMessageRead($el);
        }
    }

    /**
     * @param $el
     *
     * @return bool
     */
    protected function canMarkMessageRead($el)
    {
        if ($this->isIntendedRecipient()
            && !$el->marked_as_read
        ) {
            return true;
        }

        return false;
    }

    protected function canMarkCopyToRead($el)
    {
        $user = \Yii::app()->user;
        if ($this->isCopiedToUser($user)) {
            $copied_users = $el->copyto_users;
            foreach ($copied_users as $copied_user) {
                if ($copied_user->user_id === $user->getId() && $copied_user->marked_as_read === '0') {
                    return true;
                }
            }
        }
        return false;
    }

    protected function canMarkCommentRead($el)
    {
        $user = \Yii::app()->user;
        if ($el->last_comment->created_user_id != $user->getId() &&
            ($this->isIntendedRecipient($user) || $this->isSender($user))
            && !$el->last_comment->marked_as_read) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canMarkUnRead()
    {
        $el = $this->getMessageElement();
        $user = \Yii::app()->user;
        if ($el->comments) {
            if ($el->last_comment->created_user_id != $user->getId() &&
                ($this->isIntendedRecipient($user) || $this->isSender($user))
                && $el->last_comment->marked_as_read) {
                return true;
            }
        } elseif (($this->isIntendedRecipient() && $el->marked_as_read)
            || ($this->isCopiedToUser($user) && $this->copiedToUserMarkedRead($user))) {
            return true;
        }

        return false;
    }

    /**
     * @param $el
     *
     * @throws \Exception
     */
    protected function markMessageRead($el)
    {
        $el->marked_as_read = true;
        $transaction = \Yii::app()->db->beginTransaction();
        try {
            $el->save();
            $this->updateEvent();

            $this->event->audit('event', 'marked read');

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        return true;
    }

    protected function markCommentRead($el)
    {
        $transaction = \Yii::app()->db->beginTransaction();
        try {
            foreach ($el->comments as $comment) {
                $comment->marked_as_read = true;
                $comment->save();
            }

            $this->event->audit('event', 'comments marked read');
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    protected function markCopyToRead($el)
    {
        $user = \Yii::app()->user;
        $transaction = \Yii::app()->db->beginTransaction();
        try {
            $copiedto_users = $el->copyto_users;
            foreach ($copiedto_users as $copiedto_user) {
                if ($copiedto_user->user_id == $user->id) {
                    $copiedto_user->marked_as_read = true;
                    $copiedto_user->save();
                    break;
                }
            }

            $this->event->audit('event', 'marked read by copied to user');

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    /**
     * Extend the parent method to set event issue data based on user selection.
     */
    protected function updateEventInfo()
    {
        parent::updateEventInfo();
        $this->updateCopyToUsers();
        $this->updateEventIssues();
    }

    /**
     * Save all the users that have been copied to in the message event
     */
    protected function updateCopyToUsers()
    {
        $element = $this->getMessageElement();

        if (isset($_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_CopyTo_Users']['user_id']) &&
            trim($_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_CopyTo_Users']['user_id']) !== '') {
            $element->cc_enabled = 1;
            $element->save();

            $copyto_users = explode(',', @$_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_CopyTo_Users']['user_id']);

            foreach ($copyto_users as $copyto_user) {
                $copyto = new OphCoMessaging_Message_CopyTo_Users();
                $copyto->element_id = $element->id;
                $copyto->user_id = $copyto_user;
                $copyto->save();
            }
        }
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
}
