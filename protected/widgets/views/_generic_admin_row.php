<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$html_options = array('autocomplete' => Yii::app()->params['html_autocomplete']);
if (@$disabled) {
    $html_options['disabled'] = 'disabled';
}?>
<tr class="<?= @$row_class ?>" data-row="<?= $i ?>" style="<?= @$row_style ?>">
	<?php
    echo CHtml::hiddenField("id[{$i}]", $row->id, $html_options);
    if($display_order){?>
	<td class="reorder">
		<span>&uarr;&darr;</span>
		<?php
            echo CHtml::hiddenField("display_order[{$i}]", $row->display_order ? $row->display_order : $i, $html_options);
        ?>
	</td>
	<?php } ?>
	<?php if (!$label_extra_field):?>
	<td>
		<?php
            if ($label_field_type) {
                $this->render('application.widgets.views._generic_admin_'.$label_field_type, array(
                    'row' => $row,
                    'params' => array(
                        'relation' => $label_relation,
                        'field' => $label_field,
                        'model' => $label_field_model,
                        'allow_null' => false,
                    ),
                    'i' => $i,
                ));
            } else {
                echo CHtml::textField("{$label_field}[{$i}]", $row->{$label_field}, $html_options);
            }?>
			<?php if (isset($errors[$i])) { ?>
				<span class="error">
				<?php echo $errors[$i] ?>
				</span>
			<?php }?>
	</td>
	<?php endif;?>
	<?php foreach ($extra_fields as $field) {?>
		<td>
			<?php $this->render('_generic_admin_'.$field['type'], array('row' => $row, 'params' => $field, 'i' => $i))?>
		</td>
	<?php }?>
	<td>
		<?php if (isset($row->active)) {
    echo CHtml::checkBox('active['.$i.']', $row->active);
        } elseif (!$this->cannot_delete) {?>

			<a href="#" class="deleteRow">delete</a>
		<?php }?>
	</td>
	<?php if ($model::model()->hasAttribute('default')) {?>
		<td>
			<?php echo CHtml::radioButton('default', $row->default, array('value' => $i))?>
		</td>
	<?php }?>
</tr>
