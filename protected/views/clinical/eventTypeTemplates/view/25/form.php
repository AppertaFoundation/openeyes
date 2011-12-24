<?php $this->renderPartial("/clinical/eventTypeTemplates/view/25/form_start", array(
	'patientDetails' => $patientDetails,
	'patientName' => $patientName,
	'patient' => $patient,
)); ?>

<table width="100%">

	<tr>
		<td width="25%"
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Admitting
				Consultant:</strong></td>
		<td width="25%"
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo $consultantName ?>
		</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Decision
				to admit date (or today's date):</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo date('d M Y', strtotime($operation->decision_date)) ?>
		</td>
	</tr>

	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">Service:</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo CHtml::encode($event->episode->firm->serviceSpecialtyAssignment->specialty->name) ?>
		</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">Telephone:</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo CHtml::encode($patient->primary_phone) ?>&nbsp;</td>
	</tr>

	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">Site:</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo CHtml::encode($site->name) ?>
		</td>
		<td colspan="2"
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
			<table width="100%" class="subTableNoBorders" style="border: none;">
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Person
				organising admission:</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo $consultantName ?>
		</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Dates
				patient unavailable:</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="2"
			style="border-bottom: 1px dotted #000; border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">Signature:</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">Available
			at short notice:</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">&nbsp;</td>
	</tr>

</table>

<span class="subTitle" style="font-family: sans-serif; font-size: 10pt;">ADMISSION DETAILS</span>

<table width="100%">

	<tr>
		<td width="25%"
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Urgency:</strong>
		</td>
		<td width="25%"
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">&nbsp;</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Consultant
				to be present:</strong></td>
		<td style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
			<?php echo (empty($operation->consultant_required)) ? 'No' : 'Yes'; ?>
		</td>
	</tr>

	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Admission
				category:</strong></td>
		<td	style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
				<?php echo ($operation->overnight_stay) ? 'an overnight stay' : 'day case'; ?>
		</td>
		<?php
		if (empty($operation->booking)) { ?>
		<td colspan="2" rowspan="5" align="center"
			style="vertical-align: middle; font-family: sans-serif; font-size: 10pt; border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
			<strong>Patient Added to Waiting List.<br />
			Admission Date to be arranged</strong>
		</td>
		<?php } else { 	?>
		<td style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
			<strong>Operation date:</strong></td>
		<td style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
			<?php echo date('d M Y', strtotime($operation->booking->session->date)) ?>
		</td>
		<?php } ?>
	</tr>
	
	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Diagnosis:</strong>
		</td>
		<td	style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
			<?php if ($operation->getDisorder()) {
				echo $operation->getEyeText() . ' ' . CHtml::encode($operation->getDisorder());
			} else {
				echo 'Unknown';
			} ?>
		</td>

		<?php if (!empty($operation->booking)) { ?>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Discussed
				with patient:</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">&nbsp;</td>
			<?php	} ?>
	</tr>
	
	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Intended
				procedure:</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">
			<?php echo CHtml::encode(implode(', ', $procedureList)) ?>
		</td>

		<?php	if (!empty($operation->booking)) { ?>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Theatre
				session:</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;">&nbsp;</td>
			<?php } ?>
	</tr>
	
	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Eye:</strong>
		</td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo $operation->getEyeText() ?>
		</td>

		<?php if (!empty($operation->booking)) { 	?>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Admission
				time:</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo $operation->booking->admission_time ?>
		</td>
		<?php } ?>
	</tr>
	
	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Total
				theatre time (mins):</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo CHtml::encode($operation->total_duration) ?>
		</td>
		
		<?php if (!empty($operation->booking)) { ?>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Proposed
				admission date:</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?>
		</td>
		<?php } ?>
	</tr>
	
	<tr>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><strong>Anaesthesia:
			</strong></td>
		<td
			style="border: 1px solid #000; font-family: sans-serif; font-size: 10pt;"><?php echo $operation->getAnaestheticText() ?>
		</td>
	</tr>
	
</table>

<?php $this->renderPartial("/clinical/eventTypeTemplates/view/25/form_end"); ?>
