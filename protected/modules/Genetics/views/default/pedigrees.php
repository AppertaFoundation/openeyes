<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<div class="box admin">
	<div class="row">
		<div class="large-8 column">
			<h2>Pedigrees</h2>
		</div>
		<div class="large-12 column">
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm',array(
				'id' => 'searchform',
				'enableAjaxValidation' => false,
				'focus' => '#search',
				'action' => Yii::app()->createUrl('/Genetics/default/pedigrees'),
			))?>
			<div class="large-12 column">
				<div class="panel">
					<div class="row">
						<div class="large-12 column">
							<table class="grid">
								<thead>
								<tr>
									<th width="200px">Family ID:</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>
										<?php echo CHtml::textField('family-id', @$_GET['family-id'], array('placeholder' => 'Enter family ID here...'))?>
									</td>
									<td>
										<button id="search_pedigree_family_id" class="secondary" type="submit">
											Search
										</button>
									</td>
								</tr>
								</tbody>
							</table>
							<table class="grid">
								<thead>
								<tr>
									<th>Causative gene:</th>
									<th>Consanguineous:</th>
									<th>Molecular Diagnosis:</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>
										<?php echo CHtml::dropDownList('gene-id',@$_GET['gene-id'],CHtml::listData(PedigreeGene::model()->findAll(array('order'=>'name asc')),'id','name'),array('empty' => '- All -'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('consanguineous',@$_GET['consanguineous'],array(1 => 'Yes', 0 => 'No'),array('empty' => '- All -'))?>
									</td>
									<td>
										<?php echo CHtml::checkBox('molecular-diagnosis',@$_GET['molecular-diagnosis']=="true")?> Required
									</td>
									<td>
										<button id="search_pedigrees" class="secondary" type="submit">
											Search
										</button>
									</td>
								</tr>
								<tr>
									<td colspan="4">
										<?php $form->widget('application.widgets.DiagnosisSelection',array(
											'value' => @$_GET['disorder-id'],
											'field' => 'principal_diagnosis',
											'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
											'layoutColumns' => array(
												'label' => $form->layoutColumns['label'],
												'field' => 4,
											),
											'default' => false,
											'htmlOptions' => array(
												'fieldLabel' => 'Principal diagnosis',
											),
										))?>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php /*
				<div class="row">
					<div class="large-3 column">
						<label>Family ID:</label>
						<input type="text" name="family_id" id="family_id" placeholder="Enter family ID here..." value="<?php echo strip_tags(@$_POST['family_id'])?>" />
					</div>
					<div class="large-3 column">
						<label>Principal diagnosis:</label>
						
					<div class="large-12 column">
						<input type="text" name="search" id="search" placeholder="Enter search query..." value="<?php echo strip_tags(@$_POST['search'])?>" />
					</div>
				</div>
				*/?>
			<?php $this->endWidget()?>
		</div>
	</div>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<form id="admin_pedigrees" method="post">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />

		<?php if (count($pedigrees) <1) {?>
			<div class="alert-box no_results">
				<span class="column_no_results">
					<?php if (@$_GET['search']) {?>
						No results found for current criteria.
					<?php }else{?>
						Please enter criteria to search pedigrees.
					<?php }?>
				</span>
			</div>
		<?php } else { ?>


			<table class="grid">
				<thead>
				<tr>
					<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
						<th><input type="checkbox" name="selectall" id="selectall" /></th>
					<?php } ?>
					<th><?php echo CHtml::link('ID',$this->getUri(array('sortby'=>'id')))?></th>
					<th><?php echo CHtml::link('Inheritance',$this->getUri(array('sortby'=>'inheritance')))?></th>
					<th><?php echo CHtml::link('Consanguinity',$this->getUri(array('sortby'=>'consanguinity')))?></th>
					<th><?php echo CHtml::link('Gene',$this->getUri(array('sortby'=>'gene')))?></th>
					<th><?php echo CHtml::link('Base change',$this->getUri(array('sortby'=>'base-change')))?></th>
					<th><?php echo CHtml::link('Amino acid change',$this->getUri(array('sortby'=>'amino-acid-change')))?></th>
					<th><?php echo CHtml::link('Disorder',$this->getUri(array('sortby'=>'disorder')))?></th>
					<th>Actions</th>
				</tr>
				</thead>


				<?php if (!empty($pedigrees)) {?>
					<tbody>
					<?php foreach ($pedigrees as $pedigree) {?>
						<tr>
							<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
								<td><input type="checkbox" name="pedigrees[]" value="<?php echo $pedigree->id?>" /></td>
							<?php } ?>
							<td><?php echo CHtml::link($pedigree->id,Yii::app()->createUrl('/Genetics/default/viewPedigree/'.$pedigree->id))?></td>
							<td>
								<?php if ($pedigree->inheritance) {
									//echo CHtml::link($pedigree->inheritance->name,Yii::app()->createUrl('/Genetics/default/editInheritance/'.$pedigree->inheritance->id));
									echo $pedigree->inheritance->name;
								}?>
							</td>
							<td><?php echo $pedigree->consanguinity ? 'Yes' : 'No'?>
							<td>
								<?php if ($pedigree->gene) {
									echo InternetLink::geneName($pedigree->gene->name);
									echo $pedigree->gene->name;
								}?>
							</td>
							<td><?php echo $pedigree->base_change?></td>
							<td><?php echo $pedigree->amino_acid_change?></td>
							<td><?php echo $pedigree->disorder ? $pedigree->disorder->term : ''?></td>
							<td>
								<?php echo CHtml::link('View',Yii::app()->createUrl('/Genetics/default/viewPedigree/'.$pedigree->id))?>
								<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
									<?php echo '&nbsp;'.CHtml::link('Edit',Yii::app()->createUrl('/Genetics/default/editPedigree/'.$pedigree->id))?>
								<?php } ?>
							</td>
						</tr>
					<?php }?>
					</tbody>
				<?php } ?>
				<tfoot class="pagination-container">
				<tr>

					<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
						<td colspan="3">
							<?php echo EventAction::button('Add', 'add', null, array('class' => 'small', 'id'=>'add_pedigree'))->toHtml()?>
							<?php echo EventAction::button('Delete', 'delete', null, array('class' => 'small'))->toHtml()?>
						</td>
					<?php } ?>
					<td colspan="6">
						<?php echo $this->renderPartial('_pagination',array(
							'pagination' => $pagination
						))?>
					</td>
				</tr>
				</tfoot>
			</table>
		<?php }?>
	</form>
</div>
