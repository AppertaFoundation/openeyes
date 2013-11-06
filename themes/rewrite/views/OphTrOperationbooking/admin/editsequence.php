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
	<h2><?php echo $sequence->id ? 'Edit' : 'Add'?> sequence</h2>
	<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
	<div>
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
				'id'=>'adminform',
				'enableAjaxValidation'=>false,
				'htmlOptions' => array('class'=>'sliding'),
				'focus'=>'#username'
			))?>
		<?php echo $form->dropDownList($sequence,'firm_id',Firm::model()->getListWithSpecialties(),array('empty'=>'- Emergency -'))?>
		<?php echo $form->dropDownList($sequence,'theatre_id',CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
		<?php echo $form->datePicker($sequence,'start_date',array(),array('null'=>true))?>
		<?php echo $form->datePicker($sequence,'end_date',array(),array('null'=>true))?>
		<?php echo $form->dropDownList($sequence,'weekday',array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'),array('empty'=>'- Weekday -'))?>
		<?php echo $form->textField($sequence,'start_time',array('size'=>10))?>
		<?php echo $form->textField($sequence,'end_time',array('size'=>10))?>
		<?php echo $form->dropDownList($sequence,'interval_id',CHtml::listData(OphTrOperationbooking_Operation_Sequence_Interval::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- None -'))?>
		<?php echo $form->radioBoolean($sequence,'consultant')?>
		<?php echo $form->radioBoolean($sequence,'paediatric')?>
		<?php echo $form->radioBoolean($sequence,'anaesthetist')?>
		<?php echo $form->radioBoolean($sequence,'general_anaesthetic')?>
		<div id="OphTrOperationbooking_Operation_Sequence_week_selection" class="row field-row">
			<div class="large-2 column">
				<label>Week selection:</label>
			</div>
			<div class="large-5 column end">
				<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week1]" value="0" />
				<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week2]" value="0" />
				<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week3]" value="0" />
				<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week4]" value="0" />
				<input type="hidden" name="OphTrOperationbooking_Operation_Sequence[week_selection_week5]" value="0" />
				<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week1]" value="1" <?php if ($sequence->week_selection & 1) {?> checked="checked"<?php }?>/>1st&nbsp;
				<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week2]" value="1" <?php if ($sequence->week_selection & 2) {?> checked="checked"<?php }?>/>2nd&nbsp;
				<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week3]" value="1" <?php if ($sequence->week_selection & 4) {?> checked="checked"<?php }?>/>3rd&nbsp;
				<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week4]" value="1" <?php if ($sequence->week_selection & 8) {?> checked="checked"<?php }?>/>4th&nbsp;
				<input type="checkbox" name="OphTrOperationbooking_Operation_Sequence[week_selection_week5]" value="1" <?php if ($sequence->week_selection & 16) {?> checked="checked"<?php }?>/>5th
			</div>
		</div>
		<?php $this->endWidget()?>
	</div>

</div>
<?php echo $this->renderPartial('//admin/_form_errors',array('errors'=>$errors))?>
<div>
	<?php echo EventAction::button('Save', 'save', array('level' => 'secondary'),array('class'=>'button small'))->toHtml()?>
	<?php echo EventAction::button('Cancel', 'cancel', array('level' => 'warning'),array('class'=>'button small'))->toHtml()?>
	<?php if ($sequence->id) {?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo EventAction::button('View sessions','view_sessions',array(),array('class'=>'button small'))->toHtml()?>
		<?php echo EventAction::button('Add session','add_session_to_sequence',array(),array('class'=>'button small'))->toHtml()?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo EventAction::button('Delete sequence','delete_sequence',array('level'=>'warning'),array('class'=>'button small'))->toHtml()?>
	<?php }?>
	<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
</div>
<div id="confirm_delete_sequence" title="Confirm delete sequence" style="display: none;">
	<div>
		<div id="delete_sequence">
			<div class="alert-box alert with-icon">
				<strong>WARNING: This will remove the sequence from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="medication_id" value="" />
				<button type="submit" class="classy red venti btn_remove_sequence"><span class="button-span button-span-red">Remove sequence</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_sequence"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
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
					new OpenEyes.Dialog.Alert({
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
								new OpenEyes.Dialog.Alert({
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
					new OpenEyes.Dialog.Alert({
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
</script>
