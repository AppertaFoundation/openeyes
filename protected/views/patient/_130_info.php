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
			CVI Status
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

		<?php if ($this->checkAccess('OprnEditOphInfo')) {?>

			<div class="box-actions">
				<button id="btn-edit_oph_info" class="secondary small">
					Edit
				</button>
			</div>

			<div id="edit_oph_info" style="display: none;">

				<fieldset class="field-row">
					<legend><strong>Edit CVI Status</strong></legend>
					<?php
				$form = $this->beginWidget('FormLayout', array(
						'id'=>'edit-oph_info',
						'enableAjaxValidation'=>true,
						'clientOptions'=>array(
							'validateOnSubmit' => true,
							'validateOnChange' => false,
							'afterValidate' => "js:function(form, data, hasError) {
								if (hasError) {
									// mask the ajax loader image again
									$('img.edit_oph_info_loader').hide();
								} else {
									return true;
								}}"
						),
						'htmlOptions' => array('class'=>'form add-data'),
						'action'=>array('patient/editophinfo'),
						'layoutColumns'=>array(
							'label' => 3,
							'field' => 9
						),
					))?>

					<div class="field-row row">
						<div class="<?php echo $form->columns('label');?>">
							<label for="PatientOphInfo_cvi_status_id">Status:</label>
						</div>
						<div class="<?php echo $form->columns('field');?>">
							<?php echo CHtml::activeDropDownList($info, 'cvi_status_id', CHtml::listData(PatientOphInfoCviStatus::model()->findAll(array('order'=>'display_order')),'id','name')) ?>
							<?php echo $form->error($info, 'cvi_status_date'); ?>
						</div>
					</div>

					<?php
					$this->renderPartial('_fuzzy_date', array('form'=>$form))?>

					<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
					<div class="buttons">
						<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="edit_oph_info_loader" style="display: none;" />
						<button type="submit" class="secondary small btn_save_previous_operation btn_save_oph_info">
							Save
						</button>
						<button class="warning small btn_cancel_previous_operation btn_cancel_oph_info">
							Cancel
						</button>
					</div>

					<?php $this->endWidget(); ?>
				</fieldset>
			</div>
		<?php }?>
	</div>

</section>

<script type="text/javascript">
	$('#btn-edit_oph_info').click(function() {
		$('#edit_oph_info').slideToggle('fast');
		$('#btn-edit_oph_info').attr('disabled',true);
		$('#btn-edit_oph_info').addClass('disabled');
	});
	$('button.btn_cancel_oph_info').click(function() {
		$('#edit_oph_info').slideToggle('fast');
		$('#btn-edit_oph_info').attr('disabled',false);
		$('#btn-edit_oph_info').removeClass('disabled');
		return false;
	});
	$('button.btn_save_oph_info').click(function() {
		$('.errorMessage').slideUp();
		$('img.edit_oph_info_loader').show();
		return true;
	});
</script>
