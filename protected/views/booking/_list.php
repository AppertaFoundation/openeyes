<div id="bookings">
<div class="cleartall"></div>
<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
if (!$reschedule) {
	echo CHtml::form(array('booking/create'));
} else {
	echo CHtml::form(array('booking/update'));
} ?>
<div class="label">Ward:</div>
<div class="data"><?php echo CHtml::dropDownList('Booking[ward_id]', '', 
	$operation->getWardOptions($session['site_id'])); ?></div>
<div class="cleartall"></div>
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
	echo CHtml::label('Cancellation Reason: ', 'cancellation_reason');
	if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
		$listIndex = 3;
	} else {
		$listIndex = 2;
	}
	echo CHtml::dropDownList('cancellation_reason', '', 
		CancellationReason::getReasonsByListNumber($listIndex)
	);
}
?>
<div class="cleartall"></div>
<div class="buttonwrapper">
<button type="submit" value="submit" class="shinybutton highlighted"><span>Confirm slot</span></button><?php
echo CHtml::endForm(); ?>
<button type="submit" value="submit" class="shinybutton" id="cancel_operation"><span>Cancel operation</span></button>
</div>
</div>
<script type="text/javascript">
	$('button#cancel_operation').live('click', function() {
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