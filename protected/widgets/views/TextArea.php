					<div class="eventDetail">
						<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
						<div class="data">
							<textarea rows="4" cols="50" name="<?php echo get_class($element)?>[<?php echo $field?>]" id="<?php echo get_class($element)?>_<?php echo $field?>"><?php echo strip_tags($element->$field)?></textarea>
						</div>
					</div>
