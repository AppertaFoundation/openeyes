<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
<p><strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>
<div id="operation">
	<h1>Cancel operation</h1><br />
<?php 
echo CHtml::form(array('booking/cancelOperation'));
echo CHtml::hiddenField('operation_id', $operation->id);

echo CHtml::label('Cancellation Reason: ', 'cancellation_reason');
echo CHtml::dropDownList('cancellation_reason', '', 
	CancellationReason::getReasonsByListNumber(1)
);
echo CHtml::submitButton('Cancel operation');
echo CHtml::endForm(); ?>
</div>
</div>
