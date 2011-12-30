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

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
if (!$reschedule) {
	echo CHtml::form(array('booking/create'), 'post', array('id' => 'bookingForm'));
} else {
	echo CHtml::form(array('booking/update'), 'post', array('id' => 'bookingForm'));
}

?>
	<h4>Other operations in this session: <?php echo abs($session['time_available']) . " min {$minutesStatus}"; ?></h4>

	<div class="theatre-sessions">
	<table id="appointment_list">
		<thead>
			<tr>
				<th>Operation list overview</th>
				<th>Date: <?php echo date('d M Y', strtotime($session['date'])); ?></th>
				<th>Session time: <?php echo substr($session['start_time'], 0, 5) . ' - '
				. substr($session['end_time'], 0, 5); ?></th>
				<th>Admission time</th>
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
				<td><?php echo $booking->admission_time ?></td>

			</tr>
<?php
		$counter++;
	} ?>
	</tbody>
		<tfoot>
			<tr>
				<th colspan="4"><?php echo ($counter - 1) . ' booking';
	if (($counter - 1) != 1) {
		echo 's';
	}
	echo ' currently scheduled'; ?></th>
			</tr>
		</tfoot>
</table>
</div>

<div class="eventDetail clearfix">
	<div class="label"><strong>Admission Time:</strong></div>
	<div class="data"> 
		<input type="text" id="Booking_admission_time" name="Booking[admission_time]" value="<?php echo ($session['start_time'] == '13:30:00') ? '12:00' : date('H:i', strtotime('-1 hour', strtotime($session['start_time']))) ?>" size="6">
	</div>
</div>

<div class="eventDetail clearfix" style="position:relative;">
	<div class="label"><strong>Session Comments:</strong>
		<img src="/img/_elements/icons/alerts/comment.png" alt="comment" width="17" height="17" style="position:absolute; bottom:10px; left:10px;" />
	</div>
	<div class="data">
		<div class="sessionComments" style="width:400px; display:inline-block; margin-bottom:0; ">
			<textarea id="Session_comments" name="Session[comments]" rows="2" style="width:395px;"><?php echo htmlspecialchars($session['comments']) ?></textarea>
		</div>
	</div>	
</div>

<?php
if ($reschedule) {
	echo CHtml::hiddenField('booking_id', $operation->booking->id);
}
echo CHtml::hiddenField('Booking[element_operation_id]', $operation->id);
echo CHtml::hiddenField('Booking[session_id]', $session['id']);
?>

<?php if ($reschedule) { ?>
<h3>Reason for Reschedule</h3>
<div class="eventDetail clearfix" style="position:relative;">
	<div class="errorSummary" style="display:none"></div>
	<div class="label"><strong><?php echo CHtml::label('Reschedule Reason: ', 'cancellation_reason'); ?></strong></div>
	<?php if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
		$listIndex = 3;
	} else {
		$listIndex = 2;
	} ?>
	<div class="data">
	<?php echo CHtml::dropDownList('cancellation_reason', '',
		CancellationReason::getReasonsByListNumber($listIndex),
		array('empty' => 'Select a reason')
	); ?>
	</div>
</div>
<div class="eventDetail clearfix" style="position:relative;">
	<div class="label"><strong><?php echo CHtml::label('Reschedule Comments: ', 'cancellation_comment'); ?></strong></div>
	<div class="data">
		<textarea name="cancellation_comment" rows=3 cols=50></textarea>
	</div>
</div>
<?php } ?>

<div style="margin: 0.5em 0;">
	<span id="dateSelected">Date/Time currently selected: <span class="highlighted"><?php echo date('d M Y', strtotime($session['date'])); ?>, <?php echo substr($session['start_time'], 0, 5) . ' - ' . substr($session['end_time'], 0, 5); ?></span></span>
</div>
<div style="margin-top:10px;">
<button type="submit" class="classy green venti" id="confirm_slot"><span class="button-span button-span-green">Confirm slot</span></button>
<button type="submit" class="classy red venti" id="cancel_operation"><span class="button-span button-span-red">Cancel operation</span></button>
</div>

<?php
echo CHtml::endForm();
?>

<script type="text/javascript">
<?php if ($reschedule) { ?>
	$('#bookingForm button#confirm_slot').click(function () {
		if ($('#cancellation_reason option:selected').val() == '') {
			$('div.errorSummary').html('Please select a reason for reschedule');
			$('div.errorSummary').show();
			return false;
		}
	});
<?php } ?>
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
