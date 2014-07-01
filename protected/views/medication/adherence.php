<table class="plain patient-data">
	<tr>
		<th width="128">Adherence</th>
		<td><?=@$patient->adherence ? $patient->adherence->level->name : 'Not Recorded'?></td>
	</tr>
	<tr>
		<th width="128">Comments</th>
		<td><?=@$patient->adherence->comments ? $patient->adherence->textWithLineBreaks('comments') : 'Not Recorded'?></td>
	</tr>
	<?php if ($this->checkAccess('OprnEditMedication')): ?>
		<tr>
			<th>Actions</th>
			<td>
				<a href="#" class="medication_edit" data-id="adherence">Edit</a>
			</td>
		</tr>
	<?php endif ?>
</table>