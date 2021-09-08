<?php
    /** @var Element_ConsultantSignature $element */
    /** @var ConsultantSignatureElementWidget $this */
    $modelName = CHtml::modelName($element);
    $module_id = $this->getController()->getModule()->id;
    $user = User::model()->findByPk(Yii::app()->user->id);
if (isset($this->controller->event)) {
    $event_type_name = $this->controller->event->eventType->name;
} else {
    $event_type_name = "Form";
}
    $tokenName = Yii::app()->request->csrfTokenName;
    $token = Yii::app()->request->csrfToken;

    $leftCol = $this->inEditMode() ? "2" : "3";
    $rightCol = $this->inEditMode() ? "5" : "9";

if (is_null($form)) {
    $form = new BaseEventTypeCActiveForm();
}

?>
<div class="<?=$this->inEditMode() ? "element-fields row" : "element-data"?>">
    <div id="fieldset_<?=$modelName?>_signed" style="<?= $element->isSigned() ? "" : "display:none"?>">
        <fieldset class="row <?=$this->inEditMode() ? "field-row" : "data-row"?>">
            <div class="large-<?=$leftCol?> column">
                <label><?=$element->getAttributeLabel("signed_by_user_id")?>:</label>
            </div>
            <div class="large-<?=$rightCol?> column end">
                <p><?php echo $element->signed_by_user_id ? $element->signed_by->getFullNameAndTitleAndRole() : $user->getFullNameAndTitleAndRole(); ?></p>
            </div>
        </fieldset>
        <fieldset class="row <?=$this->inEditMode() ? "field-row" : "data-row"?>">
            <div class="large-<?=$leftCol?> column">&nbsp;</div>
            <div class="large-3 column end">
                <img id="<?=$modelName?>_signature_image" src="<?=$element->protected_file_id ? "/ProtectedFile/view?id=".$element->protected_file_id."&name=signature.jpg" : "//#0"?>" style="<?php if (!$element->protected_file_id) {
                    echo "display:none;";
                         }?> max-height: 170px;" alt="Signature"/>
            </div>
        </fieldset>
    </div>
    <fieldset id="fieldset_<?=$modelName?>_unsigned" class="row <?=$this->inEditMode() ? "field-row" : "data-row"?>" style="<?= !$element->isSigned() ? "" : "display:none"?>">
        <?php if (!is_null($info_msg = $this->getInfoMessage())) : ?>
        <div class="alert-box warning" style="margin: 0 10px 10px 10px;">
            <?=$info_msg?>
        </div>
        <?php endif; ?>

        <?php if (!$user->hasStoredSignature()) : ?>
            <div class="alert-box warning" style="margin: 0 10px;">
                You can't sign this event now because you haven't recorded your signature in OpenEyes yet. Please go to <a href="/profile/signature">your profile</a> and capture your signature.
            </div>
        <?php elseif ($this->isSigningAllowed()) : ?>
            <div class="large-<?=$leftCol?> column">
                <label><?=$element->getAttributeLabel("pin")?>:</label>
            </div>
            <div class="large-1 column">
                <p><input data-allow-enterkey="true" type="text" id="<?=$modelName?>_pin" maxlength="4" class="dummy-password" /> </p>
            </div>
            <div class="large-8 column end">
                <button type="button" class="button small" id="<?=$modelName?>_consultant_sign">Sign this <?=$event_type_name?></button>
            </div>
        <?php endif; ?>
    </fieldset>

    <?php if ($element->signature_date_readonly) : ?>
    <fieldset class="row <?=$this->inEditMode() ? "field-row" : "data-row"?>">
        <div class="large-<?=$leftCol?> column">
            <label><?=$element->getAttributeLabel("signature_date")?>:</label>
        </div>
        <div class="large-<?=$rightCol?> column end">
            <p id="<?=$modelName?>_signature_date_text">
                <?php
                if ($element->isSigned()) {
                    $time = strtotime($element->signature_date);
                    if ($time) {
                        echo date("j M Y, H:i", $time);
                    }
                } else {
                    echo "-";
                }
                ?>
            </p>
            <?php echo $form->hiddenField($element, "signature_date"); ?>
        </div>
    </fieldset>
    <?php else : ?>
        <?php echo $form->datePicker($element, "signature_date", array("style" => "width: 110px")); ?>
    <?php endif; ?>

    <?php echo $form->hiddenField($element, "protected_file_id") ?>
    <?php echo $form->hiddenField($element, "signed_by_user_id") ?>
</div>
<script type="text/javascript">
    $(function () {
        var $pin = $("#<?=$modelName?>_pin");

        $pin.off("keypress");
        $pin.keypress(function (e) {
            if(e.keyCode === 13) {
                $("#<?=$modelName?>_consultant_sign").trigger("click");
                e.preventDefault();
                return false;
            }
        });

        $(document).off("click", "#<?=$modelName?>_consultant_sign");
        $(document).on("click", "#<?=$modelName?>_consultant_sign", function (e) {
            var $file_id = $("#<?=$modelName?>_protected_file_id");
            var pin =  $pin.val();
            var $signed_fs = $("#fieldset_<?=$modelName?>_signed");
            var $unsigned_fs = $("#fieldset_<?=$modelName?>_unsigned");
            var url;
            <?php if (!$this->inEditMode()) : ?>
            url = "/<?=$module_id?>/default/signConsultantSignatureElement?element_id=<?=$element->id?>&element_type_id=<?=$element->getElementType()->id?>&user_id=<?= ($element->signed_by_user_id ? $element->signed_by_user_id : $user->id) ?>";
            <?php else : ?>
            url = "/user/getDecryptedSignatureId?id=<?= ($element->signed_by_user_id ? $element->signed_by_user_id : $user->id) ?>";
            <?php endif; ?>
            $.post(url,
                {
                    "<?=$tokenName?>": "<?=$token?>",
                    "pin" : pin
                },
                function(data) {
                    if (data === "0") {
                        new OpenEyes.UI.Dialog.Alert({
                            content: 'Sorry, the PIN you entered is invalid.'
                        }).open();
                        $pin.val("");
                    }
                    else if(!isNaN(data)) {
                        $file_id.val(data);
                        $signed_fs.show();
                        $unsigned_fs.hide();
                        $("#<?=$modelName?>_signature_date_text").text(
                            new Date().toLocaleDateString("en-GB",
                                { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
                            )
                        );
                        $("#<?=$modelName?>_signature_image").attr("src", "/ProtectedFile/view?id="+data+"&name=signature.jpg").show();
                        <?php if ($this->getController()->action->id == "view") { ?>
                        window.formHasChanged = false;
                        setTimeout(function () {
                            location.reload(true); // forces a reload from the server
                        }, 300);
                        <?php } ?>
                    }
                    else {
                        new OpenEyes.UI.Dialog.Alert({
                            content: 'We\'re sorry, there has been an error when decrypting your signature.'
                        }).open();
                        $pin.val("");
                    }
                }
            )
        });
    });
</script>