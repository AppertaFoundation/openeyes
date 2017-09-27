<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>


<?php if (@$htmlOptions['nowrapper']) {?>

	<?php if (!$no_element) {?>
		<input type="hidden" value="" name="<?php echo CHtml::modelName($element)?>[<?php echo $field?>]">
	<?php }?>

	<?php foreach ($data as $id => $data_value) {?>
		<?php
            $options = array('value' => $id, 'id' => CHtml::modelName($element).'_'.$field.'_'.$id);

    if (@$htmlOptions['options'] && array_key_exists($id, @$htmlOptions['options'])) {
        foreach ($htmlOptions['options'][$id] as $k => $v) {
            $options[$k] = $v;
        }
            }?>
			<label class="inline highlight">
				<?php echo CHtml::radioButton($name, (!is_null($value) && $value == $id) && (!is_string($value) || $value != ''), $options); ?>
		 		<?php echo CHtml::encode($data_value)?>
	 		</label>
	<?php }?>

<?php } else {?>

    <?php $fieldset_class = isset($htmlOptions['fieldset-class']) ? $htmlOptions['fieldset-class'] : ''; ?>

	<fieldset id="<?php echo CHtml::modelName($element).'_'.$field?>" class="row field-row <?=$fieldset_class?> <?php echo $hidden ? 'hidden' : ''?>" >
		<?php	// Added hidden input below to enforce posting of current form element name.
				// When using radio or checkboxes if no value is selected then nothing is posted
				// not triggereing server side validation.
		?>
		<legend class="large-<?php echo $layoutColumns['label'];?> column">
			<?php if ($field_value) {?><?php echo CHtml::encode($element->getAttributeLabel($field_value)); ?>
			<?php }elseif (!$label_above) {?><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:<?php }?>
		</legend>
		<?php if (!$no_element) {?>
			<input type="hidden" value="" name="<?php echo CHtml::modelName($element)?>[<?php echo $field?>]">
		<?php }?>
		<div class="large-<?php echo $layoutColumns['field'];?> column end">
			<?php $i = 0; ?>
			<?php if ($label_above) {?>
				<label for="">
					<?php echo CHtml::encode($element->getAttributeLabel($field))?>
				</label>
			<?php }?>
			<?php foreach ($data as $id => $data_value) {?>
				<label class="inline highlight">
					<?php
                        $options = array('value' => $id, 'id' => CHtml::modelName($element).'_'.$field.'_'.$id);

    if (@$htmlOptions['options'] && array_key_exists($id, @$htmlOptions['options'])) {
        foreach ($htmlOptions['options'][$id] as $k => $v) {
            $options[$k] = $v;
        }
    }

    $class = isset($options['class']) ? ($options['class'] . " ") : '';
    $options['class'] = $class . str_replace(' ', '', $data_value);

    echo CHtml::radioButton($name, (!is_null($value) && $value == $id) && (!is_string($value) || $value != ''), $options);
    ?>
					<?php echo CHtml::encode($data_value)?>
				</label>
			<?php }?>
		</div>
	</fieldset>
<?php } ?>
