					<div class="eventDetail">
						<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
						<div class="data">
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>$name,
								'id'=>get_class($element)."_".$field."_0",
								// additional javascript options for the date picker plugin
								'options'=>array_merge($options,array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
								)),
								'value' => $value,
								'htmlOptions'=>$htmlOptions
							)); ?>
						</div>
					</div>
