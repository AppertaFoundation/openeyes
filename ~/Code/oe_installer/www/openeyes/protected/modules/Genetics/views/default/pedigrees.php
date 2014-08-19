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
											<th>Family ID:</th>
											<th>Causative gene:</th>
											<th>Consanguineous:</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<?php echo CHtml::textField('family-id', @$_GET['family-id'], array('placeholder' => 'Enter family ID here...'))?>
											</td>
											<td>
												<?php echo CHtml::dropDownList('gene-id',@$_GET['gene-id'],CHtml::listData(PedigreeGene::model()->findAll(array('order'=>'name asc')),'id','name'),array('empty' => '- All -'))?>
											</td>
											<td>
												<?php echo CHtml::dropDownList('consanguineous',@$_GET['consanguineous'],array(1 => 'Yes', 0 => 'No'),array('empty' => '- All -'))?>
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
		<table class="grid">
			<thead>
				<tr>
					<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
						<th><input type="checkbox" name="selectall" id="selectall" /></th>
					<?php } ?>
					<th>Inheritance</th>
					<th>Consanguinity</th>
					<th>Gene</th>
					<th>Base change</th>
					<th>Amino acid change</th>
					<th>Disorder</th>
					<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
					<th>Edit</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($pedigrees as $pedigree) {?>
					<tr>
						<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
							<td><input type="checkbox" name="pedigrees[]" value="<?php echo $pedigree->id?>" /></td>
						<?php } ?>
						<td>
							<?php if ($pedigree->inheritance) {
								//echo CHtml::link($pedigree->inheritance->name,Yii::app()->createUrl('/Genetics/default/editInheritance/'.$pedigree->inheritance->id));
								echo $pedigree->inheritance->name;
							}?>
						</td>
						<td><?php echo $pedigree->consanguinity ? 'Yes' : 'No'?>
						<td>
							<?php if ($pedigree->gene) {
								//echo CHtml::link($pedigree->gene->name,Yii::app()->createUrl('/Genetics/default/editGene/'.$pedigree->gene_id));
								echo $pedigree->gene->name;
							}?>
						</td>
						<td><?php echo $pedigree->base_change?></td>
						<td><?php echo $pedigree->amino_acid_change?></td>
						<td><?php echo $pedigree->disorder ? $pedigree->disorder->term : ''?></td>
						<?php if ($this->checkAccess('OprnEditPedigree')) { ?>
							<td><?php echo CHtml::link('Edit',Yii::app()->createUrl('/Genetics/default/editPedigree/'.$pedigree->id))?></td>
						<?php } ?>
					</tr>
				<?php }?>
			</tbody>
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
	</form>
</div>
