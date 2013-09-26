<?php // FIXME:?>
						<section class="box patient-info associated-data">
							<header class="box-header">
								<h3 class="box-title">
									<span class="icon-patient-clinician-hd_flag"></span>
									Previous operations
								</h3>
								<a href="#" class="toggle-trigger toggle-hide">
															<span class="icon-showhide">
																Show/hide this section
															</span>
								</a>
							</header>
								<table class="plain patient-data">
									<thead>
									<tr>
										<th>Date</th>
										<th>Operation</th>
										<th>Edit</th>
									</tr>
									</thead>
									<tbody>
									<?php
									// FIXME:
									foreach ($this->patient->previousOperations as $operation) {?>
										<tr>
											<td><?php echo $operation->dateText?></td>
											<td><?php if ($operation->side) { echo $operation->side->adjective.' ';}?><?php echo $operation->operation?></td>
											<td>
												<a href="#" class="small editOperation" rel="<?php echo $operation->id?>"><strong>Edit</strong></a>&nbsp;&nbsp;
												<a href="#" class="small removeOperation" rel="<?php echo $operation->id?>"><strong>Remove</strong></a>
											</td>
										</tr>
									<?php }?>
									</tbody>
								</table>

							<?php if (BaseController::checkUserLevel(4)) {?>


								<div class="box-actions">
									<form>
									<button id="btn-add_previous_operation" class="secondary small">
										Add Previous operation
									</button></form>
								</div>


								<div id="add_previous_operation" style="display: none;">
									<form class="form add-data">
										<fieldset class="field-row">
											<legend><strong>Add Previous Operation</strong></legend>
									<?php
									$form = $this->beginWidget('CActiveForm', array(
											'id'=>'add-previous_operation',
											'enableAjaxValidation'=>false,
											'htmlOptions' => array('class'=>'sliding'),
											'action'=>array('patient/addPreviousOperation'),
									))?>

									<input type="hidden" name="edit_operation_id" id="edit_operation_id" value="" />
									<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

											<div class="field-row row previousOperation">
												<div class="large-3 column">
													<label for="">Common operations:</label>
												</div>
												<div class="large-7 column data end">
													<select>
														<?php echo CHtml::dropDownList('common_previous_operation','',CHtml::listData(CommonPreviousOperation::model()->findAll(array('order'=>'display_order')),'id','name'),array('style'=>'width: 125px;','empty'=>'- Select -'))?>
													</select>
												</div>
											</div>
						
											<div class="previousOperationrow field-row row">
												<div class="large-3 column">
													<label for="">Operation:</label>
												</div>
												<div class="large-7 column end">
													<?php echo CHtml::textField('previous_operation','')?>
												</div>
											</div>



											<fieldset class="previous_operation row field-row">
												<legend class="large-3 column">
											Side:
													</legend>
													<div class="large-7 column end">
										<input type="radio" name="previous_operation_side" class="previous_operation_side" value="" checked="checked" /> None
										<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $eye) {?>
														<label class="inline"><input type="radio" name="previous_operation_side" class="previous_operation_side" value="<?php echo $eye->id?>" /> <?php echo $eye->name?></label>
										<?php }?>
									</fieldset>

									<?php $this->renderPartial('_fuzzy_date',array('class'=>'previousOperation'))?>

									<div class="previous_operations_form_errors"></div>

									<div align="right">
										<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_previous_operation_loader" style="display: none;" />
										<div class="buttons">
											<button type="submit" class="secondary small btn_save_previous_operation">
												Save
											</button>
											<button class="warning small btn_cancel_previous_operation">
												Cancel
											</button>
										</div>
									</div>

									<?php $this->endWidget()?>
										</fieldset>
									</form>
							<?php }?>

					</section>

				<div id="confirm_remove_operation_dialog" title="Confirm remove operation" style="display: none;">
					<div>
						<div id="delete_operation">
							<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
								<strong>WARNING: This will remove the operation from the patient record.</strong>
							</div>
							<p>
								<strong>Are you sure you want to proceed?</strong>
							</p>
							<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
								<input type="hidden" id="operation_id" value="" />
								<button type="submit" class="classy red venti btn_remove_operation"><span class="button-span button-span-red">Remove operation</span></button>
								<button type="submit" class="classy green venti btn_cancel_remove_operation"><span class="button-span button-span-green">Cancel</span></button>
								<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
							</div>
						</div>
					</div>
				</div>
<script type="text/javascript">
	$('#btn-add_previous_operation').click(function() {
		$('#previous_operation').val('');
		$('#edit_operation_id').val('');
		$('div.previousOperation input[name="previous_operation_side"][value=""]').attr('checked','checked');
		var d = new Date;
		$('div.previousOperation select[name="fuzzy_year"]').val(d.getFullYear());
		$('#add_previous_operation').slideToggle('fast');
		$('#btn-add_previous_operation').attr('disabled',true);
		$('#btn-add_previous_operation').removeClass('green').addClass('disabled');
		$('#btn-add_previous_operation span').removeClass('button-span-green').addClass('button-span-disabled');
	});
	$('button.btn_cancel_previous_operation').click(function() {
		$('#add_previous_operation').slideToggle('fast');
		$('#btn-add_previous_operation').attr('disabled',false);
		$('#btn-add_previous_operation').removeClass('disabled').addClass('green');
		$('#btn-add_previous_operation span').removeClass('button-span-disabled').addClass('button-span-green');
		return false;
	});
	$('#common_previous_operation').change(function() {
		$('#previous_operation').val($(this).children('option:selected').text());
		$(this).val(0);
	});
	$('button.btn_save_previous_operation').click(function() {
		if ($('#previous_operation').val().length <1) {
			new OpenEyes.Dialog.Alert({
				content: "Please enter an operation"
			}).open();
			return false;
		}
		$('img.add_previous_operation_loader').show();

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/patient/addPreviousOperation',
			'dataType': 'json',
			'data': $('#add-previous_operation').serialize(),
			'success': function(errors) {
				var ok = true;

				$('.previous_operations_form_errors').html('');

				for (var i in errors) {
					ok = false;
					$('div.previous_operations_form_errors').append('<div class="errorMessage">'+errors[i]+'</div>');
				}

				$('img.add_previous_operation_loader').hide();

				if (ok) {
					window.location.reload();
				}
			}
		});

		return false;
	});
	$('a.editOperation').click(function(e) {
		var operation_id = $(this).attr('rel');

		$.ajax({
			'type': 'GET',
			'dataType': 'json',
			'url': baseUrl+'/patient/getPreviousOperation?operation_id='+operation_id,
			'success': function(data) {
				$('#edit_operation_id').val(operation_id);
				$('#previous_operation').val(data['operation']);
				$('div.previousOperation input[name="previous_operation_side"][value="'+data['side_id']+'"]').attr('checked','checked');
				$('div.previousOperation select[name="fuzzy_day"]').val(data['fuzzy_day']);
				$('div.previousOperation select[name="fuzzy_month"]').val(data['fuzzy_month']);
				$('div.previousOperation select[name="fuzzy_year"]').val(data['fuzzy_year']);
				$('#add_previous_operation').slideToggle('fast');
				$('#btn-add_previous_operation').attr('disabled',true);
				$('#btn-add_previous_operation').removeClass('green').addClass('disabled');
				$('#btn-add_previous_operation span').removeClass('button-span-green').addClass('button-span-disabled');
			}
		});

		e.preventDefault();
	});

	$('.removeOperation').live('click',function() {
		$('#operation_id').val($(this).attr('rel'));

		$('#confirm_remove_operation_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_operation').click(function() {
		$("#confirm_remove_operation_dialog").dialog("close");

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removePreviousOperation?patient_id=<?php echo $this->patient->id?>&operation_id='+$('#operation_id').val(),
			'success': function(html) {
				if (html == 'success') {
					$('a.removeOperation[rel="'+$('#operation_id').val()+'"]').parent().parent().remove();
				} else {
					new OpenEyes.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the operation.\n\nPlease contact support for assistance."
					}).open();
				}
			},
			'error': function() {
				new OpenEyes.Dialog.Alert({
					content: "Sorry, an internal error occurred and we were unable to remove the operation.\n\nPlease contact support for assistance."
				}).open();
			}
		});

		return false;
	});

	$('button.btn_cancel_remove_operation').click(function() {
		$("#confirm_remove_operation_dialog").dialog("close");
		return false;
	});
</script>
