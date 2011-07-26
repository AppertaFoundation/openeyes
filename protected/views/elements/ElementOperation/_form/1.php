<strong>Book Operation</strong>

<div class="row">
	<label for="ElementOperation_value">Eye(s) to be operated on:</label>
	<?php echo CHtml::activeRadioButtonList($model, 'eye', $model->getEyeOptions(), 
		array('separator' => ' &nbsp; ')); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Add procedure:</label>
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'procedure_id',
    'sourceUrl'=>array('procedure/autocomplete'),
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
		}",
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;width:200px;'
    ),
));
?>
</div>
<div>
	<div>
		<table id="procedure_list" class="grid"<?php 
	if ($model->isNewRecord) { ?> style="display:none;"<?php 
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
				' ' . CHtml::link('remove', '#', array('onClick' => "js:removeProcedure(this);"));
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
					<td>Estimated Duration of Procedures:</td>
					<td id="projected_duration"><?php echo $totalDuration; ?></td>
				</tr>
				<tr>
					<td>Your Estimated Total:</td>
					<td><?php echo CHtml::activeTextField($model, 'total_duration'); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php
$this->widget('zii.widgets.jui.CJuiAccordion', array(
    'panels'=>array(
        'or browse procedures for all services'=>$this->renderPartial('/procedure/_selectLists',
			array('specialties' => $specialties),true),
    ),
	'id'=>'procedure_selects',
	'themeUrl'=>Yii::app()->baseUrl . '/css/jqueryui',
	'theme'=>'theme',
    // additional javascript options for the accordion plugin
    'options'=>array(
		'active'=>false,
        'animated'=>'bounceslide',
		'collapsible'=>true,
    ),
)); ?>
<div class="row">
	<label for="ElementOperation_value">Consultant required?</label>
	<?php echo CHtml::activeRadioButtonList($model, 'consultant_required', 
		$model->getConsultantOptions(), array('separator' => ' &nbsp; ')); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Anaesthetic type:</label>
	<?php echo CHtml::activeRadioButtonList($model, 'anaesthetic_type', 
		$model->getAnaestheticOptions(), array('separator' => ' &nbsp; ')); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Overnight Stay required?</label>
	<?php echo CHtml::activeRadioButtonList($model, 'overnight_stay', 
		$model->getOvernightOptions(), array('separator' => ' &nbsp; ')); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Add comments:</label><br />
	<?php echo CHtml::activeTextArea($model, 'comments', 
		array('rows' => 6, 'cols' => 60)); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Schedule Operation:</label><br />
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
			$model->getScheduleDelayOptions(), $options); ?>
</div>
<script type="text/javascript">
	$(function() {
		$("#procedure_list tbody").sortable({
			 cursor: 'move',
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
	});
	function removeProcedure(row) {
		var duration = $(row).parent().siblings('td').text();
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
	};
</script>