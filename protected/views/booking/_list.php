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

?><div id="bookings">
<div class="cleartall"></div>
<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
if (!$reschedule) {
	echo CHtml::form(array('booking/create'), 'post', array('id' => 'bookingForm'));
} else {
	echo CHtml::form(array('booking/update'), 'post', array('id' => 'bookingForm'));
} ?>
<strong>View other operations in this session:</strong>
<span class="<?php echo $minutesStatus; ?>"><?php echo abs($session['time_available']) . " min {$minutesStatus}"; ?></span>
<p/>
<table id="appointment_list">
	<thead>
		<tr class="head">
			<th>Operation list overview</th>
			<th>Date: <?php echo date('F j, Y', strtotime($session['date'])); ?></th>
			<th>Session time: <?php echo substr($session['start_time'], 0, 5) . ' - '
				. substr($session['end_time'], 0, 5); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
	$counter = 1;
	foreach ($bookings as $booking) {
		$thisOperation = $booking->elementOperation;
		$patient = $thisOperation->event->episode->patient;
		$procedures = $thisOperation->procedures;
		$procedureNames = array();
		foreach ($procedures as $procedure) {
			$procedureNames[] = $procedure->term;
		}
		$procedureList = implode(', ', $procedureNames);
		if (empty($procedureList)) {
			$procedureList = 'No procedures';
		} ?>
		<tr>
			<td><?php echo "{$counter}. {$patient->first_name} {$patient->last_name}"; ?></td>
			<td><?php echo $procedureList; ?></td>
			<td><?php echo "{$thisOperation->total_duration} minutes"; ?></td>
		</tr>
<?php
		$counter++;
	} ?>
	</tbody>
	<tfoot class="rounded_corners">
		<tr>
			<td colspan="3"><?php echo ($counter - 1) . ' booking';
	if (($counter - 1) != 1) {
		echo 's';
	}
	echo ' currently scheduled'; ?></td>
		</tr>
	</tfoot>
</table>
<?php
if (!$reschedule) {
	echo CHtml::hiddenField('Booking[element_operation_id]', $operation->id);
	echo CHtml::hiddenField('Booking[session_id]', $session['id']);
} else {
	echo CHtml::hiddenField('booking_id', $operation->booking->id);
	echo CHtml::hiddenField('Booking[element_operation_id]', $operation->id);
	echo CHtml::hiddenField('Booking[session_id]', $session['id']);
}
if (!empty($reschedule)) {
	echo '<div class="errorSummary" style="display:none"></div><p/>';
	echo CHtml::label('Re-schedule reason: ', 'cancellation_reason');
	if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
		$listIndex = 3;
	} else {
		$listIndex = 2;
	}
	echo CHtml::dropDownList('cancellation_reason', '',
		CancellationReason::getReasonsByListNumber($listIndex),
		array('empty' => 'Select a reason')
	);
}
?>
<div class="cleartall"></div>
<div class="greyGradient">
<div style="display: inline;">
<span id="siteSelected">Location currently selected: <span class="highlighted"><?php echo $site->name; ?></span></span><br />
<span id="dateSelected">Date/Time currently selected: <span class="highlighted"><?php echo date('d F Y', strtotime($session['date'])); ?>, <?php echo substr($session['start_time'], 0, 5) . ' - ' . substr($session['end_time'], 0, 5); ?></span></span>
<button type="submit" value="submit" class="shinybutton highlighted" style="margin-top:-15px;"><span>Confirm slot</span></button><?php
echo CHtml::endForm(); ?>
<button type="submit" value="submit" class="shinybutton" id="cancel_operation" style="margin-top:-15px;"><span>Cancel operation</span></button>
</div>
</div>
<script type="text/javascript">
<?php
	if (!empty($reschedule)) { ?>
	$('#bookingForm button[type="submit"]').click(function () {
		if ('' == $('#cancellation_reason option:selected').val()) {
			$('div.errorSummary').html('Please select a cancellation reason');
			$('div.errorSummary').show();
			return false;
		}
	});
<?php
	}
	?>
	$('button#cancel_operation').die('click').live('click', function() {
		$.ajax({
			url: '<?php echo Yii::app()->createUrl('booking/cancelOperation'); ?>',
			type: 'GET',
			data: {'operation': <?php echo $operation->id; ?>},
			success: function(data) {
				$('div#schedule').parent().html(data);
			}
		});
		return false;
	});
</script>
