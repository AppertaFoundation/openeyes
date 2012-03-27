<div id="<?php echo $field?>" class="eventDetail">
	<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:</div>
	<div class="data">
		<?php echo CHtml::textField($name, $value, $htmlOptions)?>
	</div>
</div>
