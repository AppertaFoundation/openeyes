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

<?php if (!$get_row) {

	if ($filter_fields) { ?>
		<form method="get">
			<?php foreach ($filter_fields as $filter_field) { ?>
				<div class="row field-row">
					<div class="large-2 column"><label for="<?= $filter_field['field'] ?>"><?= CHtml::encode($model::model()->getAttributeLabel($filter_field['field'])); ?></label></div>
					<div class="large-5 column end"><?=
						CHtml::dropDownList(
							$filter_field['field'], $filter_field['value'],
							SelectionHelper::listData($filter_field['model']),
							array('empty' => '-- Select --', 'class' => 'generic-admin-filter')
						);
					?></div>
				</div>
			<?php } ?>
		</form>
	<?php }
	if ($filters_ready) { ?>
		<?= CHtml::beginForm() ?>
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
	<?php }
}
?>

<?php foreach ($items as $i => $row) {
	$this->render('_generic_admin_row', array('i' => $i, 'row' => $row, 'label_field' => $label_field, 'extra_fields' => $extra_fields, 'model' => $model));
}

if (!$get_row && $filters_ready) {
				if (!$this->new_row_url) {
					$this->render('_generic_admin_row', array('row_class' => 'newRow', 'row_style' => 'display: none;', 'disabled' => true,
							'i' => '{{key}}', 'row' => new $model, 'label_field' => $label_field, 'extra_fields' => $extra_fields, 'model' => $model));
				} ?>
			</tbody>
		</table>
		<div>
			<?php echo EventAction::button('Add', 'admin-add', null, array('class' => 'generic-admin-add small secondary', 'data-model' => $model, 'data-new-row-url' => @$this->new_row_url))->toHtml()?>&nbsp;
			<?php echo EventAction::button('Save', 'admin-save', null, array('class' => 'generic-admin-save small primary'))->toHtml()?>&nbsp;
		</div>
	<?= CHtml::endForm() ?>
<?php }
