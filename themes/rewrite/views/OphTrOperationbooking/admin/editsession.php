<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="box admin">
	<h2><?php echo $session->id ? 'Edit' : 'Add'?> session</h2>
	<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
	<div>
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'adminform',
				'enableAjaxValidation'=>false,
				'focus'=>'#username'
			))?>
		<?php if (!$session->id) {?>
			<?php echo $form->textField($session,'sequence_id',array('size'=>10))?>
		<?php }?>
		<?php echo $form->dropDownList($session,'firm_id',Firm::model()->getListWithSpecialties(),array('empty'=>'- Emergency -'))?>
		<?php echo $form->dropDownList($session,'theatre_id',CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
		<?php if ($session->id) {?>
			<div id="div_OphTrOperationbooking_Operation_Session_date" class="row field-row">
				<div class="large-2 column">
					<label>Date:</label>
				</div>
				<div class="large-5 column end">
					<span class="label" style="margin-bottom: 0;"><?php echo $session->NHSDate('date')?></span>
				</div>
			</div>
		<?php } else {?>
			<?php echo $form->datePicker($session,'date',array('size'=>10))?>
		<?php }?>
		<?php echo $form->textField($session,'start_time',array('size'=>10))?>
		<?php echo $form->textField($session,'end_time',array('size'=>10))?>
		<?php echo $form->radioBoolean($session,'consultant')?>
		<?php echo $form->radioBoolean($session,'paediatric')?>
		<?php echo $form->radioBoolean($session,'anaesthetist')?>
		<?php echo $form->radioBoolean($session,'general_anaesthetic')?>
		<?php echo $form->radioBoolean($session,'available')?>
		<?php $this->endWidget()?>
	</div>
</div>

<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
<div>
	<?php echo EventAction::button('Save', 'save', null , array('class' => 'small'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'cancel', array('level' => 'warning'), array('class' => 'small'))->toHtml()?>
	<?php if ($session->id) {?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo EventAction::button('Delete session','delete_session',array('level' => 'warning'), array('class' => 'small'))->toHtml()?>
	<?php }?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<div id="confirm_delete_session" title="Confirm delete session" style="display: none;">
	<div>
		<div id="delete_session">
			<div class="alert-box alert with-icon">
				<strong>WARNING: This will remove the session from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="medication_id" value="" />
				<button type="submit" class="classy red venti btn_remove_session"><span class="button-span button-span-red">Remove session</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_session"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	handleButton($('#et_cancel'),function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewSessions';
	});

	handleButton($('#et_save'),function(e) {
		$('#adminform').submit();
	});

	handleButton($('#et_delete_session'),function(e) {
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
					new OpenEyes.Dialog.Alert({
						content: "This session has one or more active bookings and so cannot be deleted."
					}).open();
					enableButtons();
				}
			}
		});
	});

	handleButton($('button.btn_remove_session'),function(e) {
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
								new OpenEyes.Dialog.Alert({
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
					new OpenEyes.Dialog.Alert({
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

	$('button.btn_cancel_remove_session').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_session').dialog('close');
	});
</script>
