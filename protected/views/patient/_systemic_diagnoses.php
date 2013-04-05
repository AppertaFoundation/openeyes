<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
					<div class="whiteBox forClinicians">
						<div class="patient_actions">
							<span class="aBtn"><a class="sprite showhide" href="#"><span class="hide"></span></a></span>
						</div>
						<div class="icon_patientIssue"></div>
						<h4>Systemic diagnoses</h4>
						<div class="data_row">
							<table class="subtleWhite">
								<thead>
									<tr>
										<th width="85px">Date</th>
										<th>Diagnosis</th>
										<?php if(BaseController::checkUserLevel(3)) { ?><th>Edit</th><?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->patient->systemicDiagnoses as $diagnosis) {?>
										<tr>
											<td><?php echo $diagnosis->dateText?></td>
											<td><?php echo $diagnosis->eye ? $diagnosis->eye->adjective : ''?> <?php echo $diagnosis->disorder->term?></td>
											<?php if(BaseController::checkUserLevel(3)) { ?>
											<td><a href="#" class="small removeDiagnosis" rel="<?php echo $diagnosis->id?>"><strong>Remove</strong></a></td>
											<?php } ?>
										</tr>
									<?php }?>
								</tbody>
							</table>
							
							<?php if(BaseController::checkUserLevel(3)) { ?>
							<div align="center" style="margin-top:10px;">
								<form><button id="btn-add_new_systemic_diagnosis" class="classy green mini" type="button"><span class="button-span button-span-green">Add Systemic Diagnosis</span></button></form>
							</div>
							<div id="add_new_systemic_diagnosis" style="display: none;">
								<h5>Add Systemic diagnosis</h5>	
								<?php
								$form = $this->beginWidget('CActiveForm', array(
										'id'=>'add-systemic-diagnosis',
										'enableAjaxValidation'=>false,
										'htmlOptions' => array('class'=>'sliding'),
										'action'=>array('patient/adddiagnosis'),
								))?>
	
								<?php $form->widget('application.widgets.DiagnosisSelection',array(
										'field' => 'systemic_disorder_id',
										'options' => CommonSystemicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
										//'restrict' => 'specialty',
										'default' => false,
										'layout' => 'patientSummary',
										'loader' => 'add_systemic_diagnosis_loader',
								))?>
	
								<div id="add_systemic_diagnosis_loader" style="display: none;">
									<img align="left" class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" />
									<div>
										searching...
									</div>
								</div>
	
								<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
	
								<div class="diagnosis_eye">
									<span class="diagnosis_eye_label">
											Side:
									</span>
									<input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="" checked="checked" /> None
									<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $eye) {?>
										<input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="<?php echo $eye->id?>" /> <?php echo $eye->name?>
									<?php }?>
								</div>
	
								<?php $this->renderPartial('_fuzzy_date')?>
	
								<div align="right">
									<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_systemic_diagnosis_loader" style="display: none;" />
									<button class="classy green mini btn_save_systemic_diagnosis" type="submit"><span class="button-span button-span-green">Save</span></button>
									<button class="classy red mini btn_cancel_systemic_diagnosis" type="submit"><span class="button-span button-span-red">Cancel</span></button>
								</div>
	
								<?php $this->endWidget()?>
							</div>	
							<?php } ?>
						</div>
						
					</div>
<?php if(BaseController::checkUserLevel(3)) { ?>
<script type="text/javascript">
	$('#btn-add_new_systemic_diagnosis').click(function() {
		$('#add_new_systemic_diagnosis').slideToggle('fast');
		$('#btn-add_new_systemic_diagnosis').attr('disabled',true);
		$('#btn-add_new_systemic_diagnosis').removeClass('green').addClass('disabled');
		$('#btn-add_new_systemic_diagnosis span').removeClass('button-span-green').addClass('button-span-disabled');
	});
	$('button.btn_cancel_systemic_diagnosis').click(function() {
		$('#add_new_systemic_diagnosis').slideToggle('fast');
		$('#btn-add_new_systemic_diagnosis').attr('disabled',false);
		$('#btn-add_new_systemic_diagnosis').removeClass('disabled').addClass('green');
		$('#btn-add_new_systemic_diagnosis span').removeClass('button-span-disabled').addClass('button-span-green');
		return false;
	});
	$('button.btn_save_systemic_diagnosis').click(function() {
		if (!$('#DiagnosisSelection_systemic_disorder_id_savedDiagnosis').val()) {
			alert("Please select a diagnosis.");
			return false;
		}
		$('img.add_systemic_diagnosis_loader').show();
		return true;
	});
</script>
<?php } ?>
