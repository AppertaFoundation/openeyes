<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCSSFile('/css/theatre_calendar.css', 'all');
$patient = $operation->event->episode->patient; ?>
<div id="schedule">
<p><strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>
<div id="operation">
	<input type="hidden" id="booking" value="<?php echo $operation->booking->id; ?>" />
	<h1>Re-schedule operation</h1><br />
<?php
if (Yii::app()->user->hasFlash('info')) { ?>
<div class="flash-error">
    <?php echo Yii::app()->user->getFlash('info'); ?>
</div>
<?php 
} ?>
	<p><strong>Operation duration:</strong> <?php echo $operation->total_duration; ?> minutes</p>
	<p><strong>Current schedule:</strong></p>
<?php $this->renderPartial('_session', array('operation' => $operation)); ?><br />
</div>
</div>
<?php 
echo CHtml::form(array('booking/update'));
echo CHtml::hiddenField('booking_id', $operation->booking->id);

echo CHtml::label('Cancellation Reason: ', 'cancellation_reason');
if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
	$listIndex = 3;
} else {
	$listIndex = 2;
}
echo CHtml::dropDownList('cancellation_reason', '', 
	CancellationReason::getReasonsByListNumber($listIndex)
);
echo CHtml::submitButton('Cancel booking');
echo CHtml::endForm(); ?>