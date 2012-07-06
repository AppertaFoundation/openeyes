<script type="text/javascript">
	var widgetSliderTable_<?php echo get_class($element)?>_<?php echo $field?> = new WidgetSliderTable({
		'range_id': '<?php echo get_class($element)?>_<?php echo $field?>',
		'data': {<?php foreach ($data as $i => $val) { if ($i >1) echo ','; echo "'$i': '$val'"; }?>}
	});
</script>
<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
	<div class="label"><?php echo $element->getAttributeLabel($field)?>:</div>
	<div class="data">
		<span class="widgetSliderValue" id="<?php echo get_class($element)?>_<?php echo $field?>_value_span"><?php echo $data[$value]?></span>
		<input class="widgetSlider" type="range" id="<?php echo get_class($element)?>_<?php echo $field?>" name="<?php echo get_class($element)?>[<?php echo $field?>]" min="<?php echo $min?>" max="<?php echo $max?>" value="<?php echo $value?>" step="<?php echo $step?>" />
	</div>
</div>
