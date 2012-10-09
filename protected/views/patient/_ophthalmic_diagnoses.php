					<div class="whiteBox forClinicians">
						<div class="patient_actions">
							<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<div class="icon_patientIssue"></div>
						<h4>Ophthalmic diagnosis</h4>
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
										<option value="<?php echo $i?>"><?php echo $i?></option>
									<?php }?>
								</select>
								<select name="diagnosis_month" class="diagnosis_date_field">
									<option value="">Month (optional)</option>
									<?php foreach (array('January','February','March','April','May','June','July','August','September','October','November','December') as $i => $month) {?>
										<option value="<?php echo $i+1?>"><?php echo $month?></option>
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
		var diagnosis_id = $(this).attr('rel');
		var anchor = $(this);

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removediagnosis?patient_id=<?php echo $this->patient->id?>&diagnosis_id='+diagnosis_id,
			'success': function(html) {
				if (html == 'success') {
					anchor.parent().parent().remove();
				} else {
					alert("Sorry, an internal error occurred and we were unable to remove the diagnosis.\n\nPlease contact support for assistance.");
				}
			}
		});

		return false;
	});
</script>
