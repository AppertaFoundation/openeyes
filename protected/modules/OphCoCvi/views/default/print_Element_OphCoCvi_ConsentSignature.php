<?php

use \OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature as SignatureElement;

Yii::app()->clientScript->registerCss("print", <<<CSS
    html {
        background-color: white;
    }
    body {
        margin: 12.7mm;
        background-color: white;
        font-family: Arial;
    }

    table {
        border: 0;
        font-family: Arial;
    }
    
    table td {
        background-color: white;
        border: 0;
        vertical-align: top;
    }
    
    table thead tr th  {
        background-color: black;
        color: white;
        text-align: center;
        font-weight: bold;
        font-family: Arial;
    }

    table.sign-box {
        border: 4pt solid black;
    }

    table.sign-box tr {
        height: 140mm;
        font-family: Arial;
    }
CSS
);

/** @var Patient $patient */
/** @var Event $event */
if($event) {
    /** @var \OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature $element */
    $element = $event->getElementByClass(\OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature::class);
}
else {
    $element = null;
}

$checked = "<span style='font-size: 60px' class=\"square\">&#x2612;</span>";
$unchecked = "<span style='font-size: 60px' class=\"square\">&#x2610;</span>";

$dotted_line = "..........................................................................";

if($element->isSigned()) {
    $url = $element->signature_file->getBase64Source();
    $img = '<img src="'.$url.'" />';
}
?>

    <p style="font-family: Arial;">
        Patient Name: <?=CHtml::encode($patient->getFullName())?><br/>
        Hospital number: <?=CHtml::encode($patient->hos_num)?>
    </p>
    <h3 style="margin:auto; text-align: center; font-size:24px" class="text-center;">Part 4: Consent to share information</h3>
    <table style="width:100%" cellpadding="7" cellspacing="0">
        <thead>
        <tr style="background-color: black;color: white;font-size: 24px;">
            <th>
                I understand that by signing this form
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td valign="top" style="font-family: Arial;font-size: 24px;border: 1px solid #00000a; padding-top: 0cm; padding-bottom: 0cm; padding-left: 0.2cm; padding-right: 0.19cm">
                <p><strong>PLEASE TICK</strong> where appropriate:</p>

                <table width="100%" style="font-size: 24px;">
                    <tr>
                        <td>
                        <span class="square">
                            <?=($element && $element->consented_to_gp) ? $checked : $unchecked ?>
                        </span>
                        </td>
                        <td>
                            <p>
                                I give my permission for a copy to be sent to my <b>GP</b> to make them aware of this certificate.
                            </p>
                        </td>
                    </tr>
                </table>
                <br>
                <table style="font-size: 24px;">
                    <tr>
                        <td>My GP name/practice:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><br>Postcode:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Telephone
                            number:</td>
                        <td></td>
                    </tr>
                </table>
                <br>
                <table width="100%" style="font-size: 24px;">
                    <tr>
                        <td>
                            <?=($element && $element->consented_to_la) ? $checked : $unchecked ?>
                        </td>
                        <td>
                            <p>
                                I give my permission for a copy to be sent to <b>my local council</b> (or an organisation working on their behalf) who have a duty (under the Care Act 2014) to contact me to offer advice on living with sight loss and explain the benefits of being registered. When the council contacts me, I am aware that I do not have to accept any help, or be registered at any time, if I choose not to do so.
                            </p>
                        </td>
                    </tr>
                </table>
                <br>
                <br>
                <table style="font-size: 24px;">
                    <tr>
                        <td>My local council name:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Postcode:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Telephone
                            number:</td>
                        <td></td>
                    </tr>
                </table>
                <br>
                <table width="100%" style="font-size: 24px;">
                    <tr>
                        <td>
                            <?=($element && $element->consented_to_rcop) ? $checked : $unchecked ?>
                        </td>
                        <td>
                            <p>
                                I give my permission for a copy to be sent to <b>The Royal College of Ophthalmologists, Certifications Office</b> at Moorfields Eye Hospital; where information about eye conditions is collected, and used to help to improve eye care and services in the future.
                            </p>
                        </td>
                    </tr>
                </table>

                <p>I understand that I do not have to consent to sharing my information with my GP, local council or The Royal College of Ophthalmologists Certifications Office, or that I can withdraw my consent at any point by contacting them directly.</p>
                <p>I confirm that my attention has been drawn to the paragraph entitled "Driving" on page 8 and understand that I must not drive.</p>

                <h2 style="text-align: center" class="text-center">
                    Please <b>sign</b> on the reverse of this page.
                </h2>
            </td>
        </tr>
        </tbody>
    </table>
    <p class="pageBreak">&nbsp;</p>
    <div class="pageBreak">
        <br/>
    </div>
    <p>
        Patient Name: <?=CHtml::encode($patient->getFullName())?><br/>
        Hospital number: <?=CHtml::encode($patient->hos_num)?>
    </p>
	
	<br><br>
    <p style="font-size: 24px;margin:0px;padding:0px"><b>PLEASE SIGN INSIDE THE BOX</b></p>
    <div style="width: 100%">
        <?php if($this->action->id === "sign"): ?>
            <?php $this->widget("application.widgets.PatientSignatureCaptureElement", array(
                "mode" => BaseSignatureCaptureElement::$EVENT_SIGN_MODE,
                "element" => $element,
            )); ?>
        <?php elseif($this->action->id !== "printQRSignature"): ?>
            <table class="sign-box">
                <tr>
                    <td>
                        <?=$element->isSigned() ? $img : ""?>
                    </td>
                </tr>
            </table>
        <?php endif; ?>
    </div>


	<?php if($this->action->id === "printQRSignature"): ?>
		<?php echo $this->renderPartial("QR_box", ['qr_code_data' => $qr_code_data], true); ?>
	<?php endif;?>


    <p style="font-size: 24px">Signed by:</p>

    <table width="100%" style="font-size: 24px">
        <tr>
            <td>
                <?= ($element && $element->signatory_person == SignatureElement::SIGNATORY_PERSON_PATIENT) ? $unchecked : $unchecked ?>
            </td>
            <td>
                <p>
                    I am the <b>patient</b>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <?= ($element && $element->signatory_person == SignatureElement::SIGNATORY_PERSON_PARENT) ? $unchecked : $unchecked ?>
            </td>
            <td>
                <br>
                <p>
                    I am the <b>patient's parent or guardian</b> and my name is (PLEASE PRINT):
                    <br/><br/>
                    <?php if($element && $element->signatory_person == SignatureElement::SIGNATORY_PERSON_PARENT && $element->signatory_name): ?>
                        <?=CHtml::encode($element->signatory_name)?>
                    <?php else: ?>
                        <?=$dotted_line?>...........................................
                    <?php endif; ?>
                    <br /><br />
                    Relationship to the patient: <?= ($element && $element->relationship_status) ?: $dotted_line ?>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <?= $element->signatory_person == SignatureElement::SIGNATORY_PERSON_REPRESENTATIVE ? $unchecked : $unchecked ?>
            </td>
            <td>
                <br>
                <p>
                    I am the <b>patient's representative</b> and my name is (PLEASE PRINT):
                    <br/><br/>
                    <?php if($element && $element->signatory_person == SignatureElement::SIGNATORY_PERSON_REPRESENTATIVE && $element->signatory_name): ?>
                        <?=CHtml::encode($element->signatory_name)?>
                    <?php else: ?>
                        <?=$dotted_line?>...........................................
                    <?php endif; ?>
                </p>
            </td>
        </tr>

    </table>
    