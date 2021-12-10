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
<?php /** @var EsignElementWidget $this */ ?>
<?php
if ($this->isSigningAllowed()) {
    $signatures = [];
    $withdrawal_signature = null;
    $confirm_signatures = [];
    $confirm_signed = false;

    foreach ($this->element->getSignatures() as $signature) {
        if (strcmp($signature->signatory_role, "Withdrawn by") == 0) {
            $withdrawal_signature = $signature;
        } elseif (strcmp($signature->signatory_role, "Confirmed by") == 0) {
            $confirm_signatures[] = $signature;
            if ($signature->isSigned()) {
                $confirm_signed = true;
            }
        } else {
            $signatures[] = $signature;
        }
    }
}
?>

<div class="element-fields">
    <?php if (isset($form)) {
        $form->hiddenInput($this->element, "dummy");
    } ?>
    <div class="element-data full-width">
        <?php foreach ($this->element->getInfoMessages() as $msg) : ?>
            <div class="alert-box info"><?= CHtml::encode($msg) ?></div>
        <?php endforeach; ?>
        <?php if (!$this->isSigningAllowed()) : ?>
            <div class="alert-box warning">E-signing of this event will be available at a later stage.</div>
        <?php else : ?>
            <?php if ($this->element instanceof Element_OphTrConsent_Esign) {
                if ($this->isSigningAllowed() && $withdrawal_signature !== null) { ?>
                    <div class="alert-box warning"><strong>Patient has withdrawn their consent, a reason for the
                            withdrawal must be recorded and the patient must sign to confirm.</strong></div>
                    <table class="last-left">
                        <colgroup>
                            <col class="cols-1">
                            <col class="cols-2">
                            <col class="cols-3">
                            <col class="cols-2">
                            <col class="cols-3">
                        </colgroup">
                            <?php
                            $withdrawal_element = $this->element->event->getElementByClass(\Element_OphTrConsent_Withdrawal::class);
                            $unable_to_consent = $withdrawal_element->isUnableToConsent();
                            $consent_type_id = $this->controller->type_id;

                            if ($unable_to_consent && $withdrawal_element->contact_type_id === null) {
                                $this->render(
                                    'OEModule.OphTrConsent.views.default._add_withdrawal_contact',
                                    ['withdrawal_element' => $withdrawal_element],
                                );
                            } else {
                                echo '<tbody>';
                                $this->widget(
                                    $this->getWidgetClassByType($withdrawal_signature->type),
                                    [
                                        "row_id" => 1,
                                        "element" => $this->element,
                                        "signature" => $withdrawal_signature,
                                        "mode" => ($this->mode === $this::$EVENT_VIEW_MODE ? 'view' : 'edit'),
                                    ]
                                );
                                echo '</tbody>';
                            }
                            ?>
                    </table>
                    <?php
                    $withdrawal_element_criteria = new CDbCriteria();
                    $withdrawal_element_criteria->compare('t.event_id', $this->element->event_id);
                    $withdrawal_element = \Element_OphTrConsent_Withdrawal::model()->find($withdrawal_element_criteria);
                    $this->render(
                        'application.widgets.views.Withdrawal',
                        array(
                            'element' => $this->element,
                            'entry' => $withdrawal_element,
                            'form' => $form
                        )
                    ); ?>
                <?php }
                if ($this->isSigningAllowed() && count($confirm_signatures) != 0) { ?>
                    <?php if ($confirm_signed === true) : ?>
                        <div class="alert-box success"><strong>Consent is confirmed</strong></div>
                    <?php else : ?>
                        <div class="alert-box issue"><strong>The user signature is still required to complete the
                                confirmation of consent.</strong></div>
                    <?php endif; ?>
                    <table class="last-left">
                        <colgroup>
                            <col class="cols-1">
                            <col class="cols-2">
                            <col class="cols-3">
                            <col class="cols-2">
                            <col class="cols-3">
                        </colgroup">
                        <tbody>
                        <?php
                        foreach ($confirm_signatures as $signature) {
                            $this->widget(
                                "OEModule\OphTrConsent\widgets\EsignUsernamePINField",
                                [
                                    "row_id" => "C",
                                    "element" => $this->element,
                                    "signature" => $signature,
                                    "mode" => ($this->mode === $this::$EVENT_VIEW_MODE ? 'view' : 'edit'),
                                ]
                            );
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php if ($confirm_signed === false) { ?>
                        <div class="row flex">
                            <div class="flex-l cols-6">
                            </div>
                            <div>
                                <button class="button blue hint js-remove-confirm pull-right">Cancel consent
                                    confirmation
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                    <hr class="divider"/>
                <?php }
            } ?>
            <?php if (!$this->element->isSigned()) : ?>
                <div class="alert-box issue"><?= $this->element->getUnsignedMessage() ?>
                    <?php if ($this->element->usesEsignDevice()) : ?>
                        <a class="js-connect-device" href="javascript:void(0);">Connect your e-Sign device</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <table class="last-left">
                <thead>
                <tr>
                    <th></th>
                    <th>Role</th>
                    <th>Signatory</th>
                    <th>Date</th>
                    <th>Signature</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $row = 0;
                foreach ($signatures as $signature) {
                    $this->widget(
                        static::getWidgetClassByType($signature->type),
                        [
                            "row_id" => $row++,
                            "element" => $this->element,
                            "signature" => $signature,
                            "mode" => ($this->mode === $this::$EVENT_VIEW_MODE ? 'view' : 'edit'),
                        ]
                    );
                }
                ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        new OpenEyes.UI.EsignElementWidget(
            $(".<?= \CHtml::modelName($this->element) ?>"),
            {
                mode: "<?= $this->mode === $this::$EVENT_VIEW_MODE ? 'view' : 'edit' ?>",
                element_id: <?= json_encode($this->element->id) ?>
            }
        );
    });

    $(document).ready(function () {
        <?php
        $confirm_element_criteria = new CDbCriteria();
        $confirm_element_criteria->compare('t.event_id', $this->element->event_id);
        $confirm_element = \Element_OphTrConsent_Confirm::model()->find($confirm_element_criteria);
        ?>

        $(this).on('click', '.js-remove-confirm', function (e) {
            e.preventDefault();
            let element = document.getElementsByClassName('element ' + <?= CJavaScript::encode(CHtml::modelName($confirm_element)) ?>)[0];
            element.dispatchEvent(new Event('element_removed'));
            removeElement(element);
            window.location.href = "<?= Yii::app()->createUrl('/OphTrConsent/default/removeConfirm?event_id=' . $element->event_id) ?>";
        });
    });
</script>
