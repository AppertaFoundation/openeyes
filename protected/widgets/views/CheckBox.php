<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail">
	<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
	<div class="datacol1">
		<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field])?>
	</div>
</div>
