<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php

use \OEModule\OphCoCvi\models\Element_OphCoCvi_Esign as SignatureElement;

/** @var Patient $patient */
/** @var Event $event */
if ($event) {
    /** @var \OEModule\OphCoCvi\models\Element_OphCoCvi_Esign $element */
    $element = $event->getElementByClass(\OEModule\OphCoCvi\models\Element_OphCoCvi_Esign::class);
} else {
    $element = null;
}

$institution = Institution::model()->getCurrent();
$site = Site::model()->getCurrent();

$nhs_num = $this->patient->getNhs();

$logo_helper = new LogoHelper();
$domographics_element = $this->event->getElementByClass('OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics');
?>

<header class="print-header flex">
    <?= $logo_helper->render('letter_head') ?>
</header>
<div class="print-title text-c"><h1 class="highlighter">Please sign &amp; return</h1></div>
<hr class="divider">
<main class="print-main"><h2>Part 4: Consent to share information</h2>
    <div class="highlighter">I understand that by signing this form</div>
    <p>I give my permission for a copy to be sent to my GP to make them aware of this certificate.</p>
    <div class="box">
        <div class="flex">
            <div class="dotted-area">
                <div class="label">GP name/practice</div>
                <?= \CHtml::encode($domographics_element->gp_name) ?>
            </div>
            <div class="dotted-area">
                <div class="label">GP address</div>
                <?= nl2br(\CHtml::encode($domographics_element->gp_address)) ?>
            </div>
        </div>
    </div>
    <p>I give my permission for a copy to be sent to my local council (or an organisation working on their behalf) who
        have a duty (under the Care Act 2014) to contact me to offer advice on living with sight loss and explain the
        benefits of being registered. When the council contacts me, I am aware that I do not have to accept any help, or
        be registered at that time, if I choose not to do so.</p>
    <div class="box">
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Local council</div>
                <?= \CHtml::encode($domographics_element->la_name) ?>
            </div>
            <div class="dotted-area">
                <div class="label">Council address</div>
                <?= \CHtml::encode($domographics_element->la_address) ?>
            </div>
        </div>
    </div>
    <p>I give my permission for a copy to be sent to The Royal College of Ophthalmologists, Certifications Office at
        Moorfields Eye Hospital; where information about eye conditions is collected, and used to help to improve eye
        care and services in the future.</p><h4>Please turn over to sign</h4>
    <hr class="divider">
    <div class="break"><!-- **** page break ***** --></div>
    <h3>Patient details</h3>
    <table class="normal-text row-lines">
        <colgroup>
            <col class="cols-5">
            <col class="cols-7">
        </colgroup>
        <tbody>
        <tr>
            <th>Patient's surname/family name</th>
            <td><?= $patient->last_name ?></td>
        </tr>
        <tr>
            <th>Patient's first names</th>
            <td><?= $patient->first_name ?></td>
        </tr>
        <tr>
            <th>NHS #</th>
            <td><?= $nhs_num ?></td>
        </tr>
        </tbody>
    </table>
    <p>I understand that I do not have to consent to sharing my information with my GP, local council or The Royal
        College of Ophthalmologists Certifications Office, or that I can withdraw my consent at any point by contacting
        them directly.</p>
    <p>I confirm that my attention has been drawn to the paragraph entitled ‘Driving’ and understand that I must not
        drive.</p>
    <div class="highlighter">Please sign inside the box:</div>
    <div>
        <?= $this->renderPartial("_QR_box", ['qr_code_data' => $qr_code_data], true); ?>
    </div>
    <div class="box">
        <div class="dotted-area">
            <div class="label">Signed by <?= CHtml::encode($role) ?></div>
             <?= CHtml::encode($signatory) ?>
        </div>
    </div>
</main>
