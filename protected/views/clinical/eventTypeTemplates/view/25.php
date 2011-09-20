<?php

// Add the invisible letter css for booking letters
Yii::app()->clientScript->registerCssFile(
        '/css/eventTypes/25.css',
        'screen, projection'
);

// Add the print css that prints the letter html elements but nothing else
Yii::app()->clientScript->registerCssFile(
        '/css/eventTypes/25_print.css',
        'print'
);

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
	if ($operation->status == $operation::STATUS_PENDING) { ?>
<div class="flash-notice">This operation has not been scheduled.</div><?php
	} elseif ($operation->status == $operation::STATUS_NEEDS_RESCHEDULING) { ?>
<div class="flash-notice">This operation needs to be rescheduled.</div>
<?php
	}
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

$patient = $operation->event->episode->patient;

if ($patient->getAge() < 16) {
	$isAdult = false;
} else {
	$isAdult = true;
}

if ($isAdult) {
	$dear = "Dear " . $patient->title . " " . $patient->last_name . ",</p>";
} else {
	$dear = "Dear SUBSITUTE NAME OF GUARDIAN,</p>";
}

$isAdmissionLetter = false;

if ($operation->status != $operation::STATUS_CANCELLED && $editable) {
	if (empty($operation->booking)) {
		$isAdmissionLetter = true;

		// The operation hasn't been booked yet
		echo CHtml::link('<span>Cancel Operation</span>', array('booking/cancelOperation', 'operation' => $operation->id), array('class' => 'fancybox shinybutton', 'encode' => false));
		echo CHtml::link("<span>Schedule Now</span>", array('booking/schedule', 'operation' => $operation->id), array('class' => 'fancybox shinybutton highlighted', 'encode' => false));

		$midsection = ". This is currently anticipated to be a";
		if ($operation->overnight_stay) {
			$admissionCategory = 'n Inpatient';
		} else {
			$admissionCategory = ' Day case';
		}
		$midsection .= $admissionCategory;
		$midsection .= " procedure STAYLENGTH in hospital. <br /><br />Please will you telephone ";
		$midsection .= $operation->getPhrase('Contact Number');
		$midsection .= " within ";
		$midsection .= $operation->getPhrase('Time Limit');
		$midsection .= " of the date of this letter to
discuss and agree a convenient date for your operation. If there is no reply, please
leave a message and contact number on the answer phone.
<br /><br />";

		// Generate the content for the non-booked operation letter
		if ($isAdult) {
			// Adult letter
			$content = "I have been asked to arrange your admission for surgery under the care of ";
			$content .= $operation->getPhrase('Consultant');
			$content .= $midsection;
			$content .= " Should you no longer require treatment, please let me know as soon as possible.";
		} else {
			// Child letter
			$content = "I have been asked to arrange your child's admission for surgery under the care of ";
			$content .= $operation->getPhrase('Consultant');
			$content .= $midsection;
			$content .= "Should your child no longer require treatment, please let me know as soon as possible.";
		}
	} else {
		echo '<p/>';
		echo CHtml::link('<span>Cancel Operation</span>', array('booking/cancelOperation', 'operation' => $operation->id), array('class' => 'fancybox shinybutton', 'encode' => false));
		echo CHtml::link("<span>Re-Schedule Later</span>", array('booking/rescheduleLater', 'operation' => $operation->id), array('class' => 'fancybox shinybutton', 'encode' => false));
		echo CHtml::link('<span>Re-Schedule Now</span>', array('booking/reschedule', 'operation' => $operation->id), array('class' => 'fancybox shinybutton highlighted', 'encode' => false));

		// Booking details:

		if ($isAdult) {
			// Adult letter
			$details = "<br /><br /><b>Date of admission: " . $operation->convertDate($operation->booking->session->date) . "<br />\n";
			$details .= "Time to arrive: " . $operation->convertTime($operation->booking->session->start_time) . "<br />\n";
			$details .= "Date of surgery: " . $operation->convertDate($operation->booking->session->date) . "<br />\n";
			$details .= "Ward: " . $operation->booking->ward->name . ", " . $operation->booking->ward->site->name . "<br />\n";
			$details .= "Situation<br />\n";
			$details .= "You will be discharged from hospital on the same day.</b><br /><br />\n";

			if ($operation->status == $operation::STATUS_RESCHEDULED) {
				// It's a reschedule letter
				$content = "I am writing to inform you that the date for your eye operation has been changed from ";
				$content .= $operation->cancelledBooking->date;
				$content .= ". The details now are:";
				$content .= $details;
				$content .= "It is important that you let us know immediately if you wish to cancel or rearrange this admission date.\n";
				$content .= "You can do this by calling ";
				$content .= $operation->getPhrase('Admission Department');
				$content .= " on ";
				$content .= $operation->getPhrase('Change Tel');
				$content .= ".<br /><br />";
			} else {
				// Non reschedule letter
				$content = "On behalf of ";
				$content .= $operation->getPhrase('Consultant');
				$content .= ", I am delighted to confirm the date of your operation. The details are:";
				$content .= $details;
				$content .= "It is important that you let us know immediately if you are unable to attend on this admission date.\n";
				$content .= "You can do this be calling ";
				$content .= $operation->getPhrase('Change Contact Admission Coordinator');
				$content .= " on ";
				$content .= $operation->getPhrase('Change Tel');
				$content .= ".<br /><br />";
			}

			$content .= "Please let us know if you have any change in your general health that may affect your surgery.<br /><br />\n";
			$content .= "If you do not speak English, please arrange for an English speaking adult to stay with you until you reach the ward and have been seen by a Doctor.<br /><br />\n";
			$content .= "To help ensure your admission proceeds smoothly, please follow these instructions:<br /><br />\n";
			$content .= "<ul>\n";
			$content .= "<li>Bring this letter with you on " . $this->convertDate($operation->booking->session->date) . "</li>\n";
			$content .= "<li>Please complete the attached in-patient questionnaire and bring it with you.</li>\n";
			$content .= "<li>Please go directly to " . $operation->booking->ward->name . " ward<li>\n";
			$content .= "<li>Please bring with you any medication you are using</li>\n";
			$content .= "<li>You must not drive yourself to or from the hospital</li>\n";
			$content .= "<li>We would like to request that only 1 person should accompany you in order to ensure that adequate seating area is available for patient coming for surgery<li>\n";
			$content .= "</ul>\n";
		} else {
			// Child letter
			$details = "<br /><br /><b>Date of admission: " . $operation->convertDate($operation->booking->session->date) . "<br />\n";
			$details .= "Time to arrive: " . $operation->convertTime($operation->booking->session->start_time) . "<br />\n";
			$details .= "Date of surgery: " . $operation->convertDate($operation->booking->session->date) . "<br />\n";
			if ($operation->booking->session->sequence->theatre_id == 1) {
				// It's City Road
				$details .= "Location: Richard Desmond's Children's Eye Centre (RDCEC)<br />\n";
			} else {
				$details .= "Ward: " . $operation->booking->ward->name . ", " . $operation->booking->ward->site->name . "<br />\n";
			}
			$details .= "Situation<br />\n";
			$details .= "You will be discharged from hospital on the same day.</b><br /><br />\n";

			if ($operation->status == $operation::STATUS_RESCHEDULED) {
				// It's a child reschedule letter
				$content = "I am writing to inform you that the date for your child's eye operation has been changed from ";
				$content .= $operation->convertDate($operation->cancelledBooking->date);
				$content .= ". The details now are:";
				$content .= $details;
			} else {
				// It's a child admission letter
				$content = "I am writing to confirm the date for your child's eye operation. The details are: ";
				$content .= $details;
			}

			$content .= "To ensure this admission proceeds smoothly, please follow these instructions.<br />\n";
			$content .= "<ul>\n";
			$content .= "<li><b>Please contact the Children's Ward as soon as possible on 0207 566 2595 or 2596 to discuss pre-operative instructions</b></li>\n";
			$content .= "<li>Bring this letter with you on " . $operation->convertDate($operation->booking->session->date) . "</li>\n";
			$content .= "<li>Please complete the attached in-patient questionnaire and bring it with you</li>\n";
			$content .= "<li>Please go directly to the Main Reception in the RDCEC at the time of your child's admission.<li>\n";
			$content .= "</ul><br /><br />\n";
			$content .= "If there has been any change in your child's general health, such as a cough or cold,  any infectious disease, or any other condition which might affect their fitness for operation, please telephone 0207 556 and ask to speak to a nurse for advice.<br /><br />\n";
			$content .= "If you do not speak English, please arrange for an English speaking adult to stay with your family until you reach the ward and have been seen by a doctor and anaesthetist.<br /><br />\n";
			$content .= "It is very important that you let us know immediately if you are unable to attend on the admission date. Please let us know by return post, or if necessary, telephone ";
			$content .= $operation->getPhrase('Admission Department');
			$content .= " on 0207 566 2258.\n";
		}
	}
// Display print letter button
?>
<a class="shinybutton highlighted" onClick="javascript:window.print()" href="#"><span>Print Letter</span></a>
<?php
} else {
	// No letters to be printed for cancelled or uneditable letters
	$content = '';
}

?>
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

        <p class="ElementLetterOut_date"><?php echo $operation->convertDate(date("Y-m-d")) ?></p>

	<p class="ElementLetterOut_re">
		Hospital number reference: INP/A/<?php echo $patient->hos_num ?><br />
		NHS number: <?php echo $patient->nhs_num ?>
	</p>

        <p class="ElementLetterOut_dear">
		<?php echo $dear ?>
        <p class="ElementLetterOut_text">
        <?php echo $content ?>
		</p>

        <p>Yours sincerely,<br /><br /><br /><br />Admissions Officer</p>
<!--span class="page_break"></span-->

<?php
if ($isAdmissionLetter) {
?>
<img src="/img/elements/ElementLetterOut/Letterhead.png" alt="Moorfields logo" border="0" /><br />
<br />
<hr />
<br />
<div class="admissionHeader">ADMISSION FORM</div>
<br />

<table class="admissionFormClear">
<tr><td>
Hospital Number
</td><td>
<?php echo $patient->hos_num ?>
</td><td>
Patient Name
</td><td>
<?php echo $patient->first_name ?> <?php echo $patient->last_name ?>
</td></tr>
<tr><td>
DoB
</td><td>
<?php echo $operation->convertDate($patient->dob) ?>
</td><td>
Address
</td><td>
<?php echo $patient->address->address1 ?><br />
<?php if ($patient->address->address2) {
	echo $patient->address->address2 . "<br />\n";
} ?>
<?php echo $patient->address->city; ?><br />
<?php echo $patient->address->postcode; ?><br />
</td></tr>
</table>

<br />

<table class="admissionFormBordered">
<tr><td class="admissionFormDashed">
<b>Admitting Consultant:</b>
</td><td>
<?php
//	$consultant = $operation->event->episode->firm->getConsultant();
//	$user = $consultant->contact->userContactAssignment->user;

//	echo($user->title . ' ' . $user->first_name . ' ' . $user->last_name);
?>
</td><td>
<b>Decision to admit date (or today's date):</b>
</td><td>
<?php echo $operation->convertDate($operation->decision_date); ?>
</td></tr>
<tr><td>
<b>Service:</b>
</td><td>
<?php echo $operation->event->episode->firm->serviceSpecialtyAssignment->service->name ?>
</td><td>
<b>Telephone:</b>
</td><td>
<?php echo $patient->primary_phone ?>
</td></tr>
<tr><td>
<b>Site:</b>
</td><td>
<?php echo $site->name ?>
</td><td>
AlternatePhone WorkPhone MobilePhone TBA
</td></tr>
<tr><td>
<b>Person organising admission:</b>
</td><td>
DOCTOR TBA
</td><td>
<b>Dates patient unavailable</b>
</td><td>
DATES CAN'T COME IN TBA
</td></tr>
<tr><td>
<b>Signature:</b>
</td><td>
&nbsp;
</td><td>
<b>Available at short notice:</b>
</td><td>
SHORT NOTICE
</td></tr>
</table>

<br />

<div class="admissionDetails">ADMISSION DETAILS</div>
<table class="admissionFormBordered">
<tr><td>
<b>Urgency</b>
</td><td>
URGENCY
</td><td>
<b>Consultant to be present:</b>
</td><td>
CONSULTANT TO BE PRESENT
</td></tr>
<tr><td>
<b>Admission category:</b>
</td><td>
<?php echo $admissionCategory ?>
</td><td colspan="2" class="admissionFormNoDashed">
&nbsp;
</td></tr>
<tr><td>
<b>Diagnosis:</b>
</td><td>
<?php echo $operation->getDisorder() ?>
</td><td colspan="2" class="admissionFormNoDashed">
&nbsp;
</td></tr>
<tr><td>
<b>Intended proceudre:</b>
</td><td>
OPERATION
</td><td colspan="2" class="admissionFormNoDashed">
&nbsp;
</td></tr>
<tr><td>
<b>Eye:</b>
</td><td>
<?php echo $operation->getEyeText() ?>
</td><td colspan="2" class="admissionFormNoDashed">
&nbsp;
</td></tr>
<tr><td>
<b>Total theatre time (mins):</b>
</td><td>
<?php echo $operation->total_duration ?>
</td></tr>
</table>

<br />

<div class="admissionDetails">PRE-OP ASSESSMENT INFORMATION</div>
<table class="admissionFormBordered">
<tr><td>
<b>Anaesthesia:</b>
</td><td>
ANAESTHESIA
</td><td>
<b>Likely to need anaesthetist review:</b>
</td><td>
ANESTHETIST REVIEW
</td></tr>
<tr><td>
<b>Anaesthesia is:</b>
</td><td>
ANAESTHESIA IS:
</td><td>
<b>Does the patient need to stop medication:</b>
</td><td>
STOP MEDICATION
</td></tr>
</table>

<div class="admissionDetails">COMMENTS</div>
<table class="admissionFormBordered">
<tr><td colspan="2">
Comments
<br />
<br />
<br />
</td></tr>
</table>
<?php
}
?>

<!--span class="page_break"></span-->
</div>
<div class="cleartall"></div>
<script type="text/javascript">
	$('a.fancybox').fancybox([]);
</script>

