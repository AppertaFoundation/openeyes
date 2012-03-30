<div id="<?php echo $field?>" class="eventDetail"<?php if (isset($htmlOptions['div_style'])) {?> style="<?php echo $htmlOptions['div_style']?>"<?php }?>>
	<div class="label<?php if (isset($htmlOptions['layout'])) {?>-<?php echo $htmlOptions['layout']?><?php }?>"><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:</div>
	<div class="data">
		<?php echo CHtml::textField($name, $value, $htmlOptions)?>
	</div>
</div>
