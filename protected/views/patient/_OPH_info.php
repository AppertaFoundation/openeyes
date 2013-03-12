					<div class="whiteBox forClinicians" id="OPH_info">
						<div class="patient_actions">
							<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<div class="icon_patientIssue"></div>
						<h4>CVI Status</h4>
						<div class="data_row">
							<table class="subtleWhite">
								<thead>
									<tr>
										<th width="85px">Date</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$info = $this->patient->getOPHInfo();
									?>
									<tr>
										<td><?php echo Helper::formatFuzzyDate($info->cvi_status_date); ?></td>
										<td><?php echo $info->cvi_status->name; ?></td>
									</tr>
								</tbody>
							</table>
							
							<div align="center" style="margin-top:10px;">
								<form><button id="btn-edit_oph_info" class="classy green mini" type="button"><span class="button-span button-span-green">Edit</span></button></form>
							</div>
							
							<div id="edit_oph_info" style="display: none;">
								<h5>Edit CVI Status</h5>
								<?php 
								$form = $this->beginWidget('CActiveForm', array(
										'id'=>'edit-oph_info',
										'enableAjaxValidation'=>true,
										'clientOptions'=>array(
											'validateOnSubmit' => true,
											'validateOnChange' => false,
											'afterValidate' => "js:function(form, data, hasError) {
											if (hasError) {
												// mask the ajax loader image again
												$('img.edit_oph_info_loader').hide();
											}
											else {
												return true;
											}}"
										),
										'htmlOptions' => array('class'=>'sliding'),
										'action'=>array('patient/editophinfo'),
								))?>
								<?php echo CHtml::activeDropDownList($info, 'cvi_status_id', CHtml::listData(PatientOphInfoCviStatus::model()->findAll(array('order'=>'display_order')),'id','name')) ?>
								
								<?php echo $form->error($info, 'cvi_status_date'); ?>
								
								<?php 
								$this->renderPartial('_diagnosis_date')?>
								
								<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
								<div align="right">
									<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="edit_oph_info_loader" style="display: none;" />
									<button class="classy green mini btn_save_oph_info" type="submit"><span class="button-span button-span-green">Save</span></button>
									<button class="classy red mini btn_cancel_oph_info" type="submit"><span class="button-span button-span-red">Cancel</span></button>
								</div>
								
								<?php $this->endWidget(); ?>
								
							</div>	
						</div>
					</div>

<script type="text/javascript">
	$('#btn-edit_oph_info').click(function() {
		$('#edit_oph_info').slideToggle('fast');
		$('#btn-edit_oph_info').attr('disabled',true);
		$('#btn-edit_oph_info').removeClass('green').addClass('disabled');
		$('#btn-edit_oph_info span').removeClass('button-span-green').addClass('button-span-disabled');
	});
	$('button.btn_cancel_oph_info').click(function() {
		$('#edit_oph_info').slideToggle('fast');
		$('#btn-edit_oph_info').attr('disabled',false);
		$('#btn-edit_oph_info').removeClass('disabled').addClass('green');
		$('#btn-edit_oph_info span').removeClass('button-span-disabled').addClass('button-span-green');
		return false;
	});
	$('button.btn_save_oph_info').click(function() {
		$('.errorMessage').slideUp();
		$('img.edit_oph_info_loader').show();
		return true;
	});
</script>
