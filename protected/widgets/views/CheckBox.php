					<div class="eventDetail"<?php if (isset($options['div_style'])) {?> style="<?php echo $options['div_style']?>"<?php }?>>
						<div class="label<?php if (isset($options['layout'])) {?>-<?php echo $options['layout']?><?php }?>"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
						<div class="datacol1">
							<?php echo CHtml::checkBox(get_class($element)."[$field]",$checked[$field])?>
						</div>
					</div>
