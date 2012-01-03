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
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
	<p>
		<strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?>
	</p>
	<div id="operation">
		<h1>Cancel operation</h1>
<?php
echo CHtml::form(array('booking/cancelOperation'), 'post', array('id' => 'cancelForm'));
echo CHtml::hiddenField('operation_id', $operation->id); ?>
		<div class="errorSummary" style="display:none"></div>
<?php echo CHtml::label('Cancellation reason: ', 'cancellation_reason'); ?>
<?php if (!empty($operation->booking) && (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date)))) {
	$listIndex = 3;
} else {
	$listIndex = 2;
} ?>
<?php echo CHtml::dropDownList('cancellation_reason', '',
	CancellationReason::getReasonsByListNumber($listIndex),
	array('empty'=>'Select a reason')
); ?>
		<br/>
<?php echo CHtml::label('Comments: ', 'cancellation_comment'); ?>
		<div style="height: 0.4em;"></div>
		<textarea name="cancellation_comment" rows=6 cols=40></textarea>
		<div style="height: 0.4em;"></div>
		<div class="buttonwrapper">
			<button type="submit" class="classy red venti"><span class="button-span button-span-red">Cancel operation</span></button>
		</div>
<?php echo CHtml::endForm(); ?>
	</div>
</div>
<script type="text/javascript">
	$('#cancelForm button[type="submit"]').click(function () {
		if ('' == $('#cancellation_reason option:selected').val()) {
			$('div.errorSummary').html('Please select a cancellation reason');
			$('div.errorSummary').show();
			return false;
		}
	});
</script>
