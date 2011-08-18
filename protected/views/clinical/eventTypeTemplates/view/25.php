<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false;

foreach ($elements as $element) {
	if (get_class($element) == 'ElementOperation') {
		$operation = $element;
		break;
	}
}

if (Yii::app()->user->hasFlash('success')) { ?>
<div class="flash-success">
    <?php echo Yii::app()->user->getFlash('success'); ?>
</div>
<?php
}

if ($operation->status != $operation::STATUS_CANCELLED) {
	if ($editable) {
		echo CHtml::link('<span>Edit Operation</span>', array('clinical/update', 'id' => $eventId), array('id' => 'editlink', 'class' => 'fancybox shinybutton', 'encode' => false));
	}
} else { ?>
<div class="flash-notice">
<?php
	echo $operation->getCancellationText(); ?>
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
			'/elements/' . get_class($element) . '/_view/' . $viewNumber, array('data' => $element)
		);
	}
}
?>
<div class="buttonlist">
<?php
if ($operation->status != $operation::STATUS_CANCELLED && $editable) {
	if (empty($operation->booking)) {
                echo CHtml::link('<span>Cancel Operation</span>', array('booking/cancelOperation', 'operation' => $operation->id), array('class' => 'fancybox shinybutton', 'encode' => false));
                echo CHtml::link("<span>Schedule Now</span>", array('booking/schedule', 'operation' => $operation->id), array('class' => 'fancybox shinybutton highlighted', 'encode' => false));
        } else {
                echo '<p/>';
                echo CHtml::link('<span>Cancel Operation</span>', array('booking/cancelOperation', 'operation' => $operation->id), array('class' => 'fancybox shinybutton', 'encode' => false));
                echo CHtml::link("<span>Re-Schedule Later</span>", array('booking/rescheduleLater', 'operation' => $operation->id), array('class' => 'fancybox shinybutton', 'encode' => false));
                echo CHtml::link('<span>Re-Schedule Now</span>', array('booking/reschedule', 'operation' => $operation->id), array('class' => 'fancybox shinybutton highlighted', 'encode' => false));

// Add the invisible letter css
Yii::app()->clientScript->registerCssFile(
        '/css/eventTypes/25.css',
        'screen, projection'
);

// Add the print css that prints the letter html elements but nothing else
Yii::app()->clientScript->registerCssFile(
        '/css/eventTypes/25_print.css',
        'print'
);

?>
<a class="shinybutton highlighted" onClick="javascript:window.print()" href="#"><span>Print Letter</span></a>
</div>
<div id="ElementLetterOut_layout">
<img src="/img/elements/ElementLetterOut/Letterhead.png" alt="Moorfields logo" border="0" />
<?php

if ($siteId = Yii::app()->request->cookies['site_id']->value) {
        $site = Site::model()->findByPk($siteId);

        if (isset($site)) {
?>
        <div class="ElementLetterOut_siteDetails">
                <?php

                echo $site->name . "<br />\n";
                echo $site->address1 . "<br />\n";
                if ($site->address2) {
                        echo $site->address2 . "<br />\n";
                }
                if ($site->address3) {
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

$patient = $operation->event->episode->patient;

?>
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <p class="ElementLetterOut_to">
		<?php echo $patient->first_name ?> <?php echo $patient->last_name ?><br />
		<?php echo $patient->address->address1 ?><br />
		<?php if ($patient->address->address2) {
			echo $patient->address->address2 . "<br />\n";
		} ?>
		<?php echo $patient->address->city; ?><br />
		<?php echo $patient->address->postcode; ?><br />
	</p>

        <p class="ElementLetterOut_date"><?php echo date("Y-m-d") ?></p>

	<p class="ElementLetterOut_re">
		Hospital number reference: INP/A/<?php echo $patient->hos_num ?><br />
		NHS number: <?php echo $patient->nhs_num ?>
	</p>

        <p class="ElementLetterOut_dear">Dear <?php echo $patient->first_name ?> <?php echo $patient->last_name ?>,</p>

        <p class="ElementLetterOut_text">
I have been asked to arrange your admission for surgery under the care of
<?php echo $operation->getPhrase('Consultant') ?>. This is currently anticipated to be a <?php
	if ($operation->overnight_stay) {
		echo 'Inpatient';
	} else {
		echo 'Day case';
	}
?> procedure
STAYLENGTH in hospital.
<br /><br />
Please will you telephone <?php echo $operation->getPhrase('Contact Number') ?> within <?php echo $operation->getPhrase('Time Limit') ?> of the date of this letter to
discuss and agree a convenient date for your operation. If there is no reply, please
leave a message and contact number on the answer phone.
<br /><br />
Should you no longer require treatment, please let me know as soon as possible.
	</p>

        <p>Yours sincerely,<br /><br /><br /><br />Admissions Officer</p>
<!--span class="page_break"></span-->
<?php
}
?>
</div>
<?php
	}
?>
<div class="cleartall"></div>
<script type="text/javascript">
	$('a.fancybox').fancybox([]);
</script>

