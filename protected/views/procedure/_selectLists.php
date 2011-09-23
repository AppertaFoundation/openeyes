<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

$data = array();
foreach ($specialties as $specialty) {
	$data[$specialty->id] = $specialty->name;
} ?>
<table border="0">
<tr>
<td>
<div class="row">
	<label for="ElementOperation_value">Service:</label><br />
	<?php echo CHtml::listBox('service', '', $data); ?>
</div>
</td>
<td>
<div class="row">
	<label for="ElementOperation_value">Subsection:</label><br />
	<?php echo CHtml::listBox('subsection', '', array()); ?>
</div>
</td>
<td>
<div class="row">
	<label for="ElementOperation_value">Procedure:</label><br />
	<?php echo CHtml::listBox('procedure', '', array()); ?>
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
			var existingProcedures = [];
			$('#procedure_list tbody').children().each(function () {
				var text = $(this).children('td:first').text();
				existingProcedures.push(text.replace(/ remove$/i, ''));
			});
			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('procedure/list'); ?>',
				'type': 'POST',
				'data': {'subsection': select, 'existing': existingProcedures},
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
					// remove selection from the filter box
					$('select[name=procedure] option:selected').remove();

					// append selection onto procedure list
					$('#procedure_list tbody').append(data);
					$('#procedureDiv').show();
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