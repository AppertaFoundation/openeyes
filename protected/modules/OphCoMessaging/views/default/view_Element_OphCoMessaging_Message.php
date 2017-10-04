<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
if (!@$comment) {
    // ensure we have base comment object
    $comment = new \OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment();
}
?>

<div class="element">

	<div class="element-data">
        <div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('for_the_attention_of_user_id'))?></div></div>
			<div class="large-10 column end"><div class="data-value <?= $element->urgent ? ' urgent' : ''?>"><?php echo $element->for_the_attention_of_user->getFullnameAndTitle();
                    if ($element->urgent) {
                        echo ' <span class="priority fa fa-exclamation"></span>';
                    }
            ?></div></div>

		</div>
		<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('message_type_id'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?php echo $element->message_type ? $element->message_type->name : 'None'?></div></div>
		</div>
		<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('message_text'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?= Yii::app()->format->Ntext($element->message_text)?></div></div>
		</div>
        <?= $element->comments ? '<hr />' : '' ?>
        <?php foreach ($element->comments as $comment) {?>
            <div class="row data-row comment">
                <div class="large-2 column"><div class="data-label">@<?php echo Helper::convertMySQL2NHS($comment->created_date) ?></div></div>
                <div class="large-10 column end"><div class="data-value"><?= Yii::app()->format->Ntext($comment->comment_text)?></div></div>
            </div>
        <?php } ?>
        <?php if ($this->canComment()) { ?>
            <div class="<?= $this->show_comment_form ? '' : 'hidden'?>" id="new-comment-form">
                <?php
                $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                    'id' => 'comment-form',
                    'action' => Yii::app()->createUrl('/'.$this->getModule()->name.'/Default/AddComment', array('id' => $this->event->id)),
                    'enableAjaxValidation' => false,
                    'layoutColumns' => array(
                        'label' => 2,
                        'field' => 10,
                    ),
                ));
                ?>
                <?php echo $form->textArea($comment, 'comment_text', array('rows' => 6, 'cols' => 80), false, null, array('label' => 2, 'field' => 6))?>
                <div class="row data-row">
                    <div class="large-2 column">&nbsp;</div>
                    <div class="large-4 column end">
                        <button class="button small secondary" id="new-comment-cancel">Cancel</button>
                        <button class="button small primary" type="submit">Save</button>
                    </div>
                </div>
                <?php $this->endWidget() ?>
            </div>
            <div class="row <?= $this->show_comment_form ? 'hidden' : '' ?>" id="add-comment-button-container">
                <div class="large-2 column">&nbsp;</div>
                <div class="large-3 column end">
                    <button class="button small secondary" name="comment" type="submit" id="add-message-comment">Comment</button></div>
            </div>
        <?php } ?>
	</div>
</div>
