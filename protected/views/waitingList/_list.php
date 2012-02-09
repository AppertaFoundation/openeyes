<div id="no_gp_warning" class="alertBox" style="display: none;">One or more patients has no GP, please correct in PAS before printing GP letter.</div>
<div id="waitingList" class="grid-view-waitinglist">
<?php
if (empty($operations)) { ?>
<h2 class="theatre">Partial bookings waiting list empty.</h2>
<?php
} else {
?>
	<table>
		<tbody>
    	<tr>
				<th>Letters sent</th>
				<th>Patient</th>
				<th>Hospital number</th>
				<th>Location</th>
				<th>Procedure</th>
				<th>Eye</th>
				<th>Firm</th>
				<th>Decision date</th>
				<th>Priority</th>
				<th>Book status (requires...)</th>
				<th><input style="margin-top: 0.4em;" type="checkbox" id="checkall" value="" /></th>
			</tr>
<?php
	$i = 0;
	foreach ($operations as $id => $operation) {
		$eo = ElementOperation::model()->findByPk($operation['eoid']);
		if (isset($_POST['status']) and $_POST['status'] != '') {
			if ($eo->getNextLetter() != $_POST['status']) {
				continue;
			} else {
				# echo "match";
			}
		}
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
	<td class="letterStatus">
<?php
	$lastletter = $eo->getLastLetter();

	if (is_null($lastletter)) {

	} elseif ($lastletter == ElementOperation::LETTER_INVITE) {
		?>
			<img src="img/_elements/icons/letters/invitation.png" alt="Invitation" width="17" height="17" />
		<?php
	} elseif ($lastletter == ElementOperation::LETTER_REMINDER_1) {
		?>
			<img src="img/_elements/icons/letters/invitation.png" alt="Invitation" width="17" height="17" />
			<img src="img/_elements/icons/letters/letter1.png" alt="1st reminder" width="17" height="17" />
		<?php

	} elseif ($lastletter == ElementOperation::LETTER_REMINDER_2) {
		?>
			<img src="img/_elements/icons/letters/invitation.png" alt="Invitation" width="17" height="17" />
			<img src="img/_elements/icons/letters/letter1.png" alt="1st reminder" width="17" height="17" />
			<img src="img/_elements/icons/letters/letter2.png" alt="2nd reminder" width="17" height="17" />
		<?php

	} elseif ($lastletter == ElementOperation::LETTER_GP) {
		?>
			<img src="img/_elements/icons/letters/invitation.png" alt="Invitation" width="17" height="17" />
			<img src="img/_elements/icons/letters/letter1.png" alt="1st reminder" width="17" height="17" />
			<img src="img/_elements/icons/letters/letter2.png" alt="2nd reminder" width="17" height="17" />
			<img src="img/_elements/icons/letters/GP.png" alt="GP" width="17" height="17" />
		<?php
	}
?>
	</td>
	<td class="patient">
		<?php echo CHtml::link(trim("<b>" . $operation['last_name']) . '</b>, ' . $operation['first_name'], '/patient/episodes/' . $operation['pid'] . '/event/' . $operation['evid'])?>
	</td>
	<td style="width: 53px;"><?php echo $operation['hos_num'] ?></td>
	<td style="width: 95px;"><?php echo $eo->site->short_name?></td>
	<td><?php echo $operation['List'] ?></td>
	<td><?php echo $eo->getEyeText() ?></td>
	<td><?php echo $eo->event->episode->firm->name ?> (<?php echo $eo->event->episode->firm->serviceSpecialtyAssignment->specialty->name ?>)</td>
	<td style="width: 80px;"><?php echo $eo->NHSDate('decision_date') ?></td>
	<td><?php echo ($eo->urgent) ? 'Urgent' : 'Routine' ?></td>
	<td><?php echo ucfirst(preg_replace('/^Requires /','',$eo->getStatusText())) ?></td>
	<td<?php if ($tablecolour == 'White' && Yii::app()->user->checkAccess('admin')) { ?> class="admin-td"<?php } ?>>
		<?php if ($eo->getDueLetter() != ElementOperation::LETTER_GP || $operation['gp_id'] || Yii::app()->user->checkAccess('admin')) { ?>
			<input<?php if ($tablecolour == 'White' && !Yii::app()->user->checkAccess('admin')) { ?> disabled="disabled"<?php } ?> type="checkbox" id="operation<?php echo $operation['eoid']?>" value="1" />
		<?php }  ?>
		<?php if($eo->getDueLetter() == ElementOperation::LETTER_GP && !$operation['gp_id'] ) { ?>
			<script type="text/javascript">
				$('#no_gp_warning').show();
			</script>
			<span style="color:red;">NO GP</span>
		<?php } ?>
	</td>
</tr>

<?php
		$i++;
	}

	if ($i == 0) { ?>
	<tr>
		<td colspan="7" style="border: none; padding-top: 10px;">
			There are no patients who match the specified criteria.
		</td>
	</tr>
	<?php }
?>
</tbody>
</table>
<?php
}
?>

<script type="text/javascript">
$('#checkall').click(function() {
	$('input[id^="operation"]:enabled').attr('checked',$('#checkall').is(':checked'));
});
</script>
</div> <!-- #waitingList -->
