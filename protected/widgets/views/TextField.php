<div id="<?php echo $field?>" class="eventDetail">
	<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:</div>
	<div class="data">
		<?php echo CHtml::textField($name, $value, $htmlOptions)?>
		<label for="<?php echo get_class($element)?>[<?php echo $field?>]"><?php echo $value?></label>
	</div>
</div>
