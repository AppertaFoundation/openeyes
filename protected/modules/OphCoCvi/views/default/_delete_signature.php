<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php $this->beginContent('//patient/event_container'); ?>
    <div id="delete_event">
        <h3>Delete signature</h3>
        <div class="alert-box alert with-icon">
            <strong>WARNING: This will permanently delete the signature and remove it from view.<br><br>THIS ACTION CANNOT
                BE UNDONE.</strong>
        </div>
        <?php
        if (isset($errors)) {
            $this->displayErrors($errors);
        }
        ?>
        <?php
        echo CHtml::form(array('Default/deleteSignature?event_id=' . $this->event->id. '&signature_id='.$signature_id) , 'post', array('id' => 'deleteForm'));
        ?>
        <div style="width:300px; margin-bottom: 0.6em;">
            <p>Reason for deletion:</p>
            <?php echo CHtml::textArea('delete_reason') ?>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <button type="submit" class="warning" name="delete_signature">
            Delete signature
        </button>
        <a href="/OphCoCvi/default/view/<?=$this->event->id?>" class="button">Cancel</a>
        <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
             alt="loading..." style="display: none;"/>
        <?php echo CHtml::endForm() ?>
    </div>
<?php $this->endContent() ?>