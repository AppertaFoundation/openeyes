<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<section class="element view full patient-info associated-data js-toggle-container">
	<header class="element-header">
		<h3 class="element-title">
			<span class="icon-patient-clinician-hd_flag"></span>
			Other ophthalmic diagnoses
		</h3>
	</header>

	<div class="jelement-data full-width js-toggle-body data-group">
		<table class="plain patient-data">
			<thead>
			<tr>
				<th>Date</th>
				<th>Diagnosis</th>
				<?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
					<th>Actions</th>
				<?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->patient->ophthalmicDiagnoses as $diagnosis) {?>
				<tr>
					<?php
					if (strpos($diagnosis->disorder->term, $diagnosis->eye->adjective. ' ') === 0) {
						$term = $diagnosis->disorder->term;
					} else {
						$term = $diagnosis->eye->adjective . ' ' . $diagnosis->disorder->term;
					}
					?>
					<td><?= $diagnosis->dateText?></td>
					<td><?= $term; ?></td>
					<?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
						<td><a href="#" class="removeDiagnosis" rel="<?= $diagnosis->id?>">Remove</a></td>
					<?php } ?>
				</tr>
			<?php }?>
			</tbody>
		</table>

		<?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
			<div class="box-actions">
				<button id='btn-add_new_ophthalmic_diagnosis' class="secondary small">
					Add Ophthalmic Diagnosis
				</button>
			</div>

			<div id="add_new_ophthalmic_diagnosis" style="display: none;">

				<?php
                $form = $this->beginWidget('FormLayout', array(
                    'id' => 'add-ophthalmic-diagnosis',
                    'enableAjaxValidation' => false,
                    'action' => array('patient/adddiagnosis'),
                    'layoutColumns' => array(
                        'label' => 3,
                        'field' => 9,
                    ),
                    'htmlOptions' => array(
                        'class' => 'form add-data',
                    ),
                ))?>

					<fieldset class="data-group">

						<legend><strong>Add ophthalmic diagnosis</strong></legend>

						<?php $form->widget('application.widgets.DiagnosisSelection', array(
                            'field' => 'ophthalmic_disorder_id',
                            'label' => 'Diagnosis',
                            'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
                            'code' => 130,
                            'default' => false,
                            'layout' => 'patientSummary',
                            'loader' => 'add_ophthalmic_diagnosis_loader',
                        ))?>

						<div class="hide" id="add_ophthalmic_diagnosis_loader">
							<p class="large-offset-<?= $form->layoutColumns['label'];?> cols-<?= $form->layoutColumns['field'];?> column end">
								<img class="loader" src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" />
									searching...
							</p>
						</div>

						<input type="hidden" name="patient_id" value="<?= $this->patient->id?>" />

						<fieldset class="diagnosis_eye data-group">
							<legend class="<?= $form->columns('label');?>">
								Eye:
							</legend>
							<div class="<?= $form->columns('field');?>">
								<?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $i => $eye) {?>
									<label class="inline">
										<input type="radio" name="diagnosis_eye" class="diagnosis_eye" value="<?= $eye->id?>"<?php if ($i == 0) {?> checked="checked"<?php }?> /> <?= $eye->name?>
									</label>
								<?php }?>
							</div>
						</fieldset>

						<?php $this->renderPartial('_fuzzy_date', array('form' => $form, 'label' => 'Date diagnosed'))?>

						<div class="ophthalmic_diagnoses_form_errors alert-box alert hide"></div>

						<div class="buttons">
							<img src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_ophthalmic_diagnosis_loader" style="display: none;" />
							<button type="submit" class="secondary small btn_save_ophthalmic_diagnosis">
								Save
							</button>
							<button class="warning small btn_cancel_ophthalmic_diagnosis">
								Cancel
							</button>
						</div>

					</fieldset>
				<?php $this->endWidget()?>
			</div>
		<?php } ?>
	</div>
</section>
<?php if ($this->checkAccess('OprnEditOtherOphDiagnosis')) { ?>
	<!-- Confirm deletion dialog -->
	<div id="confirm_remove_diagnosis_dialog" title="Confirm remove diagnosis" style="display: none;">
		<div id="delete_diagnosis">
			<div class="alert-box alert with-icon">
				<strong>WARNING: This will remove the diagnosis from the patient record.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttons">
				<input type="hidden" id="diagnosis_id" value="" />
				<button type="submit" class="warning small btn_remove_diagnosis">Remove diagnosis</button>
				<button type="submit" class="secondary small btn_cancel_remove_diagnosis">Cancel</button>
				<img class="loader hide" src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." />
			</div>
		</div>
	</div>

	<script type="text/javascript">

		$('#btn-add_new_ophthalmic_diagnosis').click(function() {
			$('#add_new_ophthalmic_diagnosis').slideToggle('fast');
			$('#btn-add_new_ophthalmic_diagnosis').attr('disabled',true);
			$('#btn-add_new_ophthalmic_diagnosis').addClass('disabled');
		});
		$('button.btn_cancel_ophthalmic_diagnosis').click(function(e) {
			$('#add_new_ophthalmic_diagnosis').slideToggle('fast');
			$('#btn-add_new_ophthalmic_diagnosis').attr('disabled',false);
			$('#btn-add_new_ophthalmic_diagnosis').removeClass('disabled');
			OpenEyes.Form.reset($(e.target).closest('form'));
			return false;
		});
		$('button.btn_save_ophthalmic_diagnosis').click(function() {
			$.ajax({
				'type': 'POST',
				'dataType': 'json',
				'url': baseUrl+'/patient/validateadddiagnosis',
				'data': $('#add-ophthalmic-diagnosis').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(data) {
					$('div.ophthalmic_diagnoses_form_errors').hide();
					if (data.length == 0) {
						$('img.add_ophthalmic_diagnosis_loader').show();
						$('#add-ophthalmic-diagnosis').submit();
						return true;
					} else {
						$('div.ophthalmic_diagnoses_form_errors').html('');
						for (var i in data) {
							$('div.ophthalmic_diagnoses_form_errors').show().append('<div>'+data[i]+'</div>');
						}
					}
				}
			});
			return false;
		});

	</script>
<?php } ?>
