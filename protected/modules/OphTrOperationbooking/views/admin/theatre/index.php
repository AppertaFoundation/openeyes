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

<div class="cols-5">
    <div class="row divider">
        <h2>Theatres</h2>
    </div>

    <form id="theatres">
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" id="checkall" class="theatres" /></th>
                <th>Site</th>
                <th>Name</th>
                <th>Code</th>
                <th>Ward</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $criteria = new CDbCriteria();
            $criteria->order = 'display_order asc';
            if (!$this->chechAccess('admin')) {
                $criteria->with = 'site';
                $criteria->addCondition('site.institution_id = :institution_id');
                $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
            }

            $theatres = OphTrOperationbooking_Operation_Theatre::model()->active()->findAll($criteria);
            if (isset($theatres)) {
                foreach ($theatres as $i => $theatre) { ?>
                    <tr class="clickable sortable" data-attr-id="<?php echo $theatre->id ?>"
                        data-uri="OphTrOperationbooking/admin/editTheatre/<?php echo $theatre->id ?>">
                        <td><input type="checkbox" name="theatre[]" value="<?php echo $theatre->id ?>"
                                   class="theatres"/></td>
                        <td><?php echo $theatre->site->name ?></td>
                        <td><?php echo $theatre->name ?></td>
                        <td><?php echo $theatre->code ?></td>
                        <td><?php echo $theatre->ward->name ?? 'None' ?></td>
                    </tr>
                <?php }
            } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <?=\CHtml::submitButton('Add', ['id' => 'et_add_theatre', 'class' => 'button large']);?>
                        <?=\CHtml::submitButton('Delete', ['id' => 'et_delete_theatre', 'class' => 'button large']);?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<div id="confirm_delete_theatres" title="Confirm delete theatre" style="display: none;">
    <div id="delete_theatres">
        <div class="alert-box alert with-icon">
            <strong>WARNING: This will remove the theatres from the system.<br/>This action cannot be undone.</strong>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <div class="buttons">
            <input type="hidden" id="medication_id" value="" />
            <button type="submit" class="warning btn_remove_theatres">Remove theatre(s)</button>
            <button type="submit" class="secondary btn_cancel_remove_theatres">Cancel</button>
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
        </div>
    </div>
</div>


<script type="text/javascript">
    handleButton($('#et_delete_theatre'),function(e) {
        e.preventDefault();

        if ($('input[type="checkbox"][name="theatre[]"]:checked').length <1) {
            new OpenEyes.UI.Dialog.Alert({
                content: "Please select the theatre(s) you wish to delete."
            }).open();
            enableButtons();
            return;
        }

        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteTheatres',
            'data': $('form#theatres').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp === "1") {
                    enableButtons();

                    if ($('input[type="checkbox"][name="theatre[]"]:checked').length === 1) {
                        $('#confirm_delete_theatres').attr('title','Confirm delete theatre');
                        $('#delete_theatres').children('div').children('strong').html("WARNING: This will remove the theatre from the system.<br/><br/>This action cannot be undone.");
                        $('button.btn_remove_theatres').children('span').text('Remove theatre');
                    } else {
                        $('#confirm_delete_theatres').attr('title','Confirm delete theatres');
                        $('#delete_theatres').children('div').children('strong').html("WARNING: This will remove the theatres from the system.<br/><br/>This action cannot be undone.");
                        $('button.btn_remove_theatres').children('span').text('Remove theatres');
                    }

                    $('#confirm_delete_theatres').dialog({
                        resizable: false,
                        modal: true,
                        width: 560
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "One or more of the selected theatres have active future bookings and so cannot be deleted."
                    }).open();
                    enableButtons();
                }
            }
        });
    });

    $('button.btn_cancel_remove_theatres').click(function(e) {
        e.preventDefault();
        $('#confirm_delete_theatres').dialog('close');
    });

    handleButton($('button.btn_remove_theatres'),function(e) {
        e.preventDefault();

        // verify again as a precaution against race conditions
        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteTheatres',
            'data': $('form#theatres').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp === "1") {
                    $.ajax({
                        'type': 'POST',
                        'url': baseUrl+'/OphTrOperationbooking/admin/deleteTheatres',
                        'data': $('form#theatres').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                        'success': function(resp) {
                            if (resp === "1") {
                                window.location.reload();
                            } else {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "There was an unexpected error deleting the theatres, please try again or contact support for assistance",
                                    onClose: function() {
                                        enableButtons();
                                        $('#confirm_delete_theatres').dialog('close');
                                    }
                                }).open();
                            }
                        }
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "One or more of the selected theatres now have active future bookings and so cannot be deleted.",
                        onClose: function() {
                            enableButtons();
                            $('#confirm_delete_theatres').dialog('close');
                        }
                    }).open();
                }
            }
        });
    });
</script>
