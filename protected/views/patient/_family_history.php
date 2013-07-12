					<div class="whiteBox forClinicians">
						<div class="patient_actions">
							<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<div class="icon_patientIssue"></div>
						<h4>Family history</h4>
						<div class="data_row">
							<table class="subtleWhite">
								<thead>
									<tr>
										<th width="85px">Relative</th>
										<th>Side</th>
										<th>Condition</th>
										<th>Comments</th>
										<th>Edit</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->patient->familyHistory as $history) {?>
										<tr>
											<td><?php echo $history->relative->name?></td>
											<td><?php echo $history->side->name?></td>
											<td><?php echo $history->condition->name?></td>
											<td><?php echo $history->comments?></td>
											<td>
												<a href="#" class="small editFamilyHistory" rel="<?php echo $history->id?>"><strong>Edit</strong></a>&nbsp;&nbsp;
												<a href="#" class="small removeFamilyHistory" rel="<?php echo $history->id?>"><strong>Remove</strong></a>
											</td>
										</tr>
									<?php }?>
								</tbody>
							</table>

							<?php if (BaseController::checkUserLevel(4)) { ?>
								<div align="center" style="margin-top:10px;">
									<form><button id="btn-add_family_history" class="classy green mini" type="button"><span class="button-span button-span-green">Add family history</span></button></form>
								</div>
								<div id="add_family_history" style="display: none;">
									<h5>Add family history</h5>
									<?php
									$form = $this->beginWidget('CActiveForm', array(
											'id'=>'add-family_history',
											'enableAjaxValidation'=>false,
											'htmlOptions' => array('class'=>'sliding'),
											'action'=>array('patient/addFamilyHistory'),
									))?>

									<input type="hidden" name="edit_family_history_id" id="edit_family_history_id" value="" />
									<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

									<div class="familyHistory">
										<div class="label">
											Relative:
										</div>
										<div class="data">
											<?php echo CHtml::dropDownList('relative_id','',CHtml::listData(FamilyHistoryRelative::model()->findAll(array('order'=>'display_order')),'id','name'),array('style'=>'width: 125px;','empty'=>'- Select -'))?>
										</div>
									</div>

									<div class="familyHistory">
										<div class="label">
											Side:
										</div>
										<div class="data">
											<?php echo CHtml::dropDownList('side_id','',CHtml::listData(FamilyHistorySide::model()->findAll(array('order'=>'display_order')),'id','name'),array('style'=>'width: 125px;'))?>
										</div>
									</div>

									<div class="familyHistory">
										<div class="label">
											Condition:
										</div>
										<div class="data">
											<?php echo CHtml::dropDownList('condition_id','',CHtml::listData(FamilyHistoryCondition::model()->findAll(array('order'=>'display_order')),'id','name'),array('style'=>'width: 125px;','empty'=>'- Select -'))?>
										</div>
									</div>

									<div class="familyHistory">
										<div class="label">
											Comments:
										</div>
										<div class="data">
											<?php echo CHtml::textField('comments','')?>
										</div>
									</div>

									<div align="right">
										<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_family_history_loader" style="display: none;" />
										<button class="classy green mini btn_save_family_history" type="submit"><span class="button-span button-span-green">Save</span></button>
										<button class="classy red mini btn_cancel_family_history" type="submit"><span class="button-span button-span-red">Cancel</span></button>
									</div>

									<?php $this->endWidget()?>
								</div>
							<?php }?>
						</div>
					</div>

				<div id="confirm_remove_family_history_dialog" title="Confirm remove family history" style="display: none;">
					<div>
						<div id="delete_family_history">
							<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
								<strong>WARNING: This will remove the family_history from the patient record.</strong>
							</div>
							<p>
								<strong>Are you sure you want to proceed?</strong>
							</p>
							<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
								<input type="hidden" id="family_history_id" value="" />
								<button type="submit" class="classy red venti btn_remove_family_history"><span class="button-span button-span-red">Remove family_history</span></button>
								<button type="submit" class="classy green venti btn_cancel_remove_family_history"><span class="button-span button-span-green">Cancel</span></button>
								<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
							</div>
						</div>
					</div>
				</div>
<script type="text/javascript">
	$('#btn-add_family_history').click(function() {
		$('#relative_id').val('');
		$('div.familyHistory #side_id').val('');
		$('#condition_id').val('');
		$('div.familyHistory #comments').val('');
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',true);
		$('#btn-add_family_history').removeClass('green').addClass('disabled');
		$('#btn-add_family_history span').removeClass('button-span-green').addClass('button-span-disabled');
	});
	$('button.btn_cancel_family_history').click(function() {
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',false);
		$('#btn-add_family_history').removeClass('disabled').addClass('green');
		$('#btn-add_family_history span').removeClass('button-span-disabled').addClass('button-span-green');
		return false;
	});
	$('button.btn_save_family_history').click(function() {
		if ($('#relative_id').val() == '') {
			alert("Please select a relative");
			return false;
		}
		if ($('#side_id').val() == '') {
			alert("Please select a side");
			return false;
		}
		if ($('#condition_id').val() == '') {
			alert("Please select a condition");
			return false;
		}
		$('img.add_family_history_loader').show();
		return true;
	});
	$('a.editFamilyHistory').click(function(e) {
		var history_id = $(this).attr('rel');

		$('#edit_family_history_id').val(history_id);
		var relative = $(this).parent().parent().children('td:first').text();
		$('#relative_id').children('option').map(function() {
			if ($(this).text() == relative) {
				$(this).attr('selected','selected');
			}
		});
		var side = $(this).parent().parent().children('td:nth-child(2)').text();
		$('#side_id').children('option').map(function() {
			if ($(this).text() == side) {
				$(this).attr('selected','selected');
			}
		});
		var condition = $(this).parent().parent().children('td:nth-child(3)').text();
		$('#condition_id').children('option').map(function() {
			if ($(this).text() == condition) {
				$(this).attr('selected','selected');
			}
		});
		$('div.familyHistory #comments').val($(this).parent().prev('td').text());
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',true);
		$('#btn-add_family_history').removeClass('green').addClass('disabled');
		$('#btn-add_family_history span').removeClass('button-span-green').addClass('button-span-disabled');

		e.preventDefault();
	});

	$('.removeFamilyHistory').live('click',function() {
		$('#family_history_id').val($(this).attr('rel'));

		$('#confirm_remove_family_history_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_family_history').click(function() {
		$("#confirm_remove_family_history_dialog").dialog("close");

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removeFamilyHistory?patient_id=<?php echo $this->patient->id?>&family_history_id='+$('#family_history_id').val(),
			'success': function(html) {
				if (html == 'success') {
					$('a.removeFamilyHistory[rel="'+$('#family_history_id').val()+'"]').parent().parent().remove();
				} else {
					alert("Sorry, an internal error occurred and we were unable to remove the family_history.\n\nPlease contact support for assistance.");
				}
			},
			'error': function() {
				alert("Sorry, an internal error occurred and we were unable to remove the family_history.\n\nPlease contact support for assistance.");
			}
		});

		return false;
	});

	$('button.btn_cancel_remove_family_history').click(function() {
		$("#confirm_remove_family_history_dialog").dialog("close");
		return false;
	});
</script>
