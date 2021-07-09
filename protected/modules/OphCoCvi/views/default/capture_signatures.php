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
<?php
$user = \User::model()->findByPk(\Yii::app()->user->id);

if ($this->checkUserSigned()) {
    $clinical_element = $this->getManager()->getClinicalElementForEvent($this->event); ?>
    <div class="element-data" xmlns="http://www.w3.org/1999/html">
        <div class="row data-row">
            <div class="large-12 column">
                <div id="div_signature_pin"  class="row field-row">
                    <div class="large-12 column">
                        This CVI has been signed by <b><?php echo CHtml::encode($clinical_element->consultant->getFullName())?></b>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else {
    if ($this->checkClinicalEditAccess()) { ?>
        <div class="element-data" xmlns="http://www.w3.org/1999/html">
            <div class="row data-row">
                <div class="large-12 column">
                <?php if (!$user->checkSignature()) { ?>
                    <div id="div_signature_pin" class="row field-row">
                        <div class="large-12 column">
                            <label>To sign this CVI, you will need to capture and upload your signature.
                                <a href="/profile/signature">Please click here to capture consultant signature
                                    now</a></label>
                        </div>
                    </div>
                <?php } else {
                    if ($this->getManager()->getClinicalElementForEvent($this->event)) {
                        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                            'id' => 'sign-cvi-form',
                            'enableAjaxValidation' => false,
                            'layoutColumns' => array(
                                'label' => 2,
                                'field' => 10
                            ),
                            'action' => '/OphCoCvi/default/signCVI/' . $this->event->id,
                            'method' => 'POST'
                        ));
                        ?>
                        <div id="div_signature_pin" class="row field-row">
                            <div class="large-4 column">
                                <label for="signature_pin">Consultant's signature - please enter your PIN:</label>
                            </div>
                            <div class="large-2 column">
                                <input type="password" maxlength="4" name="signature_pin" id="signature_pin">
                            </div>
                            <div class="large-4 column end">
                                <?php echo CHtml::button('Sign this eCVI', array(
                                    'type' => 'submit',
                                    'id' => 'et_sign_cvi',
                                    'name' => 'sign_cvi',
                                    'class' => 'small button primary event-action'
                                )); ?>

                            </div>
                        </div>
                        <?php
                        $this->endWidget();
                    } else { ?>
                        <div id="div_signature_pin" class="row field-row">
                        <div class="large-12 column">
                            <label>You can sign this CVI once the clinical data has been created.</label>
                        </div>
                    </div>
                    <?php }
                } ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="large-12 column">
            <div class="alert-box with-icon warning">
                A consultant is required to sign this CVI.
            </div>
        </div>

    <?php }
}
