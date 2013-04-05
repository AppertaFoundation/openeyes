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
<?php if (@$htmlOptions['nowrapper']) {?>
	<?php if (!$no_element) {?>
		<input type="hidden" value="" name="<?php echo get_class($element)?>[<?php echo $field?>]">
	<?php }?>
		
	<?php foreach ($data as $id => $data_value) {?>
		<span class="group">
			<?php 
				$options = array('value' => $id, "id" => get_class($element). '_' . $field . '_' . $id);
				
				if (@$htmlOptions['options'] && array_key_exists($id, @$htmlOptions['options'])) {
					foreach ($htmlOptions['options'][$id] as $k => $v) {
						$options[$k] = $v;
					}
				}	
		 	echo CHtml::radioButton($name, (!is_null($value) && $value == $id) && (!is_string($value) || $value!=""), $options)?>
			<label for="<?php echo get_class($element)?>_<?php echo $field?>_<?php echo $id?>"><?php echo $data_value?></label>
		</span>
		<?php
		if ($maxwidth) {
			$i++;
			if ($i >= $maxwidth) {
				echo "<br />";
				$i=0;
			}
		}
		?>
	<?php }?>
<?php }else{?>
	<div id="<?php echo get_class($element). '_' . $field?>" class="eventDetail"<?php if ($hidden) {?> style="display: none;"<?php }?>>
		<?php	// Added hidden input below to enforce posting of current form element name. 
				// When using radio or checkboxes if no value is selected then nothing is posted
				// not triggereing server side validation.
		?>
		<?php if (!$no_element) {?>
			<input type="hidden" value="" name="<?php echo get_class($element)?>[<?php echo $field?>]">
		<?php }?>
		<div class="label"><?php if (!$label_above) {?><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:<?php }?></div>
		<div class="data">
			<?php $i=0; ?>
			<?php if ($label_above) {?>
				<div class="label">
					<?php echo CHtml::encode($element->getAttributeLabel($field))?>
				</div>
			<?php }?>
			<?php foreach ($data as $id => $data_value) {?>
				<span class="group">
					<?php 
						$options = array('value' => $id, "id" => get_class($element). '_' . $field . '_' . $id);
						
						if (@$htmlOptions['options'] && array_key_exists($id, @$htmlOptions['options'])) {
							foreach ($htmlOptions['options'][$id] as $k => $v) {
								$options[$k] = $v;
							}
						}
							 
						echo CHtml::radioButton($name, (!is_null($value) && $value == $id) && (!is_string($value) || $value!=""), $options);
					?>
					<label for="<?php echo get_class($element)?>_<?php echo $field?>_<?php echo $id?>"><?php echo $data_value?></label>
				</span>
				<?php
				if ($maxwidth) {
					$i++;
					if ($i >= $maxwidth) {
						echo "<br />";
						$i=0;
					}
				}
				?>
			<?php }?>
		</div>
	</div>
<?php }?>