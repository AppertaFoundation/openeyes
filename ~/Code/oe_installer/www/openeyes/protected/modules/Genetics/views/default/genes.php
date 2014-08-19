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
			<h2>Genes</h2>
		</div>
		<div class="large-4 column">
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm',array(
				'id' => 'searchform',
				'enableAjaxValidation' => false,
				'focus' => '#search',
				'action' => Yii::app()->createUrl('/Genetics/default/genes'),
			))?>
				<div class="row">
					<div class="large-12 column">
						<input type="text" name="search" id="search" placeholder="Enter search query..." value="<?php echo strip_tags(@$_POST['search'])?>" />
					</div>
				</div>
			<?php $this->endWidget()?>
		</div>
	</div>
	<?php echo $this->renderPartial('_form_errors',array('errors'=>$errors))?>
	<form id="admin_genes" method="post">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<table class="grid">
			<thead>
				<tr>
					<?php if ($this->checkAccess('OprnEditGene')) { ?>
						<th><input type="checkbox" name="selectall" id="selectall" /></th>
					<?php } ?>
					<th>Name</th>
					<th>Location</th>
					<?php if ($this->checkAccess('OprnEditGene')) { ?>
						<th>Edit</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($genes as $gene) {?>
					<tr>
						<?php if ($this->checkAccess('OprnEditGene')) { ?>
							<td><input type="checkbox" name="genes[]" value="<?php echo $gene->id?>" /></td>
						<?php } ?>
						<td><?php echo $gene->name?></td>
						<td><?php echo $gene->location?></td>
						<?php if ($this->checkAccess('OprnEditGene')) { ?>
							<td><?php echo CHtml::link('Edit',Yii::app()->createUrl('/Genetics/default/editGene/'.$gene->id))?></td>
						<?php } ?>
					</tr>
				<?php }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<?php if ($this->checkAccess('OprnEditGene')) { ?>
						<td colspan="3">
							<?php echo EventAction::button('Add', 'add', null, array('class' => 'small', 'id'=>'add_gene'))->toHtml()?>
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
