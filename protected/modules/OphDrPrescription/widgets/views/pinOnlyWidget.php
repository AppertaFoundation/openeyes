<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php
/** @var \OEModule\OphDrPrescription\widgets\PinOnlyWidget $this */
/** @var string $row_id */

$widget_class = (new \ReflectionClass($this))->getShortName();
$uid = (new \ReflectionClass($this->element))->getShortName() . "_" . $widget_class . "_" . $row_id;

?>
<tr id="<?= $uid ?>" data-row_id="<?= $row_id ?>" class="secondary">
    <?php $this->renderHiddenFields(); ?>
    <!-- Row num -->
    <td><span class="highlighter js-row-num"></span></td>
    <?php if (!$hide_role) : ?>
        <!-- Role -->
        <td><span class="js-signatory-label"><?= $this->signature->signatory_role ?></span></td>
    <?php endif; ?>
    <!-- Name -->
    <td><span class="js-signatory-name" data-test="signatory-name"><?= $this->signature->signatory_name ?></span></td>
    <!-- Date -->
    <td <?php if (!$this->element->isSigned()) {
        echo 'style="display:none"';
        }?>>
        <div class="js-signature-date" data-test="signature-date">
            <?php $this->displaySignatureDate() ?>
        </div>
    </td>
    <td>
        <div<?= $this->isSigned() ? ' style="display:none"' : ''?>>
        <?php if ($this->is_pin_required) : ?>
            <div class="js-signature-control" data-test="signature-control-widget">
                <div class="oe-user-pin">
                    <?php echo CHtml::passwordField('pin_' . $uid, '', array(
                        'placeholder' => "******",
                        'maxlength' => 6,
                        'inputmode' => "numeric",
                        'class' => "user-pin-entry js-pin-input",
                        "autocomplete" => "off",
                    )); ?>
                    <button type="button" class="try-pin js-sign-button" data-test="pin-sign-button">PIN sign</button>
                </div>
            </div>
        <?php else : ?>
            <button type="button" data-test="complete-sign-btn" class="hint blue js-no-pin-auto-sign-btn-<?=$row_id;?>">Complete</button>
        <?php endif; ?>
        </div>

    <!-- Signature -->
        <div class="js-signature-wrapper flex-l"
             data-test="signature-wrapper" <?= !$this->isSigned() ? 'style="display:none"' : ''?>>
            <?php $this->displaySignature() ?>
            <div class="esigned-at" data-test="esigned-at">
                <i class="oe-i tick-green small pad-right"></i>Signed <small>at</small> <span class="js-signature-time"><?php $this->displaySignatureTime() ?></span>
            </div>
        </div>
    </td>
    <td class="sign-actions">
        <?php if ($this->isSigned()) { ?>
        <button type="button" data-test="remove-sign-btn" class="hint red js-remove-sign-btn-<?=$row_id;?>">Remove</button>
        <?php } ?>
    </td>
</tr>
<script type="text/javascript">
    $(function(){
        <?php if ($this->is_pin_required) { ?>
            new OpenEyes.UI.EsignWidget($("#<?=$uid?>"), {
                submitAction: "<?=$this->getAction()?>",
                signature_type: <?= $this->signature->type ?>,
                element_id: <?= $this->element->id ?? "null" ?>,
                mode: "<?= $this->mode ?>",
                get_user_by_pin: true,
                removeButtonSelector: `.js-remove-sign-btn-<?=$row_id;?>`,
                signatureId: <?=$this->signature->id ?: 'null';?>
            });
        <?php } else { ?>
            new OpenEyes.UI.EsignWidget($("#<?=$uid?>"), {
                submitAction: "getSignatureForLoggedInUser",
                completeButtonSelector: `.js-no-pin-auto-sign-btn-<?=$row_id;?>`,
                removeButtonSelector: `.js-remove-sign-btn-<?=$row_id;?>`,
                signatureId: <?=$this->signature->id ?: 'null';?>,

                signature_type: <?= $this->signature->type ?>,
                element_id: <?= $this->element->id ?? "null" ?>,
                mode: "<?= $this->mode ?>",
                get_user_by_pin: true
            });
        <?php } ?>
    });
</script>
