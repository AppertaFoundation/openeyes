					<div class="whiteBox forClinicians">
						<div class="patient_actions">
							<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<div class="icon_patientIssue"></div>
						<h4>Medication</h4>
						<div class="data_row">
							<table class="subtleWhite">
								<thead>
									<tr>
										<th width="85px">Medication</th>
										<th>Route</th>
										<th>Comments</th>
										<th>Edit</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->patient->medications as $medication) {?>
										<tr>
											<td><?php echo $medication->medication?></td>
											<td><?php echo $medication->route->name?></td>
											<td><?php echo $medication->comments?></td>
											<td>
												<a href="#" class="small editMedication" rel="<?php echo $medication->id?>"><strong>Edit</strong></a>&nbsp;&nbsp;
												<a href="#" class="small removeMedication" rel="<?php echo $medication->id?>"><strong>Remove</strong></a>
											</td>
										</tr>
									<?php }?>
								</tbody>
							</table>
							
							<div align="center" style="margin-top:10px;">
								<form><button id="btn-add_medication" class="classy green mini" type="button"><span class="button-span button-span-green">Add medication</span></button></form>
							</div>
							<div id="add_medication" style="display: none;">
								<h5>Add medication</h5>	
								<?php
								$form = $this->beginWidget('CActiveForm', array(
										'id'=>'add-medication',
										'enableAjaxValidation'=>false,
										'htmlOptions' => array('class'=>'sliding'),
										'action'=>array('patient/addMedication'),
								))?>
	
								<input type="hidden" name="edit_medication_id" id="edit_medication_id" value="" />
								<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
	
								<div class="patientMedication">
									<div class="label">
										Medication:
									</div>
									<div class="data">
										<?php echo CHtml::textField('medication','')?>
									</div>
								</div>

								<div class="patientMedication">
									<div class="label">
										Route:
									</div>
									<div class="data">
										<?php echo CHtml::dropDownList('route','',CHtml::listData(DrugRoute::model()->findAll(),'id','name'),array('empty'=>'- Select -'))?>
									</div>
								</div>

								<div class="patientMedication">
									<div class="label">
										Comments:
									</div>
									<div class="data">
										<?php echo CHtml::textField('comments','')?>
									</div>
								</div>

								<div align="right">
									<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_medication_loader" style="display: none;" />
									<button class="classy green mini btn_save_medication" type="submit"><span class="button-span button-span-green">Save</span></button>
									<button class="classy red mini btn_cancel_medication" type="submit"><span class="button-span button-span-red">Cancel</span></button>
								</div>
	
								<?php $this->endWidget()?>
							</div>	
						</div>
					</div>

				<div id="confirm_remove_medication_dialog" title="Confirm remove medication" style="display: none;">
					<div>
						<div id="delete_medication">
							<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
								<strong>WARNING: This will remove the medication from the patient record.</strong>
							</div>
							<p>
								<strong>Are you sure you want to proceed?</strong>
							</p>
							<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
								<input type="hidden" id="medication_id" value="" />
								<button type="submit" class="classy red venti btn_remove_medication"><span class="button-span button-span-red">Remove medication</span></button>
								<button type="submit" class="classy green venti btn_cancel_remove_medication"><span class="button-span button-span-green">Cancel</span></button>
								<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
							</div>
						</div>
					</div>
				</div>
<script type="text/javascript">
	$('#btn-add_medication').click(function() {
		$('#medication').val('');
		$('#route').val('');
		$('div.patientMedication #comments').val('');
		$('#add_medication').slideToggle('fast');
		$('#btn-add_medication').attr('disabled',true);
		$('#btn-add_medication').removeClass('green').addClass('disabled');
		$('#btn-add_medication span').removeClass('button-span-green').addClass('button-span-disabled');
	});
	$('button.btn_cancel_medication').click(function() {
		$('#add_medication').slideToggle('fast');
		$('#btn-add_medication').attr('disabled',false);
		$('#btn-add_medication').removeClass('disabled').addClass('green');
		$('#btn-add_medication span').removeClass('button-span-disabled').addClass('button-span-green');
		return false;
	});
	$('#common_medication').change(function() {
		$('#medication').val($(this).children('option:selected').text());
		$(this).val(0);
	});
	$('button.btn_save_medication').click(function() {
		if ($('#medication').length <1) {
			alert("Please enter an medication"); 
			return false;
		}
		if ($('#route').val() == '') {
			alert("Please select a route");
			return false;
		}
		$('img.add_medication_loader').show();
		return true;
	});
	$('a.editMedication').click(function(e) {
		var medication_id = $(this).attr('rel');

		$('#edit_medication_id').val(medication_id);
		$('#medication').val($(this).parent().parent().children('td:first').text());
		var route = $(this).parent().prev('td').prev('td').text();
		$('#route').children('option').map(function() {
			if ($(this).text() == route) {
				$(this).attr('selected','selected');
			}
		});
		$('div.patientMedication #comments').val($(this).parent().prev('td').text());

		$('#add_medication').slideToggle('fast');
		$('#btn-add_medication').attr('disabled',true);
		$('#btn-add_medication').removeClass('green').addClass('disabled');
		$('#btn-add_medication span').removeClass('button-span-green').addClass('button-span-disabled');

		e.preventDefault();
	});
</script>
