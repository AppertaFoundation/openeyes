<?php $this->renderPartial("/clinical/eventTypeTemplates/view/25/form_start", array(
	'patientDetails' => $patientDetails,
	'patientName' => $patientName,
	'patient' => $patient,
)); ?>

<h2>Admission Information</h2>
<table>

	<tr>
		<th>Admitting Consultant:</th>
		<td><?php echo $consultantName ?></td>
		<th>Decision to admit date (or today's date):</th>
		<td><?php echo date('d M Y', strtotime($operation->decision_date)) ?></td>
	</tr>

	<tr>
		<th>Service:</th>
		<td><?php echo CHtml::encode($event->episode->firm->serviceSpecialtyAssignment->specialty->name) ?></td>
		<th>Telephone:</th>
		<td><?php echo CHtml::encode($patient->primary_phone) ?></td>
	</tr>

	<tr>
		<th>Site:</th>
		<td><?php echo CHtml::encode($site->name) ?></td>
		<th>Person organising admission:</th>
		<td><?php echo $consultantName ?></td>
	</tr>

	<tr>
		<th>Dates patient unavailable:</th>
		<td>** TODO **</td>
		<th>Available at short notice:</th>
		<td>** TODO **</td>
	</tr>

	<tr>
		<th colspan="2" class="tall">Signature:</th>
	</tr>

</table>

<h2>Admission Details</h2>

<table>

	<tr>
		<th>Urgency:</th>
		<td><?php echo ($operation->urgent) ? 'Urgent' : 'Routine'; ?></td>
		<th>Consultant to be present:</th>
		<td><?php echo (empty($operation->consultant_required)) ? 'No' : 'Yes'; ?></td>
	</tr>

	<tr>
		<th>Admission category:</th>
		<td><?php echo ($operation->overnight_stay) ? 'an overnight stay' : 'day case'; ?></td>
		<?php
		if (empty($operation->booking)) { ?>
		<th colspan="2" rowspan="5">Patient Added to Waiting List, admission Date to be arranged</th>
		<?php } else { 	?>
		<th>Operation date:</th>
		<td><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?></td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Diagnosis:</th>
		<td>
			<?php if ($operation->getDisorder()) {
				echo $operation->getEyeText() . ' ' . CHtml::encode($operation->getDisorder());
			} else {
				echo 'Unknown';
			} ?>
		</td>
		<?php if(!empty($operation->booking)) { ?>
		<th>Discussed with patient:</th>
		<td>** TODO **</td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Intended procedure:</th>
		<td><?php echo CHtml::encode(implode(', ', $procedureList)) ?></td>
		<?php if (!empty($operation->booking)) { ?>
		<th>Theatre session:</td>
		<td><?php echo substr($operation->booking->session->start_time,0,5) . ' - ' . substr($operation->booking->session->end_time,0,5)?></td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Eye:</th>
		<td><?php echo $operation->getEyeText() ?></td>
		<?php if (!empty($operation->booking)) { ?>
		<th>Admission time:</th>
		<td><?php echo $operation->booking->admission_time ?></td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Total theatre time (mins):</th>
		<td><?php echo CHtml::encode($operation->total_duration) ?></td>
		<?php if (!empty($operation->booking)) { ?>
		<th>Proposed admission date:</></th>
		<td><?php echo date('d M Y', strtotime($operation->booking->session->date)) ?></td>
		<?php } ?>
	</tr>
	
	<tr>
		<th>Anaesthesia:</th>
		<td><?php echo $operation->getAnaestheticText() ?></td>
	</tr>
	
</table>

<h2>Comments</h2>
<p><?php echo $operation->comments?></p>
