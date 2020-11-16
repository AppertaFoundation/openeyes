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
?>

<?php
if (!isset($comment)) {
    // ensure we have base comment object
    $comment = new \OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment();
}
?>

<div class="element-data full-width flex-layout flex-top">
    <div class="cols-5">
        <table class="label-value">
            <colgroup>
                <col class="cols-3">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <div class="data-label">From</div>
                </td>
                <td>
                    <div class="data-value">
                        <span class="priority-text">
                            <?= $element->user->getFullnameAndTitle(); ?>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-label">Date sent</div>
                </td>
                <td>
                    <div class="data-value"><?php echo Helper::convertDate2NHS($element->event->event_date);?></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-label">Recipient</div>
                </td>
                <td>
                    <div class="data-value "><?php echo $element->for_the_attention_of_user->getFullnameAndTitle();?></div>
                </td>
            </tr>
            <?php if ($element->cc_enabled) { ?>
            <tr>
                <td>
                    <div class="data-label">Copied to</div>
                </td>
                <td>
                    <div class="data-value">
                        <?php
                        $copied_users = [];
                        foreach ($element->copyto_users as $copied_user) {
                            array_push($copied_users, $copied_user->user->getFullnameAndTitle());
                        }
                        echo implode(', ', $copied_users);
                        ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td>
                    <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('message_type_id')) ?></div>
                </td>
                <td>
                    <div class="data-value"><?php echo $element->message_type ? $element->message_type->name : 'None' ?></div>
                </td>
            </tr>
            <?php if ($element->urgent) { ?>
            <tr>
                <td>
                    <div class="data-label">Priority</div>
                </td>
                <td>
                    <div class="data-value">
                        <i class="oe-i status-urgent no-hover small pad-right"></i>
                        <span class="highlighter orange">Urgent message</span>
                    </div>
                </td>
            </tr>
            <?php } ?>
            </tbody>

        </table>


    </div>
    <div class="cols-6">
        <div class="row <?php echo $element->comments ? 'divider' : ''?>">
            <p class="message-start"><?= Yii::app()->format->Ntext(preg_replace("/\n/", "", preg_replace('/(\s{4})\s+/', '$1', $element->message_text))) ?></p>
        </div>

        <?php foreach ($element->comments as $comment) { ?>
            <p class="message-reply">
                <i class="oe-i child-arrow small pad-right no-click"></i>
                <em><?= Yii::app()->format->Ntext(preg_replace("/\n/", "", preg_replace('/(\s{4})\s+/', '$1', $comment->comment_text))) ?></em>
            </p>
            <table class="label-value">
                <tbody>
                <tr>
                    <td>
                        <div class="data-label">Reply date</div>
                    </td>
                    <td>
                        <div class="data-value"><?php echo Helper::convertMySQL2NHS($comment->created_date) ?></div>
                    </td>
                </tr><tr>
                    <td>
                        <div class="data-label">From</div>
                    </td>
                    <td>
                        <div class="data-value"><?php echo $comment->user->getFullnameAndTitle();?></div>
                    </td>
                </tr>           </tbody>
            </table>
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
            <div class="row">
                <p><i class="oe-i child-arrow small pad-right no-click"></i><em class="fade">Reply … <small>(can not be edited once sent)</small></em></p>
                <textarea class="cols-full column cols" placeholder="Your message"
                          id="OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment_comment_text"
                          name="OEModule_OphCoMessaging_models_OphCoMessaging_Message_Comment[comment_text]"
                          rows=5
                ></textarea>

                <div class="flex-layout flex-right">
                    <button class="green hint" id="send_reply">Send reply</button>
                </div>
            </div>
            <?php $this->endWidget() ?>
        <?php } ?>
    </div>
</div>
