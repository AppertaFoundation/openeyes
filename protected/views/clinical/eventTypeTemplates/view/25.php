<?php

Yii::app()->clientScript->scriptMap['jquery.js'] = false;

foreach ($elements as $element) {
	if (get_class($element) == 'ElementOperation') {
		$operation = $element;
	}
}

if ($operation->status != $operation::STATUS_CANCELLED) {
	if ($editable) {
        	echo CHtml::link('<span>Edit Operation</span>',
                	array('clinical/update', 'id'=>$eventId), array('id'=>'editlink','class'=>'fancybox shinybutton', 'encode'=>false));
	}
} else {
?>
<div class="flash-notice">
<?php
        $cancellation = $operation->cancellation;
        // todo: move this text generation to a nicer place
        $text = "Operation Cancelled: By " . $cancellation->user->first_name .
                ' ' . $cancellation->user->last_name . ' on ' . date('F j, Y', strtotime($cancellation->cancelled_date));
        $text .= ' [' . $cancellation->cancelledReason->text . ']';

        echo $text; ?>
</div>
<?php
}

/**
 * Loop through all the element types completed for this event
 */
foreach ($elements as $element) {
	// Only display elements that have been completed, i.e. they have an event id
	if ($element->event_id) {
		$viewNumber = $element->viewNumber;

		echo $this->renderPartial(
			'/elements/' . get_class($element) . '/_view/' . $viewNumber,
			array('data' => $element)
		);
	}
}
?>
<div class="buttonlist">
<?php
if ($operation->status != $operation::STATUS_CANCELLED && $editable) {
        if (empty($operation->booking)) {
                echo CHtml::link('<span>Cancel Operation</span>',
                        array('booking/cancelOperation', 'operation'=>$operation->id), array('class'=>'fancybox shinybutton', 'encode'=>false));
                echo CHtml::link("<span>Schedule Now</span>",
                        array('booking/schedule', 'operation'=>$operation->id), array('class'=>'fancybox shinybutton highlighted', 'encode'=>false));
        } else {
                echo '<p/>';
                echo CHtml::link('<span>Cancel Operation</span>',
                        array('booking/cancelOperation', 'operation'=>$operation->id), array('class'=>'fancybox shinybutton', 'encode'=>false));
                echo CHtml::link("<span>Re-Schedule Later</span>",
                        array('booking/rescheduleLater', 'operation'=>$operation->id), array('class'=>'fancybox shinybutton', 'encode'=>false));
                echo CHtml::link('<span>Re-Schedule Now</span>',
                        array('booking/reschedule', 'operation'=>$operation->id), array('class'=>'fancybox shinybutton highlighted', 'encode'=>false));
        }
}?>
</div>
<script type="text/javascript">
        $('a.fancybox').fancybox([]);
</script>

