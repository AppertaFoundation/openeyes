<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php /** @var OEModule\OphDrPrescription\widgets\PrescriptionEsignElementWidget $this */ ?>
<style>
    table.js-signature-list tr.secondary:hover {
        background-color: lightgray;
    }

    table.js-signature-list tr.secondary .sign-actions button {
        display: none;
    }

    table.js-signature-list tr.secondary:hover .sign-actions button {
        display: block;
    }
</style>
<div class="element-fields">
    <div class="element-data full-width">

        <?php foreach ($this->element->getInfoMessages() as $msg) { ?>
            <div class="alert-box info"><?=CHtml::encode($msg)?></div>
        <?php } ?>

        <?php if (!$this->element->isSigned()) { ?>
            <div class="alert-box issue"><?= $this->element->getUnsignedMessage() ?>
                <?php if ($this->element->usesEsignDevice()) { ?>
                    <a href="#" onclick="bluejay.demoSignatureDeviceLink();">Connect your e-Sign device</a>
                <?php } ?>
            </div>
        <?php } ?>

        <form class="js-view-signature-form" action="/OphDrPrescription/default/finalizeWithSignatures" method="post">
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
            <input type="hidden" name="event" value="<?= $this->element->event->id ?>" />
            <table class="last-left js-signature-list" data-test="signatory-list-table">
                <colgroup>
                    <col style="width:40px">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-3">
                </colgroup>
                <thead>
                <tr>
                    <th></th>
                    <th>Role</th>
                    <th>Signatory</th>
                    <?php if ($this->element->isSigned()) {?>
                        <th>Date</th>
                    <?php } ?>
                    <th>Signature</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    \Yii::app()->user->setFlash('info.info', 'To finalise this prescription, please sign below');
                    $row = 0;
                foreach ($this->element->getSignatures() as $signature) {
                    if ((int)$signature->type === \BaseSignature::TYPE_LOGGEDIN_USER) {
                        // in Prescription, we always display "Prescriber" as role for user who prescribed
                        $signature->signatory_role = $this::PRESCRIBER_DISPLAY_ROLE;
                    }

                    $this->widget(
                        static::getWidgetClassByType($signature->type),
                        [
                            "row_id" => $row++,
                            "element" => $this->element,
                            "signature" => $signature,
                            "mode" => "edit",
                            "show_date_column" => $this->element->isSigned()
                        ]
                    );
                }
                ?>
                </tbody>
            </table>
        </form>
        <script>
            $(document).ready(function() {
                $(document).on('signatureAdded', function() {
                    $('.js-view-signature-form').submit();
                });
            });

            $(function(){
                const options = {
                    "mode" : "<?= !$this->element->isSigned() ? 'edit' : 'view' ?>"
                };
                new OpenEyes.UI.EsignElementWidget($(".<?= \CHtml::modelName($this->element) ?>"), options);
            });
        </script>
    </div>
</div>
