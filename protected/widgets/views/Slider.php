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
<script type="text/javascript">
	var remap_<?php echo get_class($element)?>_<?php echo $field?> = {};
	<?php if (is_array($remap_values) && !empty($remap_values)) {
		foreach ($remap_values as $remap_value => $remap) {?>
			remap_<?php echo get_class($element)?>_<?php echo $field?>['<?php echo $remap_value?>'] = '<?php echo $remap?>';
		<?php }
	}?>
	var widgetSlider_<?php echo get_class($element)?>_<?php echo $field?> = new WidgetSlider({
		'prefix_positive': '<?php echo $prefix_positive?>',
		'range_id': '<?php echo get_class($element)?>_<?php echo $field?>',
		'force_dp': '<?php echo $force_dp?>',
		'remap': remap_<?php echo get_class($element)?>_<?php echo $field?>,
		'null': '<?php echo $null?>',
		'append': '<?php echo $append?>',
	});
</script>
<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
	<div class="label"><?php echo $element->getAttributeLabel($field)?>:</div>
	<div class="data">
		<span class="widgetSliderValue" id="<?php echo get_class($element)?>_<?php echo $field?>_value_span"><?php echo $value_display?><?php echo $append?></span>
		<input class="widgetSlider" type="range" id="<?php echo get_class($element)?>_<?php echo $field?>" name="<?php echo get_class($element)?>[<?php echo $field?>]" min="<?php echo $min?>" max="<?php echo $max?>" value="<?php echo $value?>" step="<?php echo $step?>" />
	</div>
</div>
