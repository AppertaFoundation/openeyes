<p<?php if(@$accessible) { ?> class="accessible"<?php } ?>>Dear <?php echo $to; ?>,</p>

<p<?php if(@$accessible) { ?> class="accessible"<?php } ?>>
	<strong><?php if(@$patient_ref) { 
		echo $patient->fullname . ', ';
	} ?>
	Hospital Reference Number: <?php echo $patient->hos_num; ?>
	<?php if($patient->nhsnum) { ?><br/> NHS Number: <?php echo $patient->nhsnum; }?>
	<?php if(@$patient_ref) { ?>
	<br /><?php echo $patient->correspondAddress->letterline ?>
	<br />DOB: <?php echo $patient->NHSDate('dob') ?>, <?php echo ($patient->gender == 'M') ? 'Male' : 'Female'; ?>
	<?php } ?></strong>
</p>

