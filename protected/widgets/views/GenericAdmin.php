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
			<th><?= $model::model()->getAttributeLabel($label_field) ?></th>
			<?php foreach ($extra_fields as $field) {?>
				<th>
					<?php echo $model::model()->getAttributeLabel($field['field'])?>
				</th>
			<?php }?>
			<?php
			$attributes = $model::model()->getAttributes();
			if (array_key_exists('active',$attributes)) {?>
			<th>Active</th>
			<?php } else{?>
			<th>Actions</th>
			<?php }?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($items as $i => $row) {?>
			<tr data-row="<?= $i ?>">
				<td class="reorder">
					<span>&uarr;&darr;</span>
				</td>
				<td>
					<?php echo CHtml::hiddenField("id[{$i}]",$row->id)?>
					<?php echo CHtml::hiddenField("display_order[{$i}]",$row->display_order)?>
					<?php echo CHtml::textField("{$label_field}[{$i}]",$row->{$label_field},array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
					<?php if (isset($errors[$i])) {?>
						<span class="error">
							<?php echo $errors[$i]?>
						</span>
					<?php }?>
				</td>
				<?php foreach ($extra_fields as $field) {?>
					<td>
						<?php $this->render('_generic_admin_'.$field['type'],array('row' => $row, 'params' => $field, 'i' => $i))?>
					</td>
				<?php }?>
				<td>
					<?php if (isset($row->active)) {
						echo CHtml::checkBox('active[' . $i . ']',$row->active);
					}
					else{?>
					<a href="#" class="deleteRow">delete</a>
					<?php }?>
				</td>
			</tr>
		<?php }?>
		<tr id="admin-new-row" class="newRow" style="display: none">
			<input type="hidden" name="row-key" value="{{key}}" />
			<td>
				<span>&uarr;&darr;</span>
			</td>
			<td>
				<?php echo CHtml::hiddenField('id[{{key}}]','',array('disabled' => 'disabled'))?>
				<?php echo CHtml::hiddenField('display_order[{{key}}]','{{key}}',array('disabled' => 'disabled'))?>
				<?php echo CHtml::textField("{$label_field}[{{key}}]",'',array('autocomplete' => Yii::app()->params['html_autocomplete'], 'disabled' => 'disabled'))?>
			</td>
			<?php foreach ($extra_fields as $field) {?>
				<td>
					<?php $this->render('_generic_admin_'.$field['type'],array('row' => null, 'params' => $field, 'disabled' => 'disabled', 'i' => '{{key}}'))?>
				</td>
			<?php }?>
			<td>
				<a href="#" class="deleteRow">delete</a>
			</td>
		</tr>
	</tbody>
</table>
<div>
	<?php echo EventAction::button('Add', 'admin-add', null, array('class' => 'generic-admin-add small secondary'))->toHtml()?>&nbsp;
	<?php echo EventAction::button('Save', 'admin-save', null, array('class' => 'generic-admin-save small primary'))->toHtml()?>&nbsp;
</div>
