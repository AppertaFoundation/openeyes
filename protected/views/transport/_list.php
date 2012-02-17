<div id="no_gp_warning" class="alertBox" style="display: none;">One or more patients has no GP, please correct in PAS before printing GP letter.</div>
<div id="waitingList" class="grid-view-waitinglist">
<?php
if (empty($bookings)) { ?>
<h2 class="theatre">No bookings have been made today.</h2>
<?php
} else {
?>
	<table>
		<tbody>
    	<tr>
				<th>Hospital number</th>
				<th>Patient</th>
				<th>TCI date</th>
				<th>Admission time</th>
				<th>Site</th>
				<th>Ward</th>
				<th>Method</th>
				<th>Firm</th>
				<th>Specialty</th>
				<th>DTA</th>
				<th>Priority</th>
				<th><input style="margin-top: 0.4em;" type="checkbox" id="checkall" value="" /></th>
			</tr>
<?php
	$i = 0;
	if ($bookings) {
		foreach ($bookings as $id => $booking) {
			if (isset($last_eoid) && $last_eoid == $booking['eoid']) continue;
	?>

	<?php
		if ($booking['transport'] == null) {
			if (strtotime($booking['session_date']) <= (strtotime(date('Y-m-d')) + 86400)) {
				$tablecolour = "Red";
			} else {
				$tablecolour = "Green";
			}
		} else {
			$tablecolour = "Grey";
		}
	?>
			<tr class="waitinglist<?php echo $tablecolour ?>">
			<?php
	?>
		<td style="width: 53px;"><?php echo $booking['hos_num'] ?></td>
		<td class="patient">
			<?php echo CHtml::link(trim("<b>" . $booking['last_name']) . '</b>, ' . $booking['first_name'], '/patient/episodes/' . $booking['phash'] . '/event/' . $booking['evid'])?>
		</td>
		<td style="width: 83px;"><?php echo date('j-M-Y',strtotime($booking['session_date']))?></td>
		<td style="width: 73px;"><?php echo $booking['session_time']?></td>
		<td style="width: 95px;"><?php echo $booking['location']?></td>
		<td style="width: 170px;"><?php echo $booking['ward_name']?></td>
		<td style="width: 53px;"><?php echo $booking['method']?></td>
		<td style="width: 43px;"><?php echo $booking['firm'] ?></td>
		<td style="width: 53px;"><?php echo $booking['specialty']?></td>
		<td style="width: 80px;"><?php echo $booking['decision_date'] ?></td>
		<td><?php echo ($booking['urgent']) ? 'Urgent' : 'Routine' ?></td>
		<td style="width: 20px;">
			<?php if ($booking['method'] == 'Cancelled') {?>
				<input type="checkbox" name="cancelled[]" value="<?php echo $booking['checkid']?>" />
			<?php }else{?>
				<input type="checkbox" name="booked[]" value="<?php echo $booking['checkid']?>" />
			<?php }?>
		</td>
	</tr>

	<?php
			$i++;
			$last_eoid = $booking['eoid'];
		}

		if ($i == 0) { ?>
		<tr>
			<td colspan="7" style="border: none; padding-top: 10px;">
				There is no relevant activity for the selected date.
			</td>
		</tr>
		<?php }
	?>
	</tbody>
	</table>
	<?php
	}
}
?>

<script type="text/javascript">
$('#checkall').click(function() {
	$('input[name^="cancelled"]').attr('checked',$('#checkall').is(':checked'));
	$('input[name^="booked"]').attr('checked',$('#checkall').is(':checked'));
});
</script>
</div> <!-- #waitingList -->
