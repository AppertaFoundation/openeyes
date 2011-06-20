<div id="bookings">
<strong>View other operations in this session:</strong>
<span class="<?php echo $minutesStatus; ?>"><?php echo abs($session['time_available']) . " min {$minutesStatus}"; ?></span>
<table>
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
		$operation = $booking->elementOperation;
		$patient = $operation->event->episode->patient;
		$procedures = $operation->procedures;
		$procedureNames = array();
		foreach ($procedures as $procedure) {
			$procedureNames[] = $procedure->term;
		}
		$procedureList = implode(', ', $procedureNames); ?>
		<tr>
			<td><?php echo "{$counter}. {$patient->first_name} {$patient->last_name}"; ?></td>
			<td><?php echo $procedureList; ?></td>
			<td><?php echo "{$operation->total_duration} minutes"; ?></td>
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
<input type="hidden" name="session_id" value="<?php echo $session['id']; ?>" />
<button id="cancel">Cancel operation</button>
<button id="confirm">Confirm slot</button>
</div>