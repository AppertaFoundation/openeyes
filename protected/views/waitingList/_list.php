<div id="waitingList">
<?php

if (empty($operations)) { ?>
<h2 class="theatre">Partial bookings waiting list empty.</h2>
<?php
} else {
?>
					<div id="waitingList" class="grid-view">
					    <table>
					    <tbody>

				    	<tr>
								<th>Letter status</th>
								<th>Patient</th>
								<th>Hospital number</th>
								<th>Location</th>
								<th>Procedure</th>
								<th>Eye</th>
								<th>Firm</th>
								<th>Decision date</th>
								<th>Priority</th>
								<th>Book status</th>
								<th>Select<br/><input style="margin-top: 0.4em;" type="checkbox" id="checkall" value="" /></th>
							</tr>
<?php
	$i = 0;
	foreach ($operations as $id => $operation) {
		$eo = ElementOperation::model()->findByPk($operation['eoid']);
//		$consultant = $eo->event->episode->firm->getConsultant();
//		$user = $consultant->contact->userContactAssignment->user;
?>

<?php
	if ($eo->getWaitingListStatus() == ElementOperation::STATUS_PURPLE) {
		$tablecolour = "Purple";
	} elseif ($eo->getWaitingListStatus() == ElementOperation::STATUS_GREEN1) {
		$tablecolour = "Green";
	} elseif ($eo->getWaitingListStatus() == ElementOperation::STATUS_GREEN2) {
		$tablecolour = "Green";
	} elseif ($eo->getWaitingListStatus() == ElementOperation::STATUS_ORANGE) {
		$tablecolour = "Orange";
	} elseif ($eo->getWaitingListStatus() == ElementOperation::STATUS_RED) {
		$tablecolour = "Red";
	} else {
		$tablecolour = "White";
	}
?>
    <tr class="waitinglist<?php echo $tablecolour ?>">
<?php
	$letterStatus = $eo->getLetterStatus();

	switch ($letterStatus) {
		case ElementOperation::LETTER_INVITE:
			$letterImage = 'invitation';
			break;
		case ElementOperation::LETTER_REMINDER_1:
			$letterImage = 'letter1';
			break;
		case ElementOperation::LETTER_REMINDER_2:
			$letterImage = 'letter2';
			break;
		case ElementOperation::LETTER_GP:
			$letterImage = 'GP';
			break;
		case ElementOperation::LETTER_REMOVAL:
			$letterImage = 'to-be-removed';
			break;
		default:
			$letterImage = 'invitation';
			break;
	}
?>
	<td class="letterStatus">
		<img src="img/_elements/icons/letters/<?php echo $letterImage ?>.png" alt="<?php echo $letterImage ?>" width="17" height="17" />
	</td>
	<td class="patient">
		<?php echo CHtml::link(trim($operation['last_name']) . ', ' . $operation['first_name'], '/patient/episodes/' . $operation['pid'] . '/event/' . $operation['evid'])?>
	</td>
	<td><?php echo $operation['hos_num'] ?></td>
	<td><?php echo $eo->site->short_name?></td>
	<td><?php echo $operation['List'] ?></td>
	<td><?php echo $eo->getEyeText() ?></td>
	<td><?php echo $eo->event->episode->firm->name ?> (<?php echo $eo->event->episode->firm->serviceSpecialtyAssignment->specialty->name ?>)</td>
	<td><?php echo $eo->NHSDate('decision_date') ?></td>
	<td><?php echo ($eo->urgent) ? 'Urgent' : 'Routine' ?></td>
	<td><?php echo $eo->getStatusText() ?></td>
	<td><input type="checkbox" id="operation<?php echo $operation['eoid']?>" value="1" /></td>
</tr>

<?php
		$i++;
	}
?>
</table>
<?php
}
?>
</tbody>

</table>
<script type="text/javascript">
$('#checkall').click(function() {
	$('input[id^="operation"]').attr('checked',$('#checkall').is(':checked'));
});
</script>
</div> <!-- #waitingList -->
