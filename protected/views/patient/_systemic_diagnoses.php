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
<section class="box patient-info associated-data js-toggle-container">
	<header class="box-header">
		<h3 class="box-title">
			<span class="icon-patient-clinician-hd_flag"></span>
			Systemic Diagnoses
		</h3>
		<a href="#" class="toggle-trigger toggle-hide js-toggle">
			<span class="icon-showhide">
				Show/hide this section
			</span>
		</a>
	</header>

	<div class="js-toggle-body">

		<table class="plain patient-data">
			<thead>
			<tr>
				<th>Date</th>
				<th>Diagnosis</th>
				<?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) { ?><th>Actions</th><?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->patient->systemicDiagnoses as $diagnosis) {?>
				<tr>
					<td><?php echo $diagnosis->dateText?></td>
					<td><?php echo $diagnosis->eye ? $diagnosis->eye->adjective : ''?> <?php echo $diagnosis->disorder->term?></td>
					<?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) { ?>
						<td><a href="#" class="removeDiagnosis" rel="<?php echo $diagnosis->id?>">Remove</a></td>
					<?php } ?>
				</tr>
			<?php }?>
			</tbody>
		</table>

		<?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) { ?>
			<div class="box-actions">
				<button id="btn-add_new_systemic_diagnosis" class="secondary small">
					Add Systemic Diagnosis
				</button>
			</div>

			<div id="add_new_systemic_diagnosis" style="display: none;">

				<?php
				$form = $this->beginWidget('FormLayout', array(
						'id'=>'add-systemic-diagnosis',
						'enableAjaxValidation'=>false,
						'action'=>array('patient/adddiagnosis'),
						'layoutColumns'=>array(
							'label' => 3,
							'field' => 9
						),
						'htmlOptions'=>array(
							'class' => 'form add-data'
						)
					))?>

				<fieldset class="field-row">

					<legend><strong>Add Systemic diagnosis</strong></legend>

					<?php $form->widget('application.widgets.DiagnosisSelection',array(
							'field' => 'systemic_disorder_id',
							'label' => 'Diagnosis',
							'options' => CommonSystemicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
							'restrict' => 'systemic',
							'default' => false,
							'layout' => 'patientSummary',
							'loader' => 'add_systemic_diagnosis_loader',
						))?>

					<div class="row field-row hide" id="add_systemic_diagnosis_loader">
						<p class="large-offset-<?php echo $form->layoutColumns['label'];?> large-<?php echo $form->layoutColumns['field'];?> column end">
							<img class="loader" src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" />
								searching...
						</p>
					</div>

					<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

					<fieldset class="diagnosis_eye row field-row">
						<legend class="<?php echo $form->columns('label');?>">
							Side:
						</legend>
						<div class="<?php echo $form->columns('field');?>">
							<label class="inline">
								<input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="" checked="checked" /> None
							</label>
							<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $eye) {?>
								<label class="inline">
									<input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="<?php echo $eye->id?>" /> <?php echo $eye->name?>
								</label>
							<?php }?>
						</div>
					</fieldset>

					<?php $this->renderPartial('_fuzzy_date', array('form'=>$form,'label'=>'Date diagnosed'))?>

					<div class="systemic_diagnoses_form_errors alert-box alert hide"></div>

					<div class="buttons">
						<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="add_systemic_diagnosis_loader hide" />
						<button type="submit" class="secondary small btn_save_systemic_diagnosis">
							Save
						</button>
						<button class="warning small btn_cancel_systemic_diagnosis">
							Cancel
						</button>
					</div>

				</fieldset>
				<?php $this->endWidget()?>

			</div>
		<?php } ?>
	</div>

</section>
<?php if ($this->checkAccess('OprnEditSystemicDiagnosis')) { ?>
	<script type="text/javascript">
		$('#btn-add_new_systemic_diagnosis').click(function() {
			$('#add_new_systemic_diagnosis').slideToggle('fast');
			$('#btn-add_new_systemic_diagnosis').attr('disabled',true);
			$('#btn-add_new_systemic_diagnosis').addClass('disabled');
		});
		$('button.btn_cancel_systemic_diagnosis').click(function() {
			$('#add_new_systemic_diagnosis').slideToggle('fast');
			$('#btn-add_new_systemic_diagnosis').attr('disabled',false);
			$('#btn-add_new_systemic_diagnosis').removeClass('disabled');
			return false;
		});
		$('button.btn_save_systemic_diagnosis').click(function() {
			$.ajax({
				'type': 'POST',
				'dataType': 'json',
				'url': baseUrl+'/patient/validateadddiagnosis',
				'data': $('#add-systemic-diagnosis').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(data) {
					$('div.systemic_diagnoses_form_errors').html('').hide();
					if (data.length == 0) {
						$('img.add_systemic_diagnosis_loader').show();
						$('#add-systemic-diagnosis').submit();
						return true;
					} else {
						for (var i in data) {
							$('div.systemic_diagnoses_form_errors').show().append('<div>'+data[i]+'</div>');
						}
					}
				}
			});
			return false;
		});
	</script>
<?php } ?>
