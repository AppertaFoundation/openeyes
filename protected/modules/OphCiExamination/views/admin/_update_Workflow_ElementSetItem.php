<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="box admin">
	<div class="row field-row">
		<div class="column large-2">
			<?php echo CHtml::textField('step_name', $step->name, array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
		</div>
		<div class="column large-2 end">
			<?php echo EventAction::button('Save', 'save_step_name', null, array('class' => 'small'))->toHtml()?>
		</div>
	</div>
	<form id="admin_workflow_steps">
		<table class="grid">
			<thead>
				<tr>
					<th>Element type</th>
					<th>Hidden</th>
					<th>Mandatory</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
                foreach ($step->items as $i => $item) {
                    ?>
					<tr class="clickable" data-id="<?php echo $item->id?>">
						<td><?php echo $item->element_type->name?></td>
						<td><?php echo CHtml::activeCheckBox($item, 'is_hidden', array('class' => 'workflow-item-attr'))?></td>
						<td><?php echo CHtml::activeCheckBox($item, 'is_mandatory', array('class' => 'workflow-item-attr'))?></td>
						<td><a href="#" class="removeElementType" rel="<?php echo $item->id?>" data-element-type-id="<?php echo $item->element_type_id?>">Remove</a></td>
					</tr>
				<?php 
                }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="3">
						<div class="grid-view">
							<div class="row">
								<div class="large-3 column">
									<?php echo CHtml::dropDownList('element_type_id', '', CHtml::listData($element_types, 'id', 'name'), array('empty' => '- Select -'))?>
								</div>
								<div class="large-3 column end">
									<?php echo EventAction::button('Add element type', 'add_element_type', null, array('class' => 'small'))->toHtml()?>
								</div>
							</div>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
