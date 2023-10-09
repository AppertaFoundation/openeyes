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

/**
 * @var $session OphTrOperationbooking_Operation_Session
 * @var $this AdminController
 * @var $form BaseEventTypeCActiveForm
 */
if ($this->checkAccess('admin')) {
    $theatres = OphTrOperationbooking_Operation_Theatre::model()->findAll();
} else {
    $theatres = OphTrOperationbooking_Operation_Theatre::getTheatresForCurrentInstitution();
}
?>
<div class="box admin cols-5">
    <h2><?php echo $session->id ? 'Edit' : 'Add'?> session</h2>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
        ]
    )?>
    <?php echo $form->errorSummary($session); ?>

    <table class="standard cols-full">
        <tbody>
            <?php if ($session->sequence_id) {?>
                <tr>
                    <td>
                        <?= $form->labelEx($session, 'sequence_id') ?>
                    </td>
                    <td>
                        <?= $form->textField($session, 'sequence_id', ['readonly' => true, 'nowrapper' => true])?>
                    </td>
                </tr>
            <?php }?>
            <tr>
                <td>
                    <?= $form->labelEx($session, 'firm_id') ?>
                </td>
                <td>
                    <?= $form->dropDownList($session, 'firm_id', Firm::model()->getListWithSpecialties(), ['empty' => '- Emergency -', 'nowrapper' => true, 'class' => 'cols-12', 'data-test' => 'session-context'])?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->labelEx($session, 'theatre_id') ?>
                </td>
                <td>
                    <?= $form->dropDownList($session, 'theatre_id', CHtml::listData($theatres, 'id', 'name'), ['empty' => '- None -', 'nowrapper' => true, 'data-test' => 'session-theatre'])?>
                </td>
            </tr>
            <tr>
                <?php if ($session->id) {?>
                    <td>Date</td>
                    <td><?= $session->NHSDate('date')?></td>
                <?php } else {?>
                    <td><?= $form->labelEx($session, 'date') ?></td>
                    <td><?= $form->datePickerNative($session, 'date', [], ['nowrapper' => true, 'data-test' => 'session-date'])?></td>
                <?php }?>
            </tr>
            <tr>
                <td><?= $form->labelEx($session, 'start_time') ?></td>
                <td>
                    <?= $form->textField($session, 'start_time', ['nowrapper' => true, 'data-test' => 'session-start-time'])?>
                </td>
            </tr>
            <tr>
                <td><?= $form->labelEx($session, 'end_time') ?></td>
                <td><?= $form->textField($session, 'end_time', ['nowrapper' => true, 'data-test' => 'session-end-time'])?></td>
            </tr>
            <tr>
                <td><?= $form->labelEx($session, 'default_admission_time') ?></td>
                <td><?= $form->textField($session, 'default_admission_time', ['nowrapper' => true])?></td>
            </tr>
            <tr>
                <td><?= $form->labelEx($session, 'max_procedures') ?></td>
                <td><?= $form->textField($session, 'max_procedures', ['nowrapper' => true, 'data-test' => 'session-max-procedures']) ?></td>
            </tr>
            <tr>
                <td><?= $form->labelEx($session, 'max_complex_bookings') ?></td>
                <td><?= $form->textField($session, 'max_complex_bookings', ['nowrapper' => true, 'data-test' => 'session-max-complex-bookings']) ?></td>
            </tr>
            <?php $current = $session->getBookedProcedureCount();
            if ($current) { ?>
                    <tr class = "<?= $session->isProcedureCountLimited() && $current > $session->getMaxProcedureCount() ? "alert-box alert" : "" ?>">
                        <td>Current Booked Procedures</td>
                        <td>
                            <div class="field-value" id="current-proc-count"><?= $current ?></div>
                        </td>
                    </tr>
            <?php }
            ?>

            <?php $boolean_fields = ['consultant', 'paediatric', 'anaesthetist', 'general_anaesthetic', 'available'];
            foreach ($boolean_fields as $field) : ?>
                <tr>
                    <td><?= $form->labelEx($session, $field) ?></td>
                    <td><?= $form->radioBoolean($session, $field, ['nowrapper' => true, 'test' => 'session-booleans'])?></td>
                </tr>
            <?php endforeach; ?>

            <tr id="unavailablereason_id_wrapper" <?php if ($session->available) {
                ?> style="display: none;"<?php
                                                  } ?> >
                <td>
                    <label for="OphTrOperationbooking_Operation_Session_unavailablereason_id"><?= $session->getAttributeLabel('unavailablereason_id') ?>:</label>
                </td>
                <td>
                    <?= $form->dropDownList($session, 'unavailablereason_id', CHtml::listData($session->getUnavailableReasonList(), 'id', 'name'), array('empty' => 'Select', 'nowrapper' => true))?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php echo $form->errorSummary($session); ?>
    <?php echo $form->formActions(array(
        'delete' => $session->id ? 'Delete' : false,
    ));?>
    <?php $this->endWidget()?>
</div>

<div id="confirm_delete_session" title="Confirm delete session" style="display: none;">
    <div id="delete_session">
        <div class="alert-box alert with-icon">
            <strong>WARNING: This will remove the session from the system.<br/>This action cannot be undone.</strong>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <div class="buttons">
            <input type="hidden" id="medication_id" value="" />
            <button type="submit" class="warning btn_remove_session" data-test="remove-session">Remove session</button>
            <button type="submit" class="secondary btn_cancel_remove_session">Cancel</button>
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
        </div>
    </div>
</div>


<script type="text/javascript">
    $('input[name="OphTrOperationbooking_Operation_Session[available]"]').live('change', function() {
        let $unavail_reason_id = $('#OphTrOperationbooking_Operation_Session_unavailablereason_id');
        if ($(this).val() === '1') {
            
            $('#unavailablereason_id_wrapper').hide();
            $unavail_reason_id.data('orig', $unavail_reason_id.val());
            $unavail_reason_id.val('');
        }
        else {
            $unavail_reason_id.val($unavail_reason_id.data('orig'));
            $('#unavailablereason_id_wrapper').show();
        }
    });

    handleButton($('#et_cancel'),function(e) {
        e.preventDefault();
        window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions';
    });

    handleButton($('#et_save'),function() {
        $('#adminform').submit();
    });

    handleButton($('#et_delete'),function(e) {
        e.preventDefault();
        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSessions',
            'data': "session[]=<?php echo $session->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp === "1") {
                    enableButtons();

                    $('#confirm_delete_session').dialog({
                        resizable: false,
                        modal: true,
                        width: 560
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "This session has one or more active bookings and so cannot be deleted."
                    }).open();
                    enableButtons();
                }
            }
        });
    });

    handleButton($('.btn_remove_session'),function(e) {
        e.preventDefault();

        $.ajax({
            'type': 'POST',
            'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSessions',
            'data': "session[]=<?php echo $session->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
            'success': function(resp) {
                if (resp === "1") {
                    $.ajax({
                        'type': 'POST',
                        'url': baseUrl+'/OphTrOperationbooking/admin/deleteSessions',
                        'data': "session[]=<?php echo $session->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
                        'success': function(resp) {
                            if (resp === "1") {
                                window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions';
                            } else {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "There was an unexpected error deleting the session, please try again or contact support for assistance",
                                    onClose: function() {
                                        enableButtons();
                                        $('#confirm_delete_sessions').dialog('close');
                                    }
                                }).open();
                            }
                        }
                    });
                } else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "This session has one or more active bookings and so cannot be deleted.",
                        onClose: function() {
                            enableButtons();
                            $('#confirm_delete_sessions').dialog('close');
                        }
                    }).open();
                }
            }
        });
    });

    $('.btn_cancel_remove_session').click(function(e) {
        e.preventDefault();
        $('#confirm_delete_session').dialog('close');
    });
</script>