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
		<div class="large-4 column">
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm',array(
				'id' => 'searchform',
				'enableAjaxValidation' => false,
				'focus' => '#search',
				'action' => Yii::app()->createUrl('/Genetics/default/pedigrees'),
			))?>
				<div class="row">
					<div class="large-12 column">
						<input type="text" name="search" id="search" placeholder="Enter search query..." value="<?php echo strip_tags(@$_POST['search'])?>" />
					</div>
				</div>
			<?php $this->endWidget()?>
		</div>
	</div>
	<?php echo $this->renderPartial('//elements/form_errors',array('errors'=>$errors))?>
	<form id="admin_pedigrees" method="post">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" name="selectall" id="selectall" /></th>
					<th>ID</th>
					<th>Inheritance</th>
					<th>Consanguinity</th>
					<th>Gene</th>
					<th>Base change</th>
					<th>Amino acid change</th>
					<th>Disorder</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($pedigrees as $pedigree) {?>
					<tr>
						<td><input type="checkbox" name="pedigrees[]" value="<?php echo $pedigree->id?>" /></td>
						<td><?php echo CHtml::link($pedigree->id,Yii::app()->createUrl('/Genetics/default/editPedigree/'.$pedigree->id))?></td>
						<td>
							<?php if ($pedigree->inheritance) {
								echo CHtml::link($pedigree->inheritance->name,Yii::app()->createUrl('/Genetics/default/editInheritance/'.$pedigree->inheritance->id));
							}?>
						</td>
						<td><?php echo $pedigree->consanguinity ? 'Yes' : 'No'?>
						<td>
							<?php if ($pedigree->gene) {
								echo CHtml::link($pedigree->gene->name,Yii::app()->createUrl('/Genetics/default/editGene/'.$pedigree->gene_id));
							}?>
						</td>
						<td><?php echo $pedigree->base_change?></td>
						<td><?php echo $pedigree->amino_acid_change?></td>
						<td><?php echo $pedigree->disorder ? $pedigree->disorder->term : ''?></td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="3">
						<?php echo EventAction::button('Add', 'add', null, array('class' => 'small'))->toHtml()?>
						<?php echo EventAction::button('Delete', 'delete', null, array('class' => 'small'))->toHtml()?>
					</td>
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
