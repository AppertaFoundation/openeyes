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
							
							<?php if (BaseController::checkUserLevel(3)) {?>
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
									$this->renderPartial('_fuzzy_date')?>
									
									<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
									<div align="right">
										<img src="<?php echo Yii::app()->createUrl('/img/ajax-loader.gif')?>" class="edit_oph_info_loader" style="display: none;" />
										<button class="classy green mini btn_save_oph_info" type="submit"><span class="button-span button-span-green">Save</span></button>
										<button class="classy red mini btn_cancel_oph_info" type="submit"><span class="button-span button-span-red">Cancel</span></button>
									</div>
									
									<?php $this->endWidget(); ?>
									
								</div>	
							<?php }?>
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
