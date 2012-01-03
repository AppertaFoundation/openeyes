<div class="pageBreak"></div>
<div class="banner compact">
	<div class="logo"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></div>
</div>
<h1>Admission Form</h1>
<?php
if ($patient->address === NULL) {
	$patient->address = Address::Model()->findByPk($patient->address_id);
}
?>
<table class="half right">
	<tr>
		<th>Patient Name</th>
		<td><?php echo $patient->fullname ?></td>
	</tr>
	<tr>
		<th>Address</th>
		<td><?php echo $patient->address->letterhtml ?></td>
	</tr>
</table>	
<table class="half">
	<tr>
		<th>Hospital Number</th>
		<td><?php echo $patient->hos_num ?></td>
	</tr>
	<tr>
		<th>DOB</th>
		<td><?php echo date('d M Y', strtotime($patient->dob)) ?></td>
	</tr>
</table>
