<?php echo "<?php\n"?>
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
<?php echo '<?php'?> $this->beginContent('//patient/event_container')<?php echo "?>\n"?>

    <h2 class="event-title"><?php echo '<?php'?> echo $this->event_type->name <?php echo '?>'?></h2>

    <?php echo '<?php'?> echo CHtml::form(array('Default/requestDeletion/'.$this->event->id), 'post', array('id' => 'deleteForm'))<?php echo '?>'?>
    <div id="delete_event">
        <h3>Request event deletion</h3>
        <div class="alert-box issue with-icon">
            <strong>This will send a request to delete the event to an admin user.</strong>
        </div>
        <?php echo '<?php'?> $this->displayErrors($errors)<?php echo '?>'?>
        <div style="width:300px; margin-bottom: 0.6em;">
            <p>Reason for deletion:</p>
            <?php echo '<?php'?> echo CHtml::textArea('delete_reason','')<?php echo '?>'?>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <?php echo '<?php'?> echo CHtml::hiddenField('event_id', $this->event->id)<?php echo '?>'?>
        <button type="submit" class="warning" id="et_deleteevent" name="et_deleteevent">
            Request deletion
        </button>
        <button type="submit" class="secondary" id="et_canceldelete" name="et_canceldelete">
            Cancel
        </button>
        <img class="loader" src="<?php echo '<?php'?> echo Yii::app()->createUrl('img/ajax-loader.gif')<?php echo '?>'?>" alt="loading..." style="display: none;" />
        <?php echo '<?php'?> echo CHtml::endForm()<?php echo '?>'?>
    </div>

<?php echo '<?php'?> $this->endContent() <?php echo '?>'?>
<script type="text/javascript">
    $('#delete_reason').focus();
</script>
