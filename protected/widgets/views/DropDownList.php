<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail">
	<div class="label"><?php echo $element->getAttributeLabel($field)?>:</div>
	<div class="data">
		<?php echo CHtml::activeDropDownList($element,$field,$data,$htmlOptions)?>
	</div>
</div>
