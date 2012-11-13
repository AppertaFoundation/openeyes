<div class="eventDetail" id="editDiagnosis">
	<div class="data">
		<?php echo CHtml::dropDownList("{$class}[$field]", '', $options, array('empty' => 'Select a commonly used diagnosis', 'style' => 'width: 525px; margin-bottom:10px;'))?>
		<br />
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => "{$class}[$field]",
				'id' => "{$class}_{$field}_0",
				'value'=>'',
				'source'=>"js:function(request, response) {
					$.ajax({
						'url': '" . Yii::app()->createUrl('/disorder/autocomplete') . "',
						'type':'GET',
						'data':{'term': request.term, 'code': '".$code."'},
						'success':function(data) {
							data = $.parseJSON(data);

							var result = [];

							for (var i = 0; i < data.length; i++) {
								var ok = true;
								$('#selected_diagnoses').children('input').map(function() {
									if ($(this).val() == data[i]['id']) {
										ok = false;
									}
								});
								if (ok) {
									result.push(data[i]);
								}
							}

							response(result);
						}
					});
				}",
				'options' => array(
						'minLength'=>'3',
						'select' => "js:function(event, ui) {
							".($callback ? $callback."(ui.item.id, ui.item.value);" : '')."
							$('#".$class."_".$field."_0').val('');
							$('#".$class."_".$field."').children('option').map(function() {
								if ($(this).val() == ui.item.id) {
									$(this).remove();
								}
							});
							return false;
						}",
				),
				'htmlOptions' => array(
						'style'=>'width: 520px;',
						'placeholder' => 'or type the first few characters of a diagnosis',
				),
		));
		?>
	</div>
</div>
<script type="text/javascript">
	<?php if ($callback) {?>
		$('#<?php echo $class?>_<?php echo $field?>').change(function() {
			<?php echo $callback?>($(this).children('option:selected').val(), $(this).children('option:selected').text());
			$(this).children('option:selected').remove();
			$('#<?php echo $class?>_<?php echo $field?>').val('');
		});
	<?php }?>
</script>
