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

Yii::app()->clientScript->scriptMap['jquery.js'] = false; ?>
<div class="heading">
<span class="emphasize">Book Operation:</span> Operation details
</div>
<div class="box_grey rounded-corners">
	<div class="label">Select eye(s):</div>
	<div class="data"><?php echo CHtml::activeRadioButtonList($model, 'eye', $model->getEyeOptions(),
		array('separator' => ' &nbsp; ')); ?>
	</div>
	<div class="cleartall"></div>
	<div class="label">Add procedure:</div>
<?php
	if (!empty($subsections) || !empty($procedures)) { ?>
	<div class="data"><?php
		if (!empty($subsections)) {
			echo CHtml::dropDownList('subsection_id', '', $subsections,
				array('empty' => 'Select a subsection'));
			echo CHtml::dropDownList('select_procedure_id', '', array(),
				array('empty' => 'Select a commonly used procedure', 'style' => 'display: none;'));
		} else {
			echo CHtml::dropDownList('select_procedure_id', '', $procedures,
				array('empty' => 'Select a commonly used procedure'));
 		} ?> &nbsp; <strong>or</strong></div>
<?php
	} ?>
	<div class="data"><span></span><?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
	'name'=>'procedure_id',
	'id'=>'autocomplete_procedure_id',
	'source'=>"js:function(request, response) {
		var existingProcedures = [];
		$('#procedure_list tbody').children().each(function () {
			var text = $(this).children('td:first').text();
			existingProcedures.push(text.replace(/ remove$/i, ''));
		});

		$.ajax({
			'url': '" . Yii::app()->createUrl('procedure/autocomplete') . "',
			'type':'GET',
			'data':{'term': request.term},
			'success':function(data) {
				data = $.parseJSON(data);

				var result = [];

				for (var i = 0; i < data.length; i++) {
					var index = $.inArray(data[i], existingProcedures);
					if (index == -1) {
						result.push(data[i]);
					}
				}

				response(result);
			}
		});
	}",
	'options'=>array(
		'minLength'=>'2',
		'select'=>"js:function(event, ui) {
			$.ajax({
				'url': '" . Yii::app()->createUrl('procedure/details') . "',
				'type': 'GET',
				'data': {'name': ui.item.value},
				'success': function(data) {
					// append selection onto procedure list
					$('#procedure_list tbody').append(data);
					$('#procedureDiv').show();
					$('#procedure_list').show();

					// update total duration
					var totalDuration = 0;
					$('#procedure_list tbody').children().children('td:odd').each(function() {
						duration = Number($(this).text());
						if ($('input[name=\"ElementOperation[eye]\"]:checked').val() == " . ElementOperation::EYE_BOTH . ") {
							duration = duration * 2;
						}
						totalDuration += duration;
					});
					var thisDuration = Number($('#procedure_list tbody').children().children(':last').text());
					if ($('input[name=\"ElementOperation[eye]\"]:checked').val() == " . ElementOperation::EYE_BOTH . ") {
						thisDuration = thisDuration * 2;
					}
					var operationDuration = Number($('#ElementOperation_total_duration').val());
					$('#projected_duration').text(totalDuration);
					$('#ElementOperation_total_duration').val(operationDuration + thisDuration);

					// clear out text field
					$('#autocomplete_procedure_id').val('');

					// remove selection from the filter box
					if ($('select[name=procedure]').children().length > 0) {
						var name = $('#procedure_list tbody').children().children(\":nth-child(2)\").text().replace(/ remove$/i, '');
						$('select[name=procedure] option').each(function () {
							if ($(this).text() == name) {
								$(this).remove();
							}
						});
					}
				}
			});
		}",
	),
	'htmlOptions'=>array('style'=>'width: 400px;')
)); ?></div>
	<div class="cleartall"></div>
	<div id="procedureDiv"<?php
	if ($newRecord) { ?> style="display:none;"<?php
	} ?>>
		<table id="procedure_list" class="grid"<?php
	if ($newRecord) { ?> style="display:none;"<?php
	} ?> title="Procedure List">
			<thead>
				<tr>
					<th>Procedures Added</th>
					<th>Duration</th>
				</tr>
			</thead>
			<tbody>
<?php
	$totalDuration = 0;
	if (!empty($model->procedures)) {
		foreach ($model->procedures as $procedure) {
			$display = $procedure['term'] . ' - ' . $procedure['short_format'] .
				' ' . CHtml::link('remove', '#',
				array('onClick' => "js:return removeProcedure(this);", 'class'=>'removeLink'));
			$totalDuration += $procedure['default_duration']; ?>
				<tr>
					<?php echo CHtml::hiddenField('Procedures[]', $procedure['id']); ?>
					<td><?php echo $display; ?></td>
					<td><?php echo $procedure['default_duration']; ?></td>
				</tr>
<?php	}
	} ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="topPadded">Calculated Total Duration:</td>
					<td id="projected_duration"><?php echo $totalDuration; ?></td>
				</tr>
				<tr>
					<td>Estimated Total Duration:</td>
					<td><span></span><?php echo CHtml::activeTextField($model, 'total_duration', array('style'=>'width: 40px;')); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="cleartall"></div>
	<div class="label">Consultant required?</div>
	<div class="data"><?php echo CHtml::activeRadioButtonList($model, 'consultant_required',
		$model->getConsultantOptions(), array('separator' => ' &nbsp; ')); ?></div>
	<div class="cleartall"></div>
	<div class="label">Anaesthetic type:</div>
	<div class="data"><?php
		$i = 0;
		foreach ($model->getAnaestheticOptions() as $id => $value) { ?>
		<input id="ElementOperation_anaesthetic_type_<?php echo $i; ?>"<?php
			if ($model->anaesthetic_type == $id) {
				echo 'checked="checked"';
			} ?>value="<?php echo $id; ?>" type="radio" name="ElementOperation[anaesthetic_type]">
		<label for="ElementOperation_anaesthetic_type_<?php echo $i; ?>"><?php echo $value; ?></label>
	<?php
		}
	?></div>
	<div class="cleartall"></div>
	<div class="label">Overnight Stay required?</div>
	<div class="data"><?php echo CHtml::activeRadioButtonList($model, 'overnight_stay',
		$model->getOvernightOptions(), array('separator' => ' ')); ?></div>
	<div class="cleartall"></div>
	<div class="label">Decision Date:</div>
	<div class="data"><span></span><?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name'=>'ElementOperation[decision_date]',
			'id'=>'ElementOperation_decision_date_0',
			// additional javascript options for the date picker plugin
			'options'=>array(
				'showAnim'=>'fold',
				'dateFormat'=>'yy-mm-dd',
				'maxDate'=>'today'
			),
			'value' => $model->decision_date,
			'htmlOptions'=>array('style'=>'width: 110px;')
		)); ?></div>
	<div class="cleartall"></div>
	<div class="label">Add comments:</div>
	<div class="data"><?php echo CHtml::activeTextArea($model, 'comments'); ?></div>
</div>
<div class="box_grey_big_gradient_top"></div>
<div class="box_grey_big_gradient_bottom">
	<div class="label">Schedule Operation:</div>
	<div class="data">
	<?php
	$timeframe1 = $model->schedule_timeframe == ElementOperation::SCHEDULE_IMMEDIATELY ? 0 : 1;
	if ($model->schedule_timeframe != ElementOperation::SCHEDULE_IMMEDIATELY) {
		$timeframe2 = $model->schedule_timeframe;
		$options = array();
	} else {
		$timeframe2 = 0;
		$options = array('disabled' => true);
	}
	echo CHtml::radioButtonList('schedule_timeframe1', $timeframe1,
		$model->getScheduleOptions(), array('separator' => '<br />'));
	echo CHtml::dropDownList('schedule_timeframe2', $timeframe2,
			$model->getScheduleDelayOptions(), $options); ?></div>
</div>
<script type="text/javascript">
	$(function() {
		$('input[id=autocomplete_procedure_id]').watermark('type the first few characters of a procedure');
		$("#ElementOperation_decision_date_0").val('<?php
			echo (empty($model->decision_date) || $model->decision_date == '0000-00-00')
				? date('Y-m-d') : $model->decision_date; ?>');
		$("#procedure_list tbody").sortable({
			 helper: function(e, tr)
			 {
				 var $originals = tr.children();
				 var $helper = tr.clone();
				 $helper.children().each(function(index)
				 {
					 // Set helper cell sizes to match the original sizes
					 $(this).width($originals.eq(index).width())
				 });
				 return $helper;
			 }
		}).disableSelection();
		$('input[name=schedule_timeframe1]').change(function() {
			var select = $('input[name=schedule_timeframe1]:checked').val();

			if (select == 1) {
				$('select[name=schedule_timeframe2]').attr('disabled', false);
			} else {
				$('select[name=schedule_timeframe2]').attr('disabled', true);
			}
		});

		$('select[id=subsection_id]').change(function() {
			var subsection = $('select[name=subsection_id] option:selected').val();
			if (subsection != 'Select a subsection') {
				var existingProcedures = [];
				$('#procedure_list tbody').children().each(function () {
					var text = $(this).children('td:first').text();
					existingProcedures.push(text.replace(/ remove$/i, ''));
				});
				$.ajax({
					'url': '<?php echo Yii::app()->createUrl('procedure/list'); ?>',
					'type': 'POST',
					'data': {'subsection': subsection, 'existing': existingProcedures},
					'success': function(data) {
						$('select[name=select_procedure_id]').attr('disabled', false);
						$('select[name=select_procedure_id]').html(data);
						$('select[name=select_procedure_id]').show();
					}
				});
			}
		});

		$('#select_procedure_id').change(function() {
			var procedure = $('select[name=select_procedure_id] option:selected').text();
			if (procedure != 'Select a commonly used procedure') {
				$.ajax({
					'url': '<?php echo Yii::app()->createUrl('procedure/details'); ?>',
					'type': 'GET',
					'data': {'name': procedure},
					'success': function(data) {
						// append selection onto procedure list
						$('#procedure_list tbody').append(data);
						$('#procedureDiv').show();
						$('#procedure_list').show();

						// update total duration
						var totalDuration = 0;
						$('#procedure_list tbody').children().children('td:odd').each(function() {
							duration = Number($(this).text());
							if ($('input[name="ElementOperation[eye]"]:checked').val() == <?php echo ElementOperation::EYE_BOTH; ?>) {
								duration = duration * 2;
							}
							totalDuration += duration;
						});
						var thisDuration = Number($('#procedure_list tbody').children().children(':last').text());
						if ($('input[name="ElementOperation[eye]"]:checked').val() == <?php echo ElementOperation::EYE_BOTH; ?>) {
							thisDuration = thisDuration * 2;
						}
						var operationDuration = Number($('#ElementOperation_total_duration').val());
						$('#projected_duration').text(totalDuration);
						$('#ElementOperation_total_duration').val(operationDuration + thisDuration);

						// clear out text field
						$('#autocomplete_procedure_id').val('');

						// remove the procedure from the options list
						$('select[name=select_procedure_id] option:selected').remove();

						// disable the dropdown if there are no items left to select
						if ($('select[name=select_procedure_id] option').length == 1) {
							$('select[name=select_procedure_id]').attr('disabled', true);
						}
					}
				});
			}
			return false;
		});
	});
	function removeProcedure(row) {
		var duration = $(row).parent().siblings('td').text();
		if ($('input[name="ElementOperation[eye]"]:checked').val() == <?php echo ElementOperation::EYE_BOTH; ?>) {
			duration = duration * 2;
		}
		var projectedDuration = Number($('#projected_duration').text()) - duration;
		var totalDuration = Number($('#ElementOperation_total_duration').val()) - duration;

		if (projectedDuration < 0) {
			projectedDuration = 0;
		}
		if (totalDuration < 0) {
			totalDuration = 0;
		}
		$('#projected_duration').text(projectedDuration);
		$('#ElementOperation_total_duration').val(totalDuration);

		$(row).parents('tr').remove();

		return false;
	};
	$('input[name="ElementOperation[eye]"]').click(function() {
		if ($('input[name="Procedures[]"]').length == 0) {
			$('input[id="autocomplete_procedure_id"]').focus();
		}
	});
</script>
