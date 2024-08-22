<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

    $delete_access = $this->checkDeleteAccess();
    $request_delete_access = $this->checkRequestDeleteAccess();
?>

<?php if ($delete_access || $request_delete_access) : ?>
<div class="oe-popup-wrap" id="js-delete-event" style="display: none; z-index:100">
    <div class="oe-popup">
        <?=\CHtml::form(['Default/' . ($delete_access ? 'delete' : 'requestDeletion') . '/' . $this->event->id], 'post', ['id' => 'deleteForm']) ?>
        <div class="title">
            <div class="flex-l">
                <i class="oe-i trash pad-right"></i><?=(!$delete_access ? 'Request event deletion' : 'Delete Event');?>
            </div>
        </div>
        <div class="close-icon-btn js-close-delete-btn"><i class="oe-i remove-circle pro-theme"></i></div>
        <div class="oe-popup-content delete-event">

            <div class="alert-box warning">
            <strong>WARNING:</strong>
                <ul>
                    <li>The event will be marked as deleted</li>
                    <li>Deleted events may still be viewed via Admin settings</li>
                    <li>THIS ACTION CANNOT BE UNDONE.</li>
                </ul>
                <?php $this->displayErrors(@$errors) ?>
                <p id="errors"></p>
            </div>
            <table class="large-text">
                <tbody>
                <tr>
                    <th>Event type</th>
                    <td class="large-text">
                        <i class="oe-i-e <?php echo $this->event->eventType->getEventIconCssClass()?>"></i>
                        <?php echo $this->event->eventType->name ?> <?php echo Helper::convertDate2NHS($this->event->event_date)?>
                    </td>
                </tr>
                <tr>
                    <td>Reason for deletion:</td>
                    <td><?=
                        \CHtml::textArea(
                            'delete_reason', '',
                            array(
                                'class' => 'cols-full',
                                'id' => 'js-text-area',
                                'rows' => 1,
                                'placeholder' => 'Reason for deletion (required)',
                                'data-test' => 'reason-for-deletion'
                            )
                        )
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <p>Are you sure you want to proceed?</p>

            <div class="popup-actions flex-right">
                <button type="submit" id="et_deleteevent" name="et_deleteevent" class="red hint" data-test="delete-event">
                    <?=(!$delete_access ? 'Yes - Request event deletion' : 'Yes - DELETE Event');?>
                </button>
                <button type="submit" class="js-demo-cancel-btn" id="et_canceldelete" name="et_canceldelete">
                    No, cancel
                </button>
            </div>
        </div>
    </div>
    <?=\CHtml::endForm(); ?>
</div>

<script>
    $('#et_canceldelete, .js-close-delete-btn').click(function(event){
        event.preventDefault();
        $('#errors').text("");
        $('#js-delete-event').css('display','none');
    });

    $('#et_deleteevent').click(function(event) {
        var reasonLength = $('#js-text-area').val().length;
        if (reasonLength > 0){
            return;
        } else {
            $('#errors').text("Please enter the reason for deletion");
            event.preventDefault();
        }
    });

    $('#js-delete-event-btn').click(function(event){
        $('#js-delete-event').css('display','');
        return false;
    });

    $('#js-delete-event').submit(function () {
        $('#et_deleteevent').attr('disabled', 'disabled');
        $('#et_canceldelete').attr('disabled', 'disabled');
    });
</script>
<?php endif; ?>
