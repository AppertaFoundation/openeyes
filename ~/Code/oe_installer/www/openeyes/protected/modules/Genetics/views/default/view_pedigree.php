<div class="box admin">
	<div>
		<h2>Family members</h2>
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
			</tr>
			</thead>
			<tbody>
			<?php if ($pedigree->members) {
				foreach ($pedigree->members as $pm) {?>
					<tr class="hover" data-hover="<?php echo $pm->getHoverText()?>">
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
							<?php echo EventAction::button('Add Patient to Pedigree', 'add', null, array('class' => 'small', 'id'=>'add_patient_pedigree', 'data-pedigree-id'=>@$_GET['id']))->toHtml()?>
						</td>
					<?php } ?>
				</tr>
				</tfoot>

			</tbody>
		</table>
	</div>
</div>

