<div id="bookings">
<strong>View other operations in this session:</strong>
<span class="<?php echo $minutesStatus; ?>"><?php echo abs($session['time_available']) . " min {$minutesStatus}"; ?></span>
<p/>
<table id="appointment_list">
	<thead>
		<th>Operation list overview</th>
		<th>Date: <?php echo date('F j, Y', strtotime($session['date'])); ?></th>
		<th>Session time: <?php echo substr($session['start_time'], 0, 5) . ' - ' 
			. substr($session['end_time'], 0, 5); ?></th>
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
	<tfoot>
		<td colspan="3"><?php echo ($counter - 1) . ' booking';
	if (($counter - 1) != 1) {
		echo 's';
	}
	echo ' currently scheduled'; ?></td>
	</tfoot>
</table>
<?php 
if (!$reschedule) {
	echo CHtml::form(array('booking/create'));
	echo CHtml::hiddenField('Booking[element_operation_id]', $operation->id);
	echo CHtml::hiddenField('Booking[session_id]', $session['id']);
} else {
	echo CHtml::form(array('booking/update'));
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
<button type="submit" value="submit" class="shinybutton highlighted"><span>Confirm slot</span></button><?php
echo CHtml::endForm(); ?>
</div>