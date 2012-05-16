<?php echo CHtml::hiddenField(get_class($element)."[".$field."]", $value, array('id' => get_class($element).'_'.$field))?>
