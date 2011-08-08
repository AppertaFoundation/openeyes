<?php

Yii::app()->clientScript->scriptMap['jquery.js'] = false;

if ($editable) {
	echo CHtml::link('<span>Edit</span>',
		array('clinical/update', 'id'=>$eventId), array('id'=>'editlink','class'=>'fancybox shinybutton', 'encode'=>false));
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
<script type="text/javascript">
        $('a.fancybox').fancybox([]);
</script>

