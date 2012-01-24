<div class="banner compact">
	<div class="logo"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></div>
</div>
<h1>Transport Form</h1>
<table>
	<tr>
		<th>Transport request to</th>
		<td><?php echo $transport['request_to'] ?></td>
	</tr>
	<tr>
		<th>Transport request from</th>
		<td><?php echo $transport['request_from'] ?></td>
	</tr>
	<tr>
		<th>Date</th>
		<td><?php echo date(Helper::NHS_DATE_FORMAT) ?></td>
	</tr>
</table>
<p class="centered">
		<strong>Request for non-urgent transport</strong>
		<br /><?php echo $patient->id ?> - <?php echo $patient->fullname ?>
		<br />Area: <?php echo $transport['area'] ?>
		<br />Date: <?php echo date(Helper::NHS_DATE_FORMAT, strtotime($transport['date'])) ?>
</p>
<p>
	Please transport the patient from the following address to the hospital on <?php echo date(Helper::NHS_DATE_FORMAT, strtotime($transport['date'])) ?>
</p>
<p>
	<?php echo $patient->fullname ?>
	<br /><?php echo $patient->address->letterhtml ?>
	<br /><?php echo 'FIXME: Phone number' ?>
</p>
<p>
	<?php echo $patient->fullname ?> is due to attend <?php $transport['destination'] ?> at <?php echo $transport['appointment_time'] ?>
</p>
<table>
	<tr>
		<th>Escort</th>
		<td><?php echo $transport['escort'] ?></td>
	</tr>
	<tr>
		<th>Mobility</th>
		<td><?php echo $transport['mobility'] ?></td>
	</tr>
	<tr>
		<th>Age</th>
		<td><?php echo $patient->age ?></td>
	</tr>
	<tr>
		<th>Comments</th>
		<td><?php echo $transport['comments'] ?></td>
	</tr>
	<tr>
		<th>Oxygen</th>
		<td><?php echo $transport['oxygen'] ?></td>
	</tr>
</table>
<p>
	Authorised by: <strong><?php echo $transport['request_from'] ?></strong>
</p>
<p>
	If you have any questions regarding the above booking, please telephone <?php echo $transport['contact_name'] ?> on <?php echo $transport['contact_number'] ?>
</p>