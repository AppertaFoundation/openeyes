<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>


<div class="element-data">
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('is_patient')) ?>:</div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo $element->is_patient ? 'Yes' : 'No' ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('signature_date')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->NHSDate('signature_date')) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <div class="large-2 column">
            <div
                class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('representative_name')) ?></div>
        </div>
        <div class="large-10 column end">
            <div class="data-value"><?php echo CHtml::encode($element->representative_name) ?></div>
        </div>
    </div>
    <div class="row data-row">
        <?php if ($element->checkSignature()) { ?>
            <div class="large-2 column">
                <div class="data-label">Captured Signature</div>
            </div>
            <div class="large-6 column end">
                <img src="/OphCoCvi/default/displayConsentSignature/<?=$this->event->id?>" style="height: 60px" />
            </div>
        <?php } else { ?>
            <div class="large-12 column end">
                <?php echo CHtml::button('Capture patient signature',  array('type'=> 'button' , 'id' => 'capture-patient-signature', 'name' => 'capturePatientSignature', 'class'=>'small button primary event-action')); ?>
            </div>
            <div id="capture-patient-signature-instructions" class="hidden">
                <div class="large-4 column">
                    <ol>
                        <li>Click the button to print the first page of the CVI Certificate.</li>
                        <li>Obtain patient/patient representative signature on the print out.</li>
                        <li>Visit <?= Yii::app()->params['signature_app_url'] ? : "the OpenEyes Phone Application" ?> on your mobile device.</li>
                        <li>Follow the instructions to scan the patient signature.</li>
                    </ol>
                </div>
                <div class="large-4 column end">
                    <?php echo CHtml::button('Print first page',  array('data-print-url' => '/OphCoCvi/default/consentSignature/' . $this->event->id, 'type'=> 'button' , 'id' => 'print-for-signature', 'name' => 'printForSignature', 'class'=>'small button primary event-action')); ?> <br /><br />
                    <?php echo CHtml::link('Retrieve Signature', '/OphCoCvi/default/retrieveConsentSignature/' . $this->event->id, array('class' => 'button small secondary')); ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="element-data">
    <div class="row data-row">

    </div>
</div>