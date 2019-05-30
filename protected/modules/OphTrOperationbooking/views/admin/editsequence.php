<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="box admin">
    <h2><?php echo $sequence->id ? 'Edit' : 'Add'?> sequence</h2>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>
    <?php echo $form->errorSummary($sequence); ?>
    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>
        <tbody>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('firm_id'); ?>
            </td>
            <td>
                <?php echo $form->dropDownList($sequence, 'firm_id', Firm::model()->getListWithSpecialties(),
                    array('empty' => '- Emergency -', 'nowrapper' => true))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('theatre_id'); ?>
            </td>
            <td>
                <?php echo $form->dropDownList($sequence, 'theatre_id',
                    CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->activeOrPk($sequence->theatre_id)->findAll(array('order' => 'name')),
                        'id', 'nameWithSite'),
                    array('empty' => '- None -', 'nowrapper' => true))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('start_date'); ?>
            </td>
            <td>
                <?php echo $form->datePicker($sequence, 'start_date', array(), array('null' => true, 'nowrapper' => true), array('field' => 2))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('end_date'); ?>
            </td>
            <td>
                <?php echo $form->datePicker($sequence, 'end_date', array(), array('null' => true, 'nowrapper' => true), array('field' => 2))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('weekday'); ?>
            </td>
            <td>
                <?php echo $form->dropDownList($sequence, 'weekday',
                    array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'),
                    array('empty' => '- Weekday -', 'nowrapper' => true))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('start_time'); ?>

            </td>
            <td>
                <?php echo $form->textField($sequence, 'start_time', array('class' => 'time-picker', 'nowrapper' => true), array(), array('field' => 2))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('end_time'); ?>

            </td>
            <td>
                <?php echo $form->textField($sequence, 'end_time', array('class' => 'time-picker', 'nowrapper' => true), array(), array('field' => 2))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('default_admission_time'); ?>

            </td>
            <td>
                <?php echo $form->textField($sequence, 'default_admission_time', array('class' => 'time-picker', 'nowrapper' => true), array(), array('field' => 2))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('interval_id'); ?>

            </td>
            <td>
                <?php echo $form->dropDownList($sequence, 'interval_id', 'OphTrOperationbooking_Operation_Sequence_Interval', array('nowrapper' => true))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('consultant'); ?>

            </td>
            <td>
                <?php echo $form->radioBoolean($sequence, 'consultant', array('nowrapper' => true))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('paediatric'); ?>

            </td>
            <td>
                <?php echo $form->radioBoolean($sequence, 'paediatric', array('nowrapper' => true))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('anaesthetist'); ?>
            </td>
            <td>
                <?php echo $form->radioBoolean($sequence, 'anaesthetist', array('nowrapper' => true))?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $sequence->getAttributeLabel('general_anaesthetic'); ?>
            </td>
            <td>
                <?php echo $form->radioBoolean($sequence, 'general_anaesthetic', array('nowrapper' => true))?>
            </td>
        </tr>
        <tr id="OphTrOperationbooking_Operation_Sequence_week_selection" class="data-group">
            <td class="cols-2 column">
                Week selection:
            </td>
            <td class="cols-5 column end">
                <input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week1]" value="0" />
                <input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week2]" value="0" />
                <input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week3]" value="0" />
                <input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week4]" value="0" />
                <input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week5]" value="0" />

                <label class="inline">
                    <input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week1]" value="1"
                        <?php if ($sequence->week_selection & 1) {?> checked="checked"<?php }?>/> 1st
                </label>
                <label class="inline">
                    <input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week2]" value="1"
                        <?php if ($sequence->week_selection & 2) {?> checked="checked"<?php }?>/> 2nd
                </label>
                <label class="inline">
                    <input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week3]" value="1"
                        <?php if ($sequence->week_selection & 4) {?> checked="checked"<?php }?>/> 3rd
                </label>
                <label class="inline">
                    <input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week4]" value="1"
                        <?php if ($sequence->week_selection & 8) {?> checked="checked"<?php }?>/> 4th
                </label>
                <label class="inline">
                    <input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week5]" value="1"
                        <?php if ($sequence->week_selection & 16) {?> checked="checked"<?php }?>/> 5th
                </label>
            </td>
        </tr>

        </tbody>
    </table>
    <?php echo $form->errorSummary($sequence); ?>
    <div class="data-group">
        <div class="cols-10 large-offset-2 column">
            <?php echo EventAction::button('Save', 'save', array('level' => 'save'))->toHtml()?>
            <?php echo EventAction::link('Cancel',
                Yii::app()->createUrl('OphTrOperationbooking/admin/viewSequences'),
                array('level' => 'cancel')
            )->toHtml();
            ?>
            <?php if ($sequence->id) {?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo EventAction::button('View sessions', 'view_sessions', array(), array('class' => 'button small'))->toHtml()?>
                <?php echo EventAction::button('Add session', 'add_session_to_sequence', array(), array('class' => 'button small'))->toHtml()?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo EventAction::button('Delete sequence', 'delete_sequence', array('level' => 'warning'), array('class' => 'button small'))->toHtml()?>
            <?php }?>
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
        </div>
    </div>
    <?php $this->endWidget()?>
</div>

<div id="confirm_delete_sequence" title="Confirm delete sequence" style="display: none;">
    <div id="delete_sequence">
        <div class="alert-box alert with-icon">
            <strong>WARNING: This will remove the sequence from the system.<br/>This action cannot be undone.</strong>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <div class="buttons">
            <input type="hidden" id="medication_id" value="" />
            <button type="submit" class="warning btn_remove_sequence">Remove sequence</button>
            <button type="submit" class="secondary btn_cancel_remove_sequence">Cancel</button>
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
        </div>
    </div>
</div>
<script type="text/javascript">
    handleButton($('#et_cancel'),function(e) {
        e.preventDefault();
        window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSequences';
    });
    handleButton($('#et_save'),function(e) {
        $('#adminform').submit();
    });
    handleButton($('#et_view_sessions'),function(e) {
        e.preventDefault();
        window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions?sequence_id=<?php echo $sequence->id?>';
    });
    handleButton($('#et_add_session_to_sequence'),function(e) {
        e.preventDefault();
        window.location.href = baseUrl+'/OphTrOperationbooking/admin/addSession?sequence_id=<?php echo $sequence->id?>';
    });
    $('#OphTrOperationbooking_Operation_Sequence_start_date_0').change(function() {
        var d = new Date($(this).val());
        var day_id = d.getDay();
        if (day_id == 0) {
            day_id = 7;
        }

        if ($('#OphTrOperationbooking_Operation_Sequence_weekday').val() == '') {
            $('#OphTrOperationbooking_Operation_Sequence_weekday').val(day_id);
        }
    });

    handleButton($('#et_delete_sequence'),function(e) {
        e.preventDefault();
        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSequences',
            'data': "sequence[]=<?php echo $sequence->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    enableButtons();

                    $('#confirm_delete_sequence').dialog({
                        resizable: false,
                        modal: true,
                        width: 560
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "This sequence has one or more sessions with active bookings and so cannot be deleted."
                    }).open();
                    enableButtons();
                }
            }
        });
    });

    handleButton($('button.btn_remove_sequence'),function(e) {
        e.preventDefault();

        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSequences',
            'data': "sequence[]=<?php echo $sequence->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp == "1") {
                    $.ajax({
                        'type': 'POST',
                        'url': baseUrl+'/OphTrOperationbooking/admin/deleteSequences',
                        'data': "sequence[]=<?php echo $sequence->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                        'success': function(resp) {
                            if (resp == "1") {
                                window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSequences';
                            } else {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "There was an unexpected error deleting the sequence, please try again or contact support for assistance",
                                    onClose: function() {
                                        enableButtons();
                                        $('#confirm_delete_sequences').dialog('close');
                                    }
                                }).open();
                            }
                        }
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "This sequence now has one or more sessions with active bookings and so cannot be deleted.",
                        onClose: function() {
                            enableButtons();
                            $('#confirm_delete_sequences').dialog('close');
                        }
                    }).open();
                }
            }
        });
    });

    $('button.btn_cancel_remove_sequence').click(function(e) {
        e.preventDefault();
        $('#confirm_delete_sequence').dialog('close');
    });
    $('.time-picker').timepicker({ 'timeFormat': 'H:i:s', 'step' : 5 });
</script>