<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$html_options = array('autocomplete'=>Yii::app()->params['html_autocomplete']);
if (@$disabled) {
	$html_options['disabled'] = 'disabled';
}?>
<tr class="<?= @$row_class ?>" data-row="<?= $i ?>" style="<?= @$row_style ?>">
	<td class="reorder">
		<span>&uarr;&darr;</span>
	</td>
	<td>
		<?php
		echo CHtml::hiddenField("id[{$i}]",$row->id, $html_options);
		echo CHtml::hiddenField("display_order[{$i}]",$row->display_order ? $row->display_order : $i, $html_options);

		if ($label_field_type) {
			$this->render('application.widgets.views._generic_admin_' . $label_field_type, array(
					'row' => $row,
					'params' => array(
						'relation' => $label_relation,
						'field' => $label_field,
						'model' => $label_field_model,
						'allow_null' => false
					),
					'i' => $i));
		} else {
			echo CHtml::textField("{$label_field}[{$i}]",$row->{$label_field},$html_options);
		}?>
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
		} else {?>
			<a href="#" class="deleteRow">delete</a>
		<?php }?>
	</td>
	<?php if ($model::model()->hasAttribute('default')) {?>
		<td>
			<?php echo CHtml::radioButton('default',$row->default,array('value' => $i))?>
		</td>
	<?php }?>
</tr>
