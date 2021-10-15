<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$logo_helper = new LogoHelper();
$esign_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_Esign');
$eventinfo_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_EventInfo');
$clinicalinfo_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ClinicalInfo');
$consent_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_Consent');
$demographics_element = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics');
$clearical_info = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo');

$getSignatureSource = function (int $type) use ($eventinfo_element): string {
    $signature = $eventinfo_element->getSignatureByType($type);

    if (isset($signature->signatureFile)) {
        $signature_content = file_get_contents($signature->signatureFile->getPath());
        return 'data:' . $signature->signatureFile->mimetype . ';base64,' . base64_encode($signature_content);
    }

    return '';
};

$gp_name = '';
$address = [];
$gp_postcode = '';
$gp_postcode_2nd = '';
$gp_telephone = '';

$la_name = '';
$la_address = '';
$la_postcode = '';
$la_postcode_2nd = '';
$la_telephone = '';

if (isset($print_empty) && !$print_empty) {
    // print filled in form

    if ($consent_element->consented_to_gp) {
        $gp_name = $demographics_element->gp_name;
        $address = [$demographics_element->gp_address];
        $gp_postcode = $demographics_element->gp_postcode;
        $gp_postcode_2nd = $demographics_element->gp_postcode_2nd;
        $gp_telephone = $demographics_element->gp_telephone;
    }

    if ($consent_element->consented_to_la) {
        $la_name = $demographics_element->la_name;
        $la_address = $demographics_element->la_address;
        $la_postcode = $demographics_element->la_postcode;
        $la_postcode_2nd = $demographics_element->la_postcode_2nd;
        $la_telephone = $demographics_element->la_telephone;
    }
}
?>


<header class="print-header">
    <?= $logo_helper->render() ?>
</header>

<!-- Page title -->
<div class="print-title text-c">
    <h1 class="highlighter">Part 4 only<br></h1>
</div>
<hr class="divider">

<main class="print-main">
    <h2>Part 4: Consent to share information</h2>
    <div class="highlighter">I understand that by signing this form</div>
    <p>I give my permission for a copy to be sent to my GP to make them aware of this certificate.</p>
    <div class="box">
        <div class="dotted-area">
            <div class="label">My <b>GP</b> name/practice</div>
            <?= CHtml::encode($gp_name); ?>
        </div>
        <div class="dotted-area">
            <div class="label">GP address</div>
            <?= CHtml::encode($address ? implode(", ", $address) : '') ?>
        </div>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Postcode</div>
                <?= CHtml::encode($gp_postcode); ?>
                <?= CHtml::encode($gp_postcode_2nd); ?>
            </div>
            <div class="dotted-area">
                <div class="label">Telephone number</div>
                <?= CHtml::encode($gp_telephone) ?>
            </div>
        </div>
    </div>
    <p>I give my permission for a copy to be sent to my local council (or an organisation working on their behalf) who
        have a duty (under the Care Act 2014) to contact me to offer advice on living with sight loss and explain the
        benefits of being registered. When the council contacts me, I am aware that I do not have to accept any help, or
        be registered at that time, if I choose not to do so.</p>
    <div class="box">
        <div class="dotted-area">
            <div class="label">My <b>local council</b> name</div>
            <?= CHtml::encode($la_name ?? ''); ?>
        </div>
        <div class="dotted-area">
            <div class="label">Address</div>
            <?= CHtml::encode($la_address ?? ''); ?>
        </div>
        <div class="dotted-write"><!-- Provide a dotted line area to write in --></div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Postcode</div>
                <?= CHtml::encode($la_postcode ?? ''); ?><?= CHtml::encode($la_postcode_2nd ?? ''); ?>
            </div>
            <div class="dotted-area">
                <div class="label">Telephone number</div>
                <?= CHtml::encode($la_telephone ?? ''); ?>
            </div>
        </div>
    </div>
    <p>I give my permission for a copy to be sent to The Royal College of Ophthalmologists, Certifications Office at
        Moorfields Eye Hospital; where information about eye conditions is collected, and used to help to improve eye
        care and services in the future.</p>
    <p>I understand that I do not have to consent to sharing my information with my GP, local council or The Royal
        College of Ophthalmologists Certifications Office, or that I can withdraw my consent at any point by contacting
        them directly.</p>
    <p>I confirm that my attention has been drawn to the paragraph entitled ‘Driving’ and understand that I must not
        drive.</p>
    <h4>Signed by the patient (or signature and name of parent/guardian or representative)</h4>
    <div class="box">
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Signed</div>
                <?php if (isset($print_empty) && !$print_empty) : ?>
                <img src="<?= $getSignatureSource(BaseSignature::TYPE_PATIENT); ?>" class="signature">
                <?php endif;?>
            </div>
        </div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Printed name</div>
                <?php if (isset($print_empty) && !$print_empty) : ?>
                    <?=$patient->fullName;?>
                <?php endif;?>
            </div>
        </div>
    </div>
</main>
