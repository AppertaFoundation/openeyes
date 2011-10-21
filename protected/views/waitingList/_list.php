<div id="waitingList">
<?php

if (empty($operations)) { ?>
<h2 class="theatre">Waiting list empty.</h2>
<?php
} else {
?>
    <table>
    <tr>
	<th class="repeat leftAlign">&nbsp;</th>
	<th class="repeat leftAlign">Patient</th>
	<th class="repeat leftAlign">Hosnum</th>
	<th class="repeat leftAlign">Procedures</th>
	<th class="repeat leftAlign">Eye</th>
	<th class="repeat leftAlign">Consultant</th>
	<th class="repeat leftAlign">Decision Date</th>
	<th class="repeat leftAlign">Book Status</th>
    </tr>
<?php
	foreach ($operations as $id => $operation) {
		$eo = ElementOperation::model()->findByPk($operation['eoid']);
		$consultant = $eo->event->episode->firm->getConsultant();
		$user = $consultant->contact->userContactAssignment->user;
?>
    <tr>
<?php
	$letterStatus = $eo->getLetterStatus();
?>
	<td class="letterStatus<?php echo $letterStatus ?> leftAlign">
	&nbsp;
	</td>
	<td class="patient leftAlign">
<?php
	echo CHtml::link(
		$operation['first_name'] . ' ' . $operation['last_name'],
		'/patient/episodes/' . $operation['pid'] . '/event/' . $operation['evid']
	);
?>
</td><td>
<?php echo $operation['hos_num'] ?>
</td><td>
<?php echo $operation['List'] ?>
</td><td>
<?php echo $eo->getEyeText() ?>
</td><td>
<?php echo $user->title . ' ' . $user->first_name . ' ' . $user->last_name . ' (' . $eo->event->episode->firm->serviceSpecialtyAssignment->specialty->name . ')' ?>
</td><td>
<?php echo $eo->convertDate($eo->decision_date) ?>
</td><td>
<?php echo $eo->getStatusText() ?>
</td></tr>
<?php
	}
?>
</table>
<?php
}
?>
</div>
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
