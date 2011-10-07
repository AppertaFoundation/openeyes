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

Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
<p><strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>
<div id="operation">
	<input type="hidden" id="booking" value="<?php echo $operation->booking->id; ?>" />
	<h1>Re-schedule operation</h1><br />
<?php
if (Yii::app()->user->hasFlash('info')) { ?>
<div class="flash-error">
    <?php echo Yii::app()->user->getFlash('info'); ?>
</div>
<?php
} ?>
	<p><strong>Operation duration:</strong> <?php echo $operation->total_duration; ?> minutes</p>
	<p><strong>Current schedule:</strong></p>
<?php $this->renderPartial('_session', array('operation' => $operation)); ?><br />
<?php
echo CHtml::form(array('booking/update'), 'post', array('id' => 'cancelForm'));
echo CHtml::hiddenField('booking_id', $operation->booking->id); ?>
<p/>
<div class="errorSummary" style="display:none"></div>
<p/>
<?php
echo CHtml::label('Re-schedule reason: ', 'cancellation_reason');
if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
	$listIndex = 3;
} else {
	$listIndex = 2;
}
echo CHtml::dropDownList('cancellation_reason', '',
	CancellationReason::getReasonsByListNumber($listIndex),
	array('empty'=>'Select a reason')
); ?>
<div class="clear"></div>
<button type="submit" value="submit" class="shinybutton highlighted"><span>Cancel booking</span></button><?php
echo CHtml::endForm(); ?>
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