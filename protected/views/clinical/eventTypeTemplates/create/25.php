<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery-ui.css'] = false;

$form = $this->beginWidget('CActiveForm', array(
	'id'=>'clinical-create',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array('class'=>'sliding'),
	'focus'=>'#procedure_id'
));

echo CHtml::hiddenField('action', 'create');
echo CHtml::hiddenField('event_type_id', $eventTypeId);
echo CHtml::hiddenField('patient_id', $patient->id);
echo CHtml::hiddenField('firm_id', $firm->id);

echo $form->errorSummary($elements);

/**
 * Loop through all the possible element types and display
 */

foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		'/elements/' .
			$elementClassName .
			'/_form/' .
			$element->viewNumber,
		array('model' => $element, 'form' => $form, 'specialties' => $specialties,
			'patient' => $patient, 'newRecord' => true, 'specialty' => $specialty,
			'subsections' => $subsections, 'procedures' => $procedures, 'patient' => $patient)
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
				<span class="group">
					<input id="ScheduleOperation_1" value="0" type="radio" name="ScheduleOperation[schedule]" />
					<label for="ScheduleOperation_1">Within timeframe specified by patient</label>
				</span>
			</div>
	</div>

	<div class="form_button">
			<button type="submit" value="submit" class="wBtn_save-schedule-later ir fancybox" id="scheduleLater">Save and Schedule later</button>
			<button type="submit" value="submit" class="wBtn_save-schedule-now ir fancybox" id="scheduleNow">Save and Schedule now</button>
	</div>

	</form>
</div> <!-- #new_event_details -->

<?php
$this->endWidget();
?>
<script type="text/javascript">
	$('#scheduleNow').unbind('click').click(function() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('clinical/create', array('event_type_id'=>$eventTypeId)); ?>',
			'type': 'POST',
			'data': $('#clinical-create').serialize() + '&scheduleNow=true',
			'success': function(data) {
				try {
					displayErrors(data);
				} catch (e) {
					$('#event_content').html(data);
					return false;
				}
			}
		});
		return false;
	});
	$('#scheduleLater').unbind('click').click(function() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('clinical/create', array('event_type_id'=>$eventTypeId)); ?>',
			'type': 'POST',
			'data': $('#clinical-create').serialize(),
			'success': function(data) {
				if (data.match(/^[0-9]+$/)) {
					window.location.href = '/patient/episodes/<?php echo $patient->id?>/event/'+data;
					return false;
				}
				try {
					displayErrors(data);
				} catch (e) {
					return false;
				}
			}
		});
		return false;
	});

	function displayErrors(data) {
		arr = $.parseJSON(data);
		if (!$.isEmptyObject(arr)) {
			$('#clinical-create_es_ ul').html('');

			$.each(arr, function(index, value) {
				element = index.replace('Element', '');
				element = element.substr(0, element.indexOf('_'));
				list = '<li>' + element + ': ' + value + '</li>';
				$('#clinical-create_es_ ul').append(list);
			});
			$('#clinical-create_es_').show();
			return false;
		} else {
			$('#clinical-create_es_ ul').html('');
			$('#clinical-create_es_').hide();
		}
	}
</script>
