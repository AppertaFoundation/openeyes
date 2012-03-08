<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$disorderId = '';
$value = '';
$eye = '';
$hasDiagnosis = false;

if (empty($model->event_id)) {
	// It's a new event so fetch the most recent element_diagnosis
	$diagnosis = $model->getNewestDiagnosis($patient);

	if (empty($diagnosis->disorder)) {
		// There is no diagnosis for this episode, or no episode, or the diagnosis has no disorder (?)
		$diagnosis = $model;
	} else {
		// There is a diagnosis for this episode
		$value = $diagnosis->disorder->term;
		$eye = $diagnosis->eye;
		$disorderId = $diagnosis->disorder->id;
		$hasDiagnosis = true;
	}
} else {
	if (isset($model->disorder)) {
		$value = $model->disorder->term;
		$eye = $model->eye;
		$disorderId = $model->disorder->id;
		$hasDiagnosis = true;
	}

	$diagnosis = $model;
}
?>
					<!-- Reminder -->
					<?php /*<div class="patientReminder">
						<span class="type"><img src="/img/_elements/icons/event_op_unscheduled.png" alt="op" width="16" height="16" /></span>
						<span class="patient"><strong><?php echo $patient->first_name?></strong> <?php echo $patient->last_name?> (<?php echo $patient->hos_num?>)</span>
					</div>*/?>
					<!-- Details -->
					<?php if (empty($model->event_id)) {?>
						<h3 class="withEventIcon" style="background:transparent url(/img/_elements/icons/event/medium/treatment_laser.png) center left no-repeat;">Book Operation</h3>
					<?php }else{?>
						<h3 class="withEventIcon" style="background:transparent url(/img/_elements/icons/event/medium/treatment_laser.png) center left no-repeat;">Edit Operation</h3>
					<?php }?>
					<h4>Select diagnosis</h4>
 
					<div id="editEyeSelection" class="eventDetail">
						<div class="label">Eye(s):</div>
						<div class="data">
							<input id="ytElementDiagnosis_eye" type="hidden" value="" name="ElementDiagnosis[eye]" />
							<span class="group">
							<input id="ElementDiagnosis_eye_0" value="1"<?php if ($diagnosis->eye == '1') {?> checked="checked"<?php }?> type="radio" name="ElementDiagnosis[eye]" />
							<label for="ElementDiagnosis_eye_0">Right</label>
							</span>
							<span class="group">
							<input id="ElementDiagnosis_eye_2" value="2"<?php if ($diagnosis->eye == '2') {?> checked="checked"<?php }?> type="radio" name="ElementDiagnosis[eye]" />
							<label for="ElementDiagnosis_eye_2">Both</label>
							</span>
							<span class="group">
							<input id="ElementDiagnosis_eye_1" value="0"<?php if (empty($diagnosis->eye)) {?> checked="checked"<?php }?> type="radio" name="ElementDiagnosis[eye]" />
							<label for="ElementDiagnosis_eye_1">Left</label>
							</span>
						</div>
					</div>
 
					<div id="editDiagnosis" class="eventDetail">
						<div class="label">Diagnosis:</div>
						<div class="data">
							<?php echo CHtml::dropDownList('ElementDiagnosis[disorder_id]', '', CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)), array('empty' => 'Select a commonly used diagnosis')); ?>
							<span style="display:block; margin-top:10px; margin-bottom:10px;"><strong>or</strong></span>
							<?php
							$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
								'name'=>'ElementDiagnosis[disorder_id]',
								'id'=>'ElementDiagnosis_disorder_id_0',
								'value'=>'',
								'sourceUrl'=>array('disorder/autocomplete'),
								'options'=>array(
									'minLength'=>'3',
									'select'=>"js:function(event, ui) {
										var value = ui.item.value;
										$('#ElementDiagnosis_disorder_id_0').val('');
										$('#enteredDiagnosisText span').html(value);
										//$('#editDiagnosis').hide();
										//$('#enteredDiagnosis').show();
										$('input[id=savedDiagnosis]').val(value);
										$('#ElementDiagnosis_disorder_id').focus();
										return false;
									}",
								),
								'htmlOptions'=>array(
									'style'=>'width: 300px;'
									//height:20px;width:350px;font:10pt Arial;'
								),
							));
							?>
							<input type="hidden" name="ElementDiagnosis[disorder_id]" id="savedDiagnosis" value="<?php echo $value?>" />
						</div>
						<div id="enteredDiagnosisText">
							<div class="extraDetails">
								<span class="bold" style="margin-right:20px;"><?php echo $value?></span>
							</div>
						</div>
					</div>

					<script type="text/javascript">
						$('input[name="ElementDiagnosis[eye]"]').click(function() {
							var disorder = $('input[name="ElementDiagnosis[disorder_id]"]').val();
							if (disorder.length == 0) {
								$('input[name="ElementDiagnosis[disorder_id]"]').focus();
							}
						});
						$('input[name="ElementDiagnosis[disorder_id]"]').watermark('type the first few characters of a diagnosis');
						$('select[name="ElementDiagnosis[disorder_id]"]').change(function() {
							var value = $(this).children(':selected').text();
							$('#enteredDiagnosisText span').html(value);
							$('#savedDiagnosis').val(value);
							$(this).children(':selected').attr('selected', false);
						});
					</script>
