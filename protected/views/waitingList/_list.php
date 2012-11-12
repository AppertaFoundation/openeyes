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
<div id="pas_warnings" class="alertBox" style="display: none;">
	<div class="no_gp" style="display: none;">One or more patients has no GP practice address, please correct in PAS before printing GP letter.</div>
	<div class="no_address" style="display: none;">One or more patients has no Address, please correct in PAS before printing a letter for them.</div>
</div>
<div id="waitingList" class="grid-view">
<?php
if (empty($operations)) { ?>
<h2 class="theatre">Partial bookings waiting list empty.</h2>
<?php
} else {
?>
	<table class="waiting-list">
		<tbody>
    	<tr>
				<th>Letters sent</th>
				<th style="width: 120px;">Patient</th>
				<th style="width: 53px;">Hospital number</th>
				<th style="width: 95px;">Location</th>
				<th>Procedure</th>
				<th>Eye</th>
				<th>Firm</th>
				<th style="width: 80px;">Decision date</th>
				<th>Priority</th>
				<th>Book status (requires...)</th>
				<th><input style="margin-top: 0.4em;" type="checkbox" id="checkall" value="" /> All</th>
			</tr>
<?php
	$i = 0;
	foreach ($operations as $id => $operation) {
		$eo = ElementOperation::model()->findByPk($operation['eoid']);
		
		$patient = NULL;
		if(isset($operation['pid'])){
			$patient = Patient::model()->noPas()->findByPk($operation['pid']);
		}
		if (isset($_POST['status']) and $_POST['status'] != '') {
			if ($eo->getNextLetter() != $_POST['status']) {
				continue;
			} else {
				# echo "match";
			}
		}
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
    <tr class="waitinglist<?php echo ($i % 2 == 0) ? 'Even' : 'Odd'; ?>">
	<td class="letterStatus waitinglist<?php echo $tablecolour ?>">
<?php
	$lastletter = $eo->getLastLetter();

	if (is_null($lastletter)) {

	} elseif ($lastletter == ElementOperation::LETTER_INVITE) {
		?>
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/invitation.png')?>" alt="Invitation" width="17" height="17" />
		<?php
	} elseif ($lastletter == ElementOperation::LETTER_REMINDER_1) {
		?>
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/invitation.png')?>" alt="Invitation" width="17" height="17" />
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/letter1.png')?>" alt="1st reminder" width="17" height="17" />
		<?php

	} elseif ($lastletter == ElementOperation::LETTER_REMINDER_2) {
		?>
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/invitation.png')?>" alt="Invitation" width="17" height="17" />
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/letter1.png')?>" alt="1st reminder" width="17" height="17" />
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/letter2.png')?>" alt="2nd reminder" width="17" height="17" />
		<?php

	} elseif ($lastletter == ElementOperation::LETTER_GP) {
		?>
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/invitation.png')?>" alt="Invitation" width="17" height="17" />
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/letter1.png')?>" alt="1st reminder" width="17" height="17" />
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/letter2.png')?>" alt="2nd reminder" width="17" height="17" />
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/GP.png')?>" alt="GP" width="17" height="17" />
		<?php
	}
?>
	</td>
	<td class="patient">
		<?php echo CHtml::link("<strong>" . trim(strtoupper($operation['last_name'])) . '</strong>, ' . $operation['first_name'], Yii::app()->createUrl('/patient/event/' . $operation['evid']))?>
	</td>
	<td><?php echo $operation['hos_num'] ?></td>
	<td><?php echo $eo->site->short_name?></td>
	<td><?php echo $operation['List'] ?></td>
	<td><?php echo $eo->eye->name ?></td>
	<td><?php echo $eo->event->episode->firm->name ?> (<?php echo $eo->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->name ?>)</td>
	<td><?php echo $eo->NHSDate('decision_date') ?></td>
	<td><?php echo $eo->priority->name?></td>
	<td><?php echo ucfirst(preg_replace('/^Requires /','',$eo->getStatusText())) ?></td>
	<td<?php if ($tablecolour == 'White' && Yii::app()->user->checkAccess('admin')) { ?> class="admin-td"<?php } ?>>

		<?php if(($patient && $patient->address) && $operation['eoid'] && ($eo->getDueLetter() != ElementOperation::LETTER_GP || ($eo->getDueLetter() == ElementOperation::LETTER_GP && $operation['practice_id']))) { ?>
		<div>	
			<input<?php if ($tablecolour == 'White' && !Yii::app()->user->checkAccess('admin')) { ?> disabled="disabled"<?php } ?> type="checkbox" id="operation<?php echo $operation['eoid']?>" value="1" />
		</div>
		<?php }?>
		
		<?php if(!$operation['practice_address_id'] ) { ?>
			<script type="text/javascript">
				$('#pas_warnings').show();
				$('#pas_warnings .no_gp').show();
			</script>
			<span class="no-GP">No GP</span>
		<?php } ?>
		
		<?php if($patient && !$patient->address){ ?>
			<script type="text/javascript">
				$('#pas_warnings').show();
				$('#pas_warnings .no_address').show();
			</script>
			<span class="no-Address">No Address</span>
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
<tfoot>
	<tr>
		<td colspan="11">
			<div id="key">
			<span>Colour Key:</span>
				<div class="container" id="sendflag-invitation"><div class="color_box"></div><div class="label">Send invitation letter</div></div>
				<div class="container" id="sendflag-reminder"><div class="color_box"></div><div class="label">Send another reminder (2 weeks)</div></div>
				<div class="container" id="sendflag-GPremoval"><div class="color_box"></div><div class="label">Send GP removal letter</div></div>
				<div class="container" id="sendflag-remove"><div class="color_box"></div><div class="label">Patient is due to be removed</div></div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="11" class="small">
			<div id="letters-key">
			<span>Letters sent out:</span>&nbsp;&nbsp;
				<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/invitation.png')?>" alt="Invitation" height="17" width="17"> - Invitation
				<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/letter1.png')?>" alt="1st reminder" height="17" width="17"> - 1<sup>st</sup> Reminder
				<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/letter2.png')?>" alt="2nd reminder" height="17" width="17"> - 2<sup>nd</sup> Reminder
				<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/letters/GP.png')?>" alt="GP" height="17" width="17"> - GP Removal
			</div>
		</td>
	</tr>
</tfoot>
</table>
<?php
}
?>

<script type="text/javascript">
$('#checkall').click(function() {
	$('input[id^="operation"]:enabled').attr('checked',$('#checkall').is(':checked'));
});

// Row highlighting
$(this).undelegate('.waiting-list td','click').delegate('.waiting-list td','click',function() {
    var $tr = $(this).closest("tr");

    //toggle current row
    $tr.toggleClass('hover');
});
</script>
</div> <!-- #waitingList -->
