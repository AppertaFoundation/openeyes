<div class="box admin">
	<div>
		<h2>Pedigree ID: <?=$pedigree->id?></h2>
		<?php
		$this->widget('Caption',
			array(
				'label'=>'Inheritance',
				'value'=>@$pedigree->inheritance->name,
			));
		$this->widget('Caption',
			array(
				'label'=>'Gene',
				'value'=>@$pedigree->gene->name,
			));
		?>
		<fieldset id="Pedigree_diagnosis" class="row field-row">
			<legend class="large-2 column">Pedigree Diagnosis:</legend>
			<input type="hidden" value="" name="Pedigree[consanguinity]">
			<div class="large-4 column end">
				<?php if ($pedigree->disorder) { ?>
					<div id="enteredDiagnosisText" class="panel diagnosis hide" style="display: block;"><?php echo $pedigree->disorder->fully_specified_name?></div>
				<?php } else { ?>
					<div id="enteredDiagnosisText" class="panel diagnosis hide" style="display: block;">No Pedigree Diagnosis</div>
				<?php } ?>
			</div>
		</fieldset>


		<?php
		$this->widget('Caption',
			array(
				'label'=>'Consanguinity',
				'value'=>@$pedigree->consanguinity,
			));
		$this->widget('Caption',
			array(
				'label'=>'Base Change',
				'value'=>@$pedigree->base_change,
			));
		$this->widget('Caption',
			array(
				'label'=>'Amino Acid Change',
				'value'=>@$pedigree->amino_acid_change,
			));
		$this->widget('Caption',
			array(
				'label'=>'Comments',
				'value'=>@Yii::app()->format->ntext($pedigree->comments),
			));
		?>
		<table class="grid">
			<thead>
			<tr>
				<th>ID</th>
				<th>Hospital no</th>
				<th>Title</th>
				<th>First name</th>
				<th>Last name</th>
				<th>DoB</th>
				<th>Gender</th>
				<th>Status</th>
				<th>DNA available</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
			<?php if ($pedigree->members) {
				foreach ($pedigree->members as $pm) {?>
					<tr>
						<td><?php echo CHtml::link($pm->patient->id,Yii::app()->createUrl('/patient/view/'.$pm->patient_id))?></td>
						<td><?php echo $pm->patient->hos_num?></td>
						<td><?php echo $pm->patient->title?></td>
						<td><?php echo $pm->patient->first_name?></td>
						<td><?php echo $pm->patient->last_name?></td>
						<td><?php echo $pm->patient->dob ? $pm->patient->NHSDate('dob') : $pm->patient->yob?></td>
						<td><?php echo $pm->patient->gender == 'M' ? 'Male' : 'Female'?></td>
						<td><?php echo $pm->status->name?></td>
						<td>
							<?php echo Element_OphInDnaextraction_DnaExtraction::model()->with(array('event' => array('with' => 'episode')))->find('patient_id=?',array($pm->patient_id)) ? 'Yes' : 'No'?>
						</td>
						<td><?php echo CHtml::link('Remove',Yii::app()->createUrl('/Genetics/default/removePatient/'.$pm->patient_id))?></td>
					</tr>
				<?php }
			} else {?>
				<tr>
					<td colspan="7">
						There are no family members in this pedigree.
					</td>
				</tr>
				<?php }?>
				<tfoot class="pagination-container">
				<tr>
					<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
						<td colspan="6">
							<?php echo EventAction::button('Add patient to pedigree', 'add', null, array('class' => 'small', 'id'=>'add_patient_pedigree', 'data-pedigree-id'=>@$_GET['id']))->toHtml()?>
						</td>
					<?php } ?>
				</tr>
				</tfoot>

			</tbody>
		</table>
	</div>
</div>

