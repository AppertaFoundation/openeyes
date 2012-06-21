<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div id="no_gp_warning" class="alertBox" style="display: none;">One or more patients has no GP, please correct in PAS before printing GP letter.</div>
<div id="waitingList" class="grid-view-waitinglist">
<?php
if (empty($bookings['bookings'])) { ?>
<h3>No bookings match your search criteria.</h3>
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
				<th>Subspecialty</th>
				<th>DTA</th>
				<th>Priority</th>
				<th><input style="margin-top: 0.4em;" type="checkbox" id="checkall" value="" /></th>
			</tr>
<?php
	$i = 0;
	if ($bookings) {
		foreach ($bookings['bookings'] as $id => $booking) {
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
			<?php echo CHtml::link(trim("<b>" . $booking['last_name']) . '</b>, ' . $booking['first_name'], '/patient/event/' . $booking['evid'])?>
		</td>
		<td style="width: 83px;"><?php echo date('j-M-Y',strtotime($booking['session_date']))?></td>
		<td style="width: 73px;"><?php echo $booking['session_time']?></td>
		<td style="width: 95px;"><?php echo $booking['location']?></td>
		<td style="width: 170px;"><?php echo $booking['ward_name']?></td>
		<td style="width: 53px;"><?php echo $booking['method']?></td>
		<td style="width: 43px;"><?php echo $booking['firm'] ?></td>
		<td style="width: 53px;"><?php echo $booking['subspecialty']?></td>
		<td style="width: 80px;"><?php echo Helper::convertMySQL2NHS($booking['decision_date']) ?></td>
		<td><?php echo $booking['priority']?></td>
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

<div style="display: none;">
	<div id="printable">
		<table>
			<thead>
				<tr>
					<th>Hospital number</th>
					<th>Patient</th>
					<th>TCI date</th>
					<th>Admission time</th>
					<th>Site</th>
					<th>Ward</th>
					<th>Method</th>
					<th>Firm</th>
					<th>Subspecialty</th>
					<th>DTA</th>
					<th>Priority</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($bookings['bookings_all'] as $id => $booking) {?>
					<tr>
						<td style="width: 53px;"><?php echo $booking['hos_num'] ?></td>
						<td>
							<?php echo "<b>" . trim($booking['last_name']) . '</b>, ' . trim($booking['first_name'])?>
						</td>
						<td style="width: 83px;"><?php echo date('j-M-Y',strtotime($booking['session_date']))?></td>
						<td style="width: 73px;"><?php echo $booking['session_time']?></td>
						<td style="width: 95px;"><?php echo $booking['location']?></td>
						<td style="width: 170px;"><?php echo $booking['ward_name']?></td>
						<td style="width: 53px;"><?php echo $booking['method']?></td>
						<td style="width: 43px;"><?php echo $booking['firm'] ?></td>
						<td style="width: 53px;"><?php echo $booking['subspecialty']?></td>
						<td style="width: 80px;"><?php echo Helper::convertMySQL2NHS($booking['decision_date']) ?></td>
						<td><?php echo $booking['priority']?></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
$('#checkall').click(function() {
	$('input[name^="cancelled"]').attr('checked',$('#checkall').is(':checked'));
	$('input[name^="booked"]').attr('checked',$('#checkall').is(':checked'));
});
</script>
</div> <!-- #waitingList -->
