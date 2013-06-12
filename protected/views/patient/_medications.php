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
										<th>Option</th>
										<th>Frequency</th>
										<th>Start date</th>
										<th>Edit</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->patient->medications as $medication) {?>
										<tr>
											<td><?php echo $medication->drug->name?></td>
											<td><?php echo $medication->route->name?></td>
											<td><?php echo $medication->option ? $medication->option->name : '-'?></td>
											<td><?php echo $medication->frequency->name?></td>
											<td><?php echo $medication->NHSDate('start_date')?></td>
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
										<?php echo CHtml::dropDownList('drug_id','',Drug::model()->listBySubspecialty($firm->serviceSubspecialtyAssignment->subspecialty_id),array('empty'=>'- Select -'))?>
									</div>
								</div>
								<div class="patientMedication">
									<div class="label"></div>
									<div class="data">
										<?php
										$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
												'name' => 'drug_id',
												'id' => 'autocomplete_drug_id',
												'source' => "js:function(request, response) {
													$.getJSON('".$this->createUrl('DrugList')."', {
														term : request.term,
													}, response);
												}",
												'options' => array(
													'select' => "js:function(event, ui) {
														$('#selectedMedicationName').text(ui.item.value);
														$('#selectedMedicationID').val(ui.item.id);
														$(this).val('');
														return false;
													}",
												),
												'htmlOptions' => array(
													'placeholder' => 'or search formulary',
												),
										))?>
									</div>
								</div>

								<div class="patientMedication">
									<div class="label"></div>
									<div class="data">
										<span id="selectedMedicationName" style="font-weight: bold;"></span>
										<input type="hidden" name="selectedMedicationID" id="selectedMedicationID" value="" />
									</div>
								</div>

								<div class="patientMedication">
									<div class="label">
										Route:
									</div>
									<div class="data">
										<?php echo CHtml::dropDownList('route_id','',CHtml::listData(DrugRoute::model()->findAll(),'id','name'),array('empty'=>'- Select -'))?>
									</div>
								</div>

								<div class="patientMedication routeOption" style="display: none;">
									<div class="label">
										Option:
									</div>
									<div class="data">
									</div>
								</div>

								<div class="patientMedication">
									<div class="label">
										Frequency:
									</div>
									<div class="data">
										<?php echo CHtml::dropDownList('frequency_id','',CHtml::listData(DrugFrequency::model()->findAll(array('order'=>'display_order')),'id','name'),array('empty'=>'- Select -'))?>
									</div>
								</div>

								<div class="patientMedication">
									<div class="label">
										Date from:
									</div>
									<div class="data">
										<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
											'name'=>'start_date',
											'id'=>'start_date',
											'options'=>array(
												'showAnim'=>'fold',
												'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
											),
											'value' => date('j M Y'),
											'htmlOptions'=>array('style'=>'width: 90px;')
										))?>
									</div>
								</div>

								<div class="medication_form_errors"></div>

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
		$('div.patientMedication #route_id').val('');
		$('div.patientMedication #drug_id').val('');
		$('div.patientMedication #frequency_id').val('');
		$('div.patientMedication #start_date').val('');
		$('div.routeOption .date').html('');
		$('div.routeOption').hide();

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
	$('#drug_id').change(function() {
		if ($(this).val() != '') {
			selectMedication($(this).val(),$(this).children('option:selected').text());
			$('#drug_id').val('');
		}
	});

	function selectMedication(id, name) {
		$('#selectedMedicationName').text(name);
		$('#selectedMedicationID').val(id);

		$.ajax({
			'type': 'GET',
			'dataType': 'json',
			'url': baseUrl+'/patient/DrugDefaults?drug_id='+id,
			'success': function(data) {
				if (data['route_id']) {
					$('#route_id').val(data['route_id']);
					$('#route_id').change();
				}
				if (data['frequency_id']) {
					$('#frequency_id').val(data['frequency_id']);
				}
			}
		});
	}

	handleButton($('button.btn_save_medication'),function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'data': $('#add-medication').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'dataType': 'json',
			'url': baseUrl+'/patient/validateAddMedication',
			'success': function(data) {
				$('div.medication_form_errors').html('');

				if (data.length == 0) {
					$('#add-medication').submit();
					return;
				}

				enableButtons();

				for (var i in data) {
					$('div.medication_form_errors').append('<div class="errorMessage">'+data[i]+'</div>');
				}
			}
		});
	});
	$('a.editMedication').click(function(e) {
		var medication_id = $(this).attr('rel');

		$('#edit_medication_id').val(medication_id);

		$.ajax({
			'type': 'GET',
			'dataType': 'json',
			'url': baseUrl+'/patient/getMedication?medication_id='+medication_id,
			'success': function(data) {
				$('div.patientMedication #route_id').val(data['route_id']);
				$('#selectedMedicationID').val(data['drug_id']);
				$('#selectedMedicationName').text(data['drug_name']);
				$('div.patientMedication #frequency_id').val(data['frequency_id']);
				$('div.patientMedication #start_date').val(data['start_date']);
				$('div.routeOption .data').html(data['route_options']);
				$('div.routeOption').show();
				$('div.patientMedication #option_id').val(data['option_id']);
			}
		});

		$('#add_medication').slideToggle('fast');
		$('#btn-add_medication').attr('disabled',true);
		$('#btn-add_medication').removeClass('green').addClass('disabled');
		$('#btn-add_medication span').removeClass('button-span-green').addClass('button-span-disabled');

		e.preventDefault();
	});
	$('#route_id').change(function() {
		var route_id = $(this).val();

		if (route_id == '') {
			$('div.routeOption').hide();
			$('div.routeOption .data').html('');
		} else {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/patient/getDrugRouteOptions?route_id='+route_id,
				'success': function(html) {
					$('div.routeOption .data').html(html);
					if (html.length >0) {
						$('div.routeOption').show();
					} else {
						$('div.routeOption').hide();
					}
				}
			});
		}
	});
</script>
