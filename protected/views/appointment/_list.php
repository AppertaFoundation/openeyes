<div id="appointments">
<strong>View other operations in this session:</strong>
<span><?php echo $session['time_available']; ?> min available</span>
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
	foreach ($appointments as $appointment) { ?>
		<td><?php echo $counter . '. Patient name'; ?></td>
		<td><?php echo 'Procedure list'; ?></td>
		<td><?php echo 'Duration'; ?></td>
<?php
		$counter++;
	} ?>
	</tbody>
	<tfoot>
		<td colspan="3"><?php echo ($counter - 1) . ' appointment';
	if (($counter - 1) != 1) {
		echo 's';
	}
	echo ' currently scheduled'; ?></td>
	</tfoot>
</table>
<input type="button" id="cancel" value="Cancel operation" />
<button id="confirm">Confirm slot</button>
</div>