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
