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
	<?php echo CHtml::activeDropDownList($element,$field,$data,$htmlOptions)?>
<?php }else{?>
	<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
		<?php if (@$htmlOptions['layout'] == 'vertical') {?>
			<div class="label"></div>
			<div class="DropDownLabelVertical">
				<?php echo $element->getAttributeLabel($field)?>
			</div>
			<div class="label"></div>
		<?php }else{?>
			<?php if (!@$htmlOptions['nolabel']){?><div class="label"><?php echo $element->getAttributeLabel($field)?>:</div><?php }?>
		<?php }?>
		<div class="data">
			<?php if (@$htmlOptions['divided']) {?>
				<select name="<?php echo get_class($element)?>[<?php echo $field?>]" id="<?php echo get_class($element)?>_<?php echo $field?>">
					<?php if (isset($htmlOptions['empty'])) {?>
						<option value=""><?php echo $htmlOptions['empty']?></option>
					<?php }?>
					<?php foreach ($data as $i => $optgroup) {?>
						<optgroup label="---------------">
							<?php foreach ($optgroup as $id => $option) {?>
								<option value="<?php echo $id?>"<?php if ($id == $value) {?> selected="selected"<?php }?>><?php echo $option?></option>
							<?php }?>
						</optgroup>
					<?php }?>
				</select>
			<?php }else{
				if (@$htmlOptions['textAttribute']) {
					$html_options = array();
					foreach ($data as $i => $item) {
						$html_options[(string)$i] = array($htmlOptions['textAttribute'] => $item);
					}
					$htmlOptions['options'] = $html_options;
				}
				echo CHtml::activeDropDownList($element,$field,$data,$htmlOptions)?>
			<?php }?>
		</div>
	</div>
<?php }?>
