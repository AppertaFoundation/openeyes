<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
<p><strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>
<div id="operation">
	<h1>Cancel operation</h1><br />
<?php
echo CHtml::form(array('booking/cancelOperation'), 'post', array('id' => 'cancelForm'));
echo CHtml::hiddenField('operation_id', $operation->id); ?>
<div class="errorSummary" style="display:none"></div>
<?php
echo CHtml::label('Cancellation Reason: ', 'cancellation_reason');
echo CHtml::dropDownList('cancellation_reason', '',
	CancellationReason::getReasonsByListNumber(1),
	array('empty'=>'Select a reason')
); ?>
<div class="buttonwrapper">
<button type="submit" value="submit" class="shinybutton highlighted"><span>Cancel operation</span></button>
</div><?php
echo CHtml::endForm(); ?>
</div>
</div>
<script type="text/javascript">
	$('#cancelForm button[type="submit"]').click(function () {
		if ('' == $('#cancellation_reason option:selected').val()) {
			$('div.errorSummary').html('Please select a cancellation reason');
			$('div.errorSummary').show();
			return false;
		}
	});
</script>