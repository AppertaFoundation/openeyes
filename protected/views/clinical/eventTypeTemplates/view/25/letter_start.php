<div id="letters" style="display:block; background:#000; font-family: sans-serif; font-size:10pt;">
	<div id="letterTemplate">
		<div id="l_address">
			<table width="100%">
				<tr>
					<td style="text-align:left;" width="50%"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></td>
					<td style="text-align:right; font-family: sans-serif; font-size:10pt;"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:right; font-family: sans-serif; font-size:10pt;">
						<?php
							foreach (array('name', 'address1', 'address2', 'address3', 'postcode') as $field) {
								if (!empty($site->$field)) {
									echo CHtml::encode($site->$field) . '<br />';
								}
							}
							?>
							<br />
							Tel: <?php echo CHtml::encode($site->telephone) ?><br />
							Fax: <?php echo CHtml::encode($site->fax) ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left; font-family: sans-serif; font-size:10pt;">
						<?php echo $patientName ?>
						<?php echo $patientDetails ?>
					</td>
				</tr>

				<tr>
					<td colspan="2" style="text-align:right; font-family: sans-serif; font-size:10pt;">
						<?php echo date('d M Y') ?>
					</td>
				</tr>
			</table>
		</div>

		<div id="l_content" style="font-family: sans-serif; font-size:10pt;">
			<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">
				<strong>Hospital number reference: <?php echo $patient->hos_num ?>
					<?php if (!empty($patient->nhs_num)) { ?>
						<br />NHS number: <?php echo $patient->nhs_num; } ?>
				</strong>
			</p>

			<p style="font-family: sans-serif; font-size:10pt; margin-bottom:1em;">
				Dear <?php echo $patientName ?>,
			</p>

