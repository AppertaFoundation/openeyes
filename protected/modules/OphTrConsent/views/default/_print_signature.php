<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
/** @var OphTrConsent_Signature $signature */
/** @var Element_OphTrConsent_Esign $element */
if ($element->isBeingSigned($signature)) {
    $this->widget(SignatureCapture::class, [
        "submit_url" => Yii::app()->createUrl(
            $this->module->id . "/" . $this->id . "/saveCapturedSignature",
            [
                "element_id" => $element->id,
                "element_type_id" => $element->getElementType()->id,
                "signature_type" => $signature->type,
                "signatory_role" => urlencode(Yii::app()->request->getParam("signatory_role")),
                "signatory_name" => urlencode(Yii::app()->request->getParam("signatory_name")),
                "initiator_element_type_id" => Yii::app()->request->getParam("initiator_element_type_id"),
                "initiator_row_id" => Yii::app()->request->getParam("initiator_row_id"),
            ]
        ),
        "after_submit_js" =>
            Yii::app()->request->getParam("deviceSign") > 0 ?
                // The signature was submitted using a handheld device, go back to device ready page
                'function(response, widget)
                        {window.parent.formHasChanged=false;window.parent.location="/site/deviceready";}'
                :
                // The signature was submitted in OpenEyes event view mode, refresh the page
                'function(response, widget)
                        {window.parent.formHasChanged=false;window.parent.location.reload();}',
    ]);
} elseif ($signature->isSigned()) {
    ?>
    <div class="box">
        <div class="flex">
            <div class="dotted-area">
                <div class="label">Signed</div>
                <?php
                echo $signature->getPrintout() ?>
            </div>
            <div class="dotted-area">
                <div class="label">Date</div>
                <?php echo CHtml::encode(date("j M Y, H:i", strtotime($signature->last_modified_date))); ?>
            </div>
        </div>
        <div class="flex">
            <div class="dotted-area">
                <div class="label"><?= $name_label ?></div>
                <?= $signature->signatory_name ?>
            </div>
            <div class="dotted-area">
                <div class="label"><?= $title_label ?></div>
                <?= $job_title ?? $signature->signatory_role ?>
            </div>
        </div>
    </div>
    <?php
}

?>
