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
?>
<div class="box admin">
    <h2>Scheduling options</h2>
    <form id="admin_schedulingoptions">
        <table class="standard">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkall" class="scheduleoptions" /></th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $criteria = new CDbCriteria();
                $criteria->order = 'display_order asc';
                foreach (OphTrOperationbooking_ScheduleOperation_Options::model()->active()->findAll() as $i => $scheduleoption) {?>
                    <tr class="clickable sortable" data-attr-id="<?php echo $scheduleoption->id?>?>" data-uri="OphTrOperationbooking/admin/editschedulingoption/<?php echo $scheduleoption->id?>">
                        <td><input type="checkbox" name="scheduleoption[]" value="<?php echo $scheduleoption->id?>" class="scheduleoptions" /></td>
                        <td><?php echo $scheduleoption->name?></td>
                    </tr>
                <?php }?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <?php echo EventAction::button('Add', 'add_scheduleoption', null, array('class' => 'button small'))->toHtml()?>
                        <?php echo EventAction::button('Delete', 'delete_scheduleoption', null, array('class' => 'button small'))->toHtml()?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<div id="confirm_delete_scheduleoptions" title="Confirm delete scheduling option(s)" style="display: none;">
    <div id="delete_scheduleoptions">
        <div class="alert-box alert with-icon">
            <strong>WARNING: This will remove the scheduling option(s) from the system.<br/>This action cannot be undone.</strong>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <div class="buttons">
            <input type="hidden" id="medication_id" value="" />
            <button type="submit" class="warning btn_remove_scheduleoptions">Remove scheduling option(s)</button>
            <button type="submit" class="secondary btn_cancel_remove_scheduleoptions">Cancel</button>
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
        </div>
    </div>
</div>
<script type="text/javascript">
    handleButton($('#et_delete_scheduleoption'),function(e) {
        e.preventDefault();

        if ($('input[type="checkbox"][name="scheduleoption[]"]:checked').length <1) {
            var dialog = new OpenEyes.UI.Dialog.Alert({
                content: "Please select the scheduling option(s) you wish to delete."
            });
            dialog.on('close', function() {enableButtons();});
            dialog.open();
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSchedulingOptions',
            'data': $('#admin_schedulingoptions').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    enableButtons();

                    $('#confirm_delete_scheduleoptions').dialog({
                        resizable: false,
                        modal: true,
                        width: 560
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "One or more of the selected scheduling options are in use by operations and so cannot be deleted."
                    }).open();
                    enableButtons();
                }
            }
        });
    });

    $('button.btn_cancel_remove_scheduleoptions').click(function(e) {
        e.preventDefault();
        $('#confirm_delete_scheduleoptions').dialog('close');
    });

    handleButton($('button.btn_remove_scheduleoptions'),function(e) {
        e.preventDefault();

        // verify again as a precaution against race conditions
        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSchedulingOptions',
            'data': $('#admin_schedulingoptions').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    $.ajax({
                        'type': 'POST',
                        'url': baseUrl+'/OphTrOperationbooking/admin/deleteSchedulingOptions',
                        'data': $('#admin_schedulingoptions').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                        'success': function(resp) {
                            if (resp == "1") {
                                window.location.reload();
                            } else {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "There was an unexpected error deleting the scheduleoptions, please try again or contact support for assistance",
                                    onClose: function() {
                                        enableButtons();
                                        $('#confirm_delete_scheduleoptions').dialog('close');
                                    }
                                }).open();
                            }
                        }
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "One or more of the selected scheduling options are now in use by operations and so cannot be deleted.",
                        onClose: function() {
                            enableButtons();
                            $('#confirm_delete_scheduleoptions').dialog('close');
                        }
                    }).open();
                }
            }
        });
    });
</script>
