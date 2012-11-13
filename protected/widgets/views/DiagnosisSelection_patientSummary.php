<div class="eventDetail" id="editDiagnosis">
	<div class="data">
		<div id="<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText" class="eventHighlight big"
		<?php if (!$label){?> style="display: none;" <?php }?>>
			<h4>
				<?php echo $label?>
			</h4>
		</div>
		<?php echo CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis', 'style' => 'width: 525px; margin-bottom:10px;'))?>
		<br />
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => "{$class}[$field]",
				'id' => "{$class}_{$field}_0",
				'value'=>'',
				'source'=>"js:function(request, response) {
					".($loader ? "$('#".$loader."').show();" : "")."
					$.ajax({
						'url': '" . Yii::app()->createUrl('/disorder/autocomplete') . "',
						'type':'GET',
						'data':{'term': request.term, 'code': '".$code."'},
						'success':function(data) {
							".($loader ? "$('#".$loader."').hide();" : "")."
							data = $.parseJSON(data);
							response(data);
						}
					});
				}",
				'options' => array(
						'minLength'=>'3',
						'select' => "js:function(event, ui) {
							$('#".$class."_".$field."_0').val('');
							$('#".$class."_".$field."_enteredDiagnosisText h4').html(ui.item.value);
							$('#".$class."_".$field."_enteredDiagnosisText').show();
							$('input[id=".$class."_".$field."_savedDiagnosis]').val(ui.item.id);
							$('#".$class."_".$field."').focus();
							return false;
						}",
				),
				'htmlOptions' => array(
						'style'=>'width: 520px;',
						'placeholder' => 'or type the first few characters of a diagnosis',
				),
		));
		?>
		<input type="hidden" name="<?php echo $class?>[<?php echo $field?>]"
			id="<?php echo $class?>_<?php echo $field?>_savedDiagnosis" value="<?php echo $value?>" />
	</div>
</div>
<script type="text/javascript">
	$('#<?php echo $class?>_<?php echo $field?>').change(function() {
		$('#<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText h4').html($('option:selected', this).text());
		$('#<?php echo $class?>_<?php echo $field?>_savedDiagnosis').val($(this).val());
		$('#<?php echo $class?>_<?php echo $field?>_enteredDiagnosisText').show();
	});
</script>
