					<div class="whiteBox forClinicians">
						<div class="patient_actions">
							<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<div class="icon_patientIssue"></div>
						<h4>Ophthalmic diagnoses</h4>
						<div class="data_row">
							<table class="subtleWhite">
								<thead>
									<tr>
										<th width="80px">Date</th>
										<th>Diagnosis</th>
										<th>Edit</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->patient->ophthalmicDiagnoses as $diagnosis) {?>
										<tr>
											<td><?php echo $diagnosis->dateText?></td>
											<td><?php echo $diagnosis->eye->adjective?> <?php echo $diagnosis->disorder->term?></td>
											<td><a href="#" class="small removeDiagnosis" rel="<?php echo $diagnosis->id?>"><strong>Remove</strong></a></td>
										</tr>
									<?php }?>
								</tbody>
							</table>
							
							<div align="center" style="margin-top:10px;">
								<form><button id="btn-add_new_ophthalmic_diagnosis" class="classy green mini" type="button"><span class="button-span button-span-green">Add Ophthalmic Diagnosis</span></button></form>
							</div>	
						</div>
						<div class="data_row" id="add_new_ophthalmic_diagnosis" style="display: none;">
							<h5>Add ophthalmic diagnosis</h5>	
							<?php
							$form = $this->beginWidget('CActiveForm', array(
									'id'=>'add-ophthalmic-diagnosis',
									'enableAjaxValidation'=>false,
									'htmlOptions' => array('class'=>'sliding'),
									'action'=>array('patient/adddiagnosis'),
							))?>

							<?php $form->widget('application.widgets.DiagnosisSelection',array(
									'field' => 'ophthalmic_disorder_id',
									'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
									'restrict' => 'ophthalmic',
									'default' => false,
									'layout' => 'patientSummary',
							))?>

							<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

							<div class="diagnosis_eye">
								<span class="diagnosis_eye_label">
										Eye:
								</span>
								<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $i => $eye) {?>
									<input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="<?php echo $eye->id?>"<?php if ($i==0) {?> checked="checked"<?php }?> /> <?php echo $eye->name?>
								<?php }?>
							</div>

							<div class="diagnosis_date">
								<span class="diagnosis_date_label">
									Date:
								</span>
								<select name="diagnosis_day" class="diagnosis_date_field">
									<option value="">Day (optional)</option>
									<?php for ($i=1;$i<31;$i++) {?>
										<option value="<?php echo $i?>"<?php if (date('j') == $i) {?> selected="selected"<?php }?>><?php echo $i?></option>
									<?php }?>
								</select>
								<select name="diagnosis_month" class="diagnosis_date_field">
									<option value="">Month (optional)</option>
									<?php foreach (array('January','February','March','April','May','June','July','August','September','October','November','December') as $i => $month) {?>
										<option value="<?php echo $i+1?>"<?php if (date('F') == $month) {?> selected="selected"<?php }?>><?php echo $month?></option>
									<?php }?>
								</select>
								<select name="diagnosis_year" class="diagnosis_date_field">
									<?php for ($i=1990;$i<=date('Y');$i++) {?>
										<option value="<?php echo $i?>"<?php if ($i == date('Y')) {?> selected="selected"<?php }?>><?php echo $i?></option>
									<?php }?>
								</select>
							</div>
							<div align="right">
								<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_ophthalmic_diagnosis_loader" style="display: none;" />
								<button class="classy green mini btn_save_ophthalmic_diagnosis" type="submit"><span class="button-span button-span-green">Save</span></button>
								<button class="classy red mini btn_cancel_ophthalmic_diagnosis" type="submit"><span class="button-span button-span-red">Cancel</span></button>
							</div>

							<?php $this->endWidget()?>
						</div>
					</div>
				<div id="confirm_remove_diagnosis_dialog" title="Confirm remove diagnosis" style="display: none;">
					<div id="schedule">
						<div id="delete_event">
							<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
								<strong>WARNING: This will remove the diagnosis from the patient record.
							</div>
							<p>
								<strong>Are you sure you want to proceed?</strong>
							</p>
							<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
								<input type="hidden" id="diagnosis_id" value="" />
								<button type="submit" class="classy red venti btn_remove_diagnosis"><span class="button-span button-span-red">Remove diagnosis</span></button>
								<button type="submit" class="classy green venti btn_cancel_remove_diagnosis"><span class="button-span button-span-green">Cancel</span></button>
								<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
							</div>
						</div>
					</div>
				</div>
<script type="text/javascript">
	$('#btn-add_new_ophthalmic_diagnosis').click(function() {
		$('#add_new_ophthalmic_diagnosis').slideToggle('fast');
	});
	$('button.btn_cancel_ophthalmic_diagnosis').click(function() {
		$('#add_new_ophthalmic_diagnosis').slideToggle('fast');
		return false;
	});
	$('button.btn_save_ophthalmic_diagnosis').click(function() {
		if (!$('#DiagnosisSelection_ophthalmic_disorder_id_savedDiagnosis').val()) {
			alert('Please select a diagnosis.');
			return false;
		}
		$('img.add_ophthalmic_diagnosis_loader').show();
		return true;
	});
	$('.removeDiagnosis').live('click',function() {
		$('#diagnosis_id').val($(this).attr('rel'));

		$('#confirm_remove_diagnosis_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_diagnosis').click(function() {
		$("#confirm_remove_diagnosis_dialog").dialog("close");

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removediagnosis?patient_id=<?php echo $this->patient->id?>&diagnosis_id='+$('#diagnosis_id').val(),
			'success': function(html) {
				if (html == 'success') {
					$('a.removeDiagnosis[rel="'+$('#diagnosis_id').val()+'"]').parent().parent().remove();
				} else {
					alert("Sorry, an internal error occurred and we were unable to remove the diagnosis.\n\nPlease contact support for assistance.");
				}
			}
		});

		return false;
	});

	$('button.btn_cancel_remove_diagnosis').click(function() {
		$("#confirm_remove_diagnosis_dialog").dialog("close");
		return false;
	});
</script>
