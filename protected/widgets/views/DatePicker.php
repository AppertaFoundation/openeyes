					<?php if (!@$htmlOptions['nowrapper']) {?>
						<div class="eventDetail"<?php if (@$htmlOptions['hidden']) {?> style="display: none;"<?php }?>>
							<?php unset($htmlOptions['hidden'])?>
							<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field))?>:</div>
							<div class="data">
								<?php }?>
								<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
									'name'=>$name,
									'id'=>get_class($element)."_".$field."_0",
									// additional javascript options for the date picker plugin
									'options'=>array_merge($options,array(
										'showAnim'=>'fold',
										'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
									)),
									'value' => (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$value) ? Helper::convertMySQL2NHS($value) : $value),
									'htmlOptions'=>$htmlOptions
								)); ?>
								<?php if (!@$htmlOptions['nowrapper']) {?>
							</div>
						</div>
					<?php }?>
