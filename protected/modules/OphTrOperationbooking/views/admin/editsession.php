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
	<h2><?php echo $session->id ? 'Edit' : 'Add'?> session</h2>
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
	<?php echo $form->errorSummary($session); ?>
	<?php if ($session->sequence_id) {?>
		<?php echo $form->textField($session, 'sequence_id', array('readonly' => true), array(), array('field' => 2))?>
	<?php }?>
	<?php echo $form->dropDownList($session, 'firm_id', Firm::model()->getListWithSpecialties(), array('empty' => '- Emergency -'))?>
	<?php echo $form->dropDownList($session, 'theatre_id', 'OphTrOperationbooking_Operation_Theatre', array('empty' => '- None -'))?>
	<?php if ($session->id) {?>
		<div id="div_OphTrOperationbooking_Operation_Session_date" class="data-group">
			<div class="cols-2 column">
				<div class="field-label">Date:</div>
			</div>
			<div class="cols-5 column end">
				<div class="field-value"><?php echo $session->NHSDate('date')?></div>
			</div>
		</div>
	<?php } else {?>
		<?php echo $form->datePicker($session, 'date', array(), array(), array('field' => 2))?>
	<?php }?>
	<?php echo $form->textField($session, 'start_time', array('class' => 'time-picker'), array(), array('field' => 2))?>
	<?php echo $form->textField($session, 'end_time', array('class' => 'time-picker'), array(), array('field' => 2))?>
	<?php echo $form->textField($session, 'default_admission_time', array('class' => 'time-picker'), array(), array('field' => 2))?>
	<?php echo $form->textField($session, 'max_procedures', array(), array(), array('field' => 2)); ?>
	<?php if ($current = $session->getBookedProcedureCount()) { ?>
		<fieldset id="procedure_count_wrapper" class="data-group <?php if ($session->max_procedures && $current > $session->max_procedures) { echo ' warn'; }?>">
			<div class="cols-2 column">
				<div class="field-label">Current Booked Procedures:</div>
			</div>
			<div class="cols-5 column end">
				<div class="field-value" id="current-proc-count"><?php echo $current ?></div>
			</div>
		</fieldset>
	<?php } ?>
    <?php echo $form->textField($session, 'max_complex_procedures', array(), array(), array('field' => 2)); ?>
	<?php echo $form->radioBoolean($session, 'consultant')?>
	<?php echo $form->radioBoolean($session, 'paediatric')?>
	<?php echo $form->radioBoolean($session, 'anaesthetist')?>
	<?php echo $form->radioBoolean($session, 'general_anaesthetic')?>
	<?php echo $form->radioBoolean($session, 'available')?>
	<fieldset id="unavailablereason_id_wrapper" class="data-group"<?php if ($session->available) {?> style="display: none;"<?php } ?>>
		<div class="cols-2 column">
			<label for="OphTrOperationbooking_Operation_Session_unavailablereason_id"><?php echo $session->getAttributeLabel('unavailablereason_id'); ?>:</label>
		</div>
		<div class="cols-5 column end">
			<?php echo $form->dropDownList($session, 'unavailablereason_id', CHtml::listData($session->getUnavailableReasonList(), 'id', 'name'), array('empty' => 'Select', 'nowrapper' => true))?>
		</div>
	</fieldset>
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
			<button type="submit" class="warning btn_remove_session">Remove session</button>
			<button type="submit" class="secondary btn_cancel_remove_session">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>
<script type="text/javascript">
	$('input[name="OphTrOperationbooking_Operation_Session[available]"]').live('change', function() {
		if ($(this).val() == '1') {
			$('#unavailablereason_id_wrapper').hide();
			$('#OphTrOperationbooking_Operation_Session_unavailablereason_id').data('orig', $('#OphTrOperationbooking_Operation_Session_unavailablereason_id').val());
			$('#OphTrOperationbooking_Operation_Session_unavailablereason_id').val('');
		}
		else {
			$('#OphTrOperationbooking_Operation_Session_unavailablereason_id').val($('#OphTrOperationbooking_Operation_Session_unavailablereason_id').data('orig'));
			$('#unavailablereason_id_wrapper').show();
		}
	});

	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions';
	});

	handleButton($('#et_save'),function(e) {
		$('#adminform').submit();
	});

	handleButton($('#et_delete'),function(e) {
		e.preventDefault();
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteSessions',
			'data': "session[]=<?php echo $session->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
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
				if (resp == "1") {
					$.ajax({
						'type': 'POST',
						'url': baseUrl+'/OphTrOperationbooking/admin/deleteSessions',
						'data': "session[]=<?php echo $session->id?>&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
						'success': function(resp) {
							if (resp == "1") {
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
	$('.time-picker').timepicker({ 'timeFormat': 'H:i:s', 'step' : 5 });
</script>
