					<div class="eventDetail" id="editDiagnosis">
						<div class="label">Diagnosis:</div>
						<div class="data">
				
							<div id="enteredDiagnosisText" class="eventHighlight big"<?php if (!$value){?> style="display: none;"<?php }?>>
								<h4><?php echo $value?></h4>
							</div>
							
							<h5 class="small"><em>Change diagnosis:</em></h5>

							<?php echo CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis', 'style' => 'width: 525px; margin-bottom:10px;'))?><br />							

							<?php
							$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
								'name'=>"{$class}[$field]",
								'id'=>"{$class}_{$field}_0",
								'value'=>'',
								'sourceUrl'=>array('disorder/autocomplete'),
								'options'=>array(
									'minLength'=>'3',
									'select'=>"js:function(event, ui) {
										var value = ui.item.value;
										$('#".$class."_".$field."_0').val('');
										$('#enteredDiagnosisText h4').html(value);
										$('#enteredDiagnosisText').show();
										$('input[id=savedDiagnosis]').val(value);
										$('#".$class."_".$field."').focus();
										return false;
									}",
								),
								'htmlOptions'=>array(
									'style'=>'width: 520px;',
									'placeholder' => 'or type the first few characters of a diagnosis'
								),
							));
							?>

							<input type="hidden" name="<?php echo $class?>[<?php echo $field?>]" id="savedDiagnosis" value="<?php echo $value?>" />
						</div>
					</div>
					<script type="text/javascript">
						$('#<?php echo $class?>_<?php echo $field?>').change(function() {
							$('#enteredDiagnosisText h4').html($('#<?php echo $class?>_<?php echo $field?> option:selected').text());
							$('#enteredDiagnosisText').show();
						});
					</script>
