					<div id="editDiagnosis" class="eventDetail">
						<div class="label"><?php echo CHtml::encode($this->model->getAttributeLabel($this->field))?>:</div>
						<div class="data">
							<?php echo CHtml::dropDownList("{$this->class}[{$this->field}]", '', $this->options, array('empty' => 'Select a commonly used diagnosis')); ?>
							<span style="display:block; margin-top:10px; margin-bottom:10px;"><strong>or</strong></span>
							<?php
							$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
								'name'=>"{$this->class}[$this->field]",
								'id'=>"{$this->class}_{$this->field}_0",
								'value'=>'',
								'sourceUrl'=>array('disorder/autocomplete'),
								'options'=>array(
									'minLength'=>'3',
									'select'=>"js:function(event, ui) {
										var value = ui.item.value;
										$('#".$this->class."_".$this->field."_0').val('');
										$('#enteredDiagnosisText span').html(value);
										$('input[id=savedDiagnosis]').val(value);
										$('#".$this->class."_".$this->field."').focus();
										return false;
									}",
								),
								'htmlOptions'=>array(
									'style'=>'width: 300px;'
								),
							));
							?>
							<input type="hidden" name="<?php echo $this->class?>[<?php echo $this->field?>]" id="savedDiagnosis" value="<?php echo @$_POST[$this->class][$this->field]?>" />
						</div>
						<div id="enteredDiagnosisText">
							<div class="extraDetails">
								<span class="bold" style="margin-right:20px;"><?php echo @$_POST[$this->class][$this->field]?></span>
							</div>
						</div>
					</div>
