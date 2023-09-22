<?php
$uid = $this->field_name;


?>
<td>e-Signature</td>
<td id="<?= $uid ?>">
    <div class="flex-l">
        <?php $this->renderHiddenFields(); ?>


        <div class="oe-user-pin js-signature-control js-pin-fields"<?php if ($is_signed) {
            echo 'style="display:none"';
                                                                   } ?>>
            <?php echo CHtml::passwordField('pin_' . $uid, '', array(
                'placeholder' => "********",
                'maxlength' => 8,
                'inputmode' => "numeric",
                'class' => "user-pin-entry js-pin-input",
                'data-test' => "event-auto-pin-entry"
            )); ?>
            <button type="button" class="try-pin js-sign-button" data-test="event-auto-sign-by-pin-button">Sign by PIN
            </button>
        </div>
        <?php $this->displaySignature() ?>
        <div class="esigned-at js-signature-wrapper" data-test="esigned-at"
            <?= !$is_signed ? 'style="display:none"' : '' ?>>
            <i class="oe-i tick-green small pad-right"></i>Signed <small>at</small> <span
                class="js-signature-time"><?php $this->displaySignatureTime() ?></span>
        </div>
    </div>
</td>

<script type="text/javascript">
    $(function () {
        new OpenEyes.UI.EsignWidget($("#<?=$uid?>"), {
            submitAction: "<?=$this->getAction()?>",
            signature_type: <?= $this->signature->type ?>,
            signatureInsertBeforeWrapper: true,
        });
    });
</script>
