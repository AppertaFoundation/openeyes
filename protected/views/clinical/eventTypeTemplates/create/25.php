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

$this->header();

$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
	'id'=>'clinical-create',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('class'=>'sliding'),
	'focus'=>'#procedure_id'
));

echo CHtml::hiddenField('action', 'create');
echo CHtml::hiddenField('event_type_id', $eventTypeId);
echo CHtml::hiddenField('patient_id', $_GET['patient_id']);
echo CHtml::hiddenField('firm_id', $_GET['firm_id']);

if (isset($errors) && !empty($errors)) {?>
	<div id="clinical-create_es_" class="alertBox">
		<p>Please fix the following input errors:</p>
		<?php foreach ($errors as $field => $errs) {?>
			<ul>
				<?php foreach ($errs as $err) {?>
					<li>
						<?php echo $field.': '.$err?>
					</li>
				<?php }?>
			</ul>
		<?php }?>
	</div>
<?php }?>
<span style="display: none;" id="header_text">Operation: <?php echo $this->patient->getDisplayName()?></span>
<script type="text/javascript">
	// When eye selected in diagnosis, reflect the value in operation details
	$(document).ready(function(){
		$('input[name="ElementDiagnosis[eye_id]"]').change(function(){
			if($(this).siblings('label').text() != 'Both'){
				// Set operation eye selection to current diagnosis eye selection
				id = $('input[name="ElementDiagnosis[eye_id]"]:checked').val();
				$('#ElementOperation_eye_id input[value="'+id+'"]').attr('checked', true);
			}else{
				// Unset operation eye selection if user selected 'Both'
				$('input[name="ElementOperation[eye_id]"]:checked').attr('checked', false);
			}
		});
	});
</script>
<?php

/**
 * Loop through all the possible element types and display
 */

foreach ($elements as $element) {
	echo $this->renderPartial(
		'/elements/' .	get_class($element) .  '/form',
		array('model' => $element, 'form' => $form, 'specialties' => $specialties,
			'newRecord' => true)
	);
}

// Display referral select box if required
if (isset($referrals) && is_array($referrals)) {
	// There is at least on referral, so include it/them
	if (count($referrals) > 1) {
		// Display a list of referrals for the user to choose from
?>
<div class="box_grey_big_gradient_top"></div>
<div class="box_grey_big_gradient_bottom">
	<span class="referral_red">There is more than one open referral that could apply to this event.</span><p />
	<label for="referral_id">Select the referral that applies to this event:</label>
<?php echo CHtml::dropDownList('referral_id', '', CHtml::listData($referrals, 'id', 'id')); ?>
</div>
<?php }}?>
	<h4>Schedule Operation</h4>

	<div id="schedule options" class="eventDetail">
			<div class="label">Schedule options:</div>
			<div class="data">
				<input id="ScheduleOperation" type="hidden" value="" name="ScheduleOperationn[schedule]" />
				<span class="group">
					<input id="ScheduleOperation_0" value="1" checked="checked" type="radio" name="ScheduleOperation[schedule]" />
					<label for="ScheduleOperation_0">As soon as possible</label>
				</span>
				<!--span class="group">
					<input id="ScheduleOperation_1" value="0" type="radio" name="ScheduleOperation[schedule]" />
					<label for="ScheduleOperation_1">Within timeframe specified by patient</label>
				</span-->
			</div>
	</div>

<?php if (isset($errors) && !empty($errors)) {?>
	<div id="clinical-create_es_" class="alertBox">
		<p>Please fix the following input errors:</p>
		<?php foreach ($errors as $field => $errs) {?>
			<ul>
				<?php foreach ($errs as $err) {?>
					<li>
						<?php echo $field.': '.$err?>
					</li>
				<?php }?>
			</ul>
		<?php }?>
	</div>
<?php }?>

	<div class="form_button">
		<img class="loader" style="display: none;" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
		<button type="submit" class="classy green venti auto" id="scheduleLater" name="scheduleLater"><span class="button-span button-span-green">Save and Schedule later</span></button>
		<button type="submit" class="classy green venti auto" id="scheduleNow" name="scheduleNow"><span class="button-span button-span-green">Save and Schedule now</span></button>
		<button type="submit" class="classy red venti auto" id="cancelOperation" name="cancelOperation"><span class="button-span button-span-red">Cancel Operation</span></button>
	</div>

<?php $this->endWidget(); ?>
<?php $this->footer()?>
