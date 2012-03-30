<div id="<?php echo get_class($element); ?>" class="eventDetail"<?php if (isset($htmlOptions['div_style'])) {?> style="<?php echo $htmlOptions['div_style']?>"<?php }?>>
	<div class="label<?php if (isset($htmlOptions['layout'])) {?>-<?php echo $htmlOptions['layout']?><?php }?>"><?php echo $element->getAttributeLabel($field); ?>:</div>
	<div class="data">
		<?php echo $listHTML; ?>
	</div>
</div>
