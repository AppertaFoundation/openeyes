<div style="page-break-after:always;"></div>
<div id="printForm" style="display:block; background:#000; font-size:7pt;">
	<div id="printFormTemplate">
		<table width="100%">
			<tr>
				<td colspan="2" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td>
				<td colspan="4" style="text-align:right; padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">
					<img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" />
				</td>
			</tr>
			<tr>
				<td colspan="2" width="50%" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">
					<span class="title" style="font-size:13pt; font-weight: bold;">Admission Form</span>
				</td>
				<td rowspan="4" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td>
				<td rowspan="4" style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">
					<?php echo $patientName ?><br />
					<?php echo $patientDetails ?>
				</td>
			</tr>
			<tr>
				<td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">Hospital Number</td>
				<td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;"><?php echo $patient->hos_num ?></td>
			</tr>
			<tr>
				<td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">DOB</td>
				<td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;"><?php echo date('d M Y', strtotime($patient->dob)) ?></td>
			</tr>
			<tr>
				<td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td>
				<td style="padding:1em 0.5em; border:none; font-family: sans-serif; font-size:10pt;">&nbsp;</td>
			</tr>
		</table>			
