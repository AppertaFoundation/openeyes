<?php

// Add the invisible letter css
Yii::app()->clientScript->registerCssFile(
	'/css/elements/ElementLetterOut/1_invisible.css',
	'screen, projection'
);

Yii::app()->clientScript->registerCssFile(
	'/css/elements/ElementLetterOut/1_print.css',
	'print'
);

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
?>
<a class="fancybox shinybutton highlighted" onClick="javascript:window.print()" href="#"><span>Print Letter</span></a>
<div id="ElementLetterOut_layout">
<?php

Yii::app()->clientScript->registerCssFile(
        '/css/elements/ElementLetterOut/1.css',
        'screen, projection'
);

Yii::app()->clientScript->registerCssFile(
        '/css/elements/ElementLetterOut/1_print.css',
        'print'
);

if ($siteId = Yii::app()->request->cookies['site_id']->value) {
        $site = Site::model()->findByPk($siteId);

        if (isset($site)) {
?>
        <div class="ElementLetterOut_siteDetails">
                <?php

                echo $site->name . "<br />\n";
                echo $site->address1 . "<br />\n";
                if (isset($site->address2)) {
                        echo $site->address2 . "<br />\n";
                }
                if (isset($site->address3)) {
                        echo $site->address3 . "<br />\n";
                }
                echo $site->postcode . "<br />\n";
                echo "<br />\n";
                echo "Tel: " . $site->telephone . "<br />\n";
                echo "Fax: " . $site->fax . "<br />\n";
?>
        </div>
<?php
        }
}

?>
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <p class="ElementLetterOut_to">Patient Address</p>

        <p class="ElementLetterOut_date">Automatically populate date</p>

        <p class="ElementLetterOut_dear">Dear Patient</p>

        <p class="ElementLetterOut_re">Hosnum and nhsnum</p>

        <p class="ElementLetterOut_text">Text</p>

        <p>Yours sincerely,<br /><br /><br /><br />Admissions Officer</p>
</div>
<?php
	}
}?>
</div>
<div class="cleartall"></div>
<script type="text/javascript">
	$('a.fancybox').fancybox([]);
</script>

