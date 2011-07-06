<?php
$data = array();
foreach ($specialties as $specialty) {
	$data[$specialty->id] = $specialty->name;
} ?>
<table border="0">
<tr>
<td>
<div class="row">
	<label for="ElementOperation_value">Service:</label><br />
	<?php echo CHtml::listBox('service', '',
		$data, array('style' => 'width: 200px;')); ?>
</div>
</td>
<td>
<div class="row">
	<label for="ElementOperation_value">Subsection:</label><br />
	<?php echo CHtml::listBox('subsection', '', array(), array('style' => 'width: 150px;')); ?>
</div>
</td>
<td>
<div class="row">
	<label for="ElementOperation_value">Procedure:</label><br />
	<?php echo CHtml::listBox('procedure', '', array(), array('style' => 'width: 250px;')); ?>
</div>
</td>
</tr>
</table>
<div class="row">
	<?php echo CHtml::submitButton('Add procedure', array('id' => 'add_procedure')); ?>
</div>
<script type="text/javascript">
	$(function() {
		$('select[name=service]').change(function() {
			var select = $('select[name=service]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('procedure/subsection'); ?>',
				'type': 'GET',
				'data': {'service': select},
				'success': function(data) {
					$('select[name=subsection]').html(data);
					$('select[name=procedure]').html('');
				}
			});
		});
		$('select[name=subsection]').change(function() {
			var select = $('select[name=subsection]').val();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('procedure/list'); ?>',
				'type': 'GET',
				'data': {'subsection': select},
				'success': function(data) {
					$('select[name=procedure]').html(data);
				}
			});
		});
		$('#add_procedure').click(function() {
			var procedure = $('select[name=procedure] option:selected').text();
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('procedure/details'); ?>',
				'type': 'GET',
				'data': {'name': procedure},
				'success': function(data) {
					// append selection onto procedure list
					$('#procedure_list tbody').append(data);
					$('#procedure_list').show();
					
					// update total duration
					var totalDuration = 0;
					$('#procedure_list tbody').children().children('td:odd').each(function() {
						duration = Number($(this).text());
						totalDuration += duration;
					});
					var thisDuration = Number($('#procedure_list tbody').children().children(':last').text());
					var operationDuration = Number($('#ElementOperation_total_duration').val());
					$('#projected_duration').text(totalDuration);
					$('#ElementOperation_total_duration').val(operationDuration + thisDuration);
					
					// clear out text field
					$('#procedure_id').val('');
				}
			});
			return false;
		});
	});
</script>