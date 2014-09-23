<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2013
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
<table class="generic-admin">
	<thead>
		<tr>
			<th>Order</th>
			<th>Name</th>
			<?php foreach ($extra_fields as $field) {?>
				<th>
					<?php echo CHtml::hiddenField('_extra_fields[]',$field['field'])?>
					<?php echo $model::model()->getAttributeLabel($field['field'])?>
				</th>
			<?php }?>
			<?php if ($model::model()->hasAttribute('active')) {?>
				<th>Active</th>
			<?php } else{?>
				<th>Actions</th>
			<?php }
			if ($model::model()->hasAttribute('default')) {?>
				<th>Default</th>
			<?php }?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($data as $i => $row) {?>
			<tr data-i="<?php echo $i?>">
				<td class="reorder">
					<span>&uarr;&darr;</span>
				</td>
				<td>
					<?php echo CHtml::hiddenField('id[]',$row->id)?>
					<?php echo CHtml::textField('name[]',$row->name)?>
					<?php if (isset($errors[$i])) {?>
						<span class="error">
							<?php echo $errors[$i]?>
						</span>
					<?php }?>
				</td>
				<?php foreach ($extra_fields as $field) {?>
					<td>
						<?php $this->render('_generic_admin_'.$field['type'],array('row' => $row, 'params' => $field))?>
					</td>
				<?php }?>
				<td>
					<?php if ($model::model()->hasAttribute('active')) {
						echo CHtml::checkBox('active[' . $i . ']',$row->active);
					} else{?>
						<a href="#" class="deleteRow">delete</a>
					<?php }?>
				</td>
				<?php if ($model::model()->hasAttribute('default')) {?>
					<td>
						<?php echo CHtml::radioButton('default',$row->default,array('value' => $i))?>
					</td>
				<?php }?>
			</tr>
		<?php }?>
		<tr class="newRow" style="display: none">
			<td>
				<span>&uarr;&darr;</span>
			</td>
			<td>
				<?php echo CHtml::hiddenField('id[]','',array('disabled' => 'disabled'))?>
				<?php echo CHtml::textField('name[]','',array('disabled' => 'disabled'))?>
			</td>
			<?php foreach ($extra_fields as $field) {?>
				<td>
					<?php $this->render('_generic_admin_'.$field['type'],array('row' => null, 'params' => $field, 'disabled' => 'disabled'))?>
				</td>
			<?php }?>
			<td>
				<a href="#" class="deleteRow">delete</a>
			</td>
			<?php if ($model::model()->hasAttribute('default')) {?>
				<td>
					<?php echo CHtml::radioButton('default',false)?>
				</td>
			<?php }?>
		</tr>
	</tbody>
	<?php if ($model::model()->hasAttribute('default')) {?>
		<tfoot>
			<tr>
				<td colspan="4" class="generic-admin-no-default">
					No default
				</td>
				<td>
					<?php echo CHtml::radioButton('default',!$has_default,array('value' => 'NONE'))?>
				</td>
			</tr>
		</tfoot>
	<?php }?>
</table>
<div>
	<?php echo EventAction::button('Add', 'admin-add', null, array('class' => 'generic-admin-add small secondary'))->toHtml()?>&nbsp;
	<?php echo EventAction::button('Save', 'admin-save', null, array('class' => 'generic-admin-save small primary'))->toHtml()?>&nbsp;
</div>
