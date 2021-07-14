<?php
    /** @var Element_PatientSignature $element */

    $icon_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.img')."/icon_info.png");

    $modelName = CHtml::modelName($element);
    $module = Yii::app()->controller->module->id;
    $onSubmitJS = <<<JS
function(data) {
    $("#{$modelName}_protected_file_id").val(data);
    $("#{$modelName}_signature_image").attr("src", "/ProtectedFile/view?id="+data+"&name=signature.jpg").show();
    $("#{$modelName}_sign_later_note").hide();
    $("#{$modelName}_signature_date_text").text(new Date().toLocaleDateString("en-GB",
      { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
    ));
}
JS;

?>

<div class="element-fields row">
    <div style="<?=$element->required_checkbox_visible ? "": "display:none"?>">
        <?php if($element->signatory_person == $element::SIGNATORY_PERSON_WITNESS): ?>
        <p>
            <img id="<?=$modelName?>_witness_tooltip" alt="Guidance" src="<?= $icon_path ?>" style="height: 20px" class="help-icon" data-tooltip-content="If a patient is unable to sign and has only provided a mark above or is unable to sign at all, a witness should sign below to indicate that the patient has consented (and please explain this in the medical records)." />
        </p>
        <?php endif; ?>
        <?php echo $form->radioBoolean($element, "signatory_required") ?>
    </div>
    <div id="<?=$modelName?>_fields" style="<?=$element->signatory_required ? "" : "display:none"?>">
        <?php if($element->signature_date_readonly): ?>
        <fieldset class="row field-row">
            <div class="large-2 column">
                <label><?=$element->getAttributeLabel("signature_date")?>:</label>
            </div>
            <div class="large-5 column end">
                <p id="<?=$modelName?>_signature_date_text">
                    <?php
                        $time = strtotime($element->signature_date);
                        if($time && $element->isSigned()) {
                            echo date("j M Y, H:i", $time);
                        }
                        else {
                            echo "-";
                        }
                    ?>
                </p>
                <?php echo $form->hiddenField($element, "signature_date"); ?>
            </div>
        </fieldset>
        <?php else: ?>
            <?php echo $form->datePicker($element, 'signature_date', array('maxDate' => 'today'),
                array('style' => 'width: 110px;')) ?>
        <?php endif; ?>
        <fieldset class="row field-row" style="<?php if(count($element->getSignatoryPersonOptions()) == 1) { echo "display:none;"; } ?>">
            <div class="large-2 column">
                <label><?=$element->getAttributeLabel("signatory_person")?>:</label>
            </div>
            <div class="large-10 column end">
                <div id="div_<?=$modelName?>_signatory_person">
                    <?php echo $form->radioButtons($element, 'signatory_person',
                        $element->getSignatoryPersonOptions(),
                        is_null($element->signatory_person) ? Element_PatientSignature::SIGNATORY_PERSON_PATIENT : $element->signatory_person,
                        false, false, false, false,
                        array('nowrapper' => true)
                    ); ?>
                </div>
            </div>
        </fieldset>
        <?php echo $form->textField($element, 'signatory_name', array('hide' => is_null($element->signatory_person) || $element->signatory_person == Element_PatientSignature::SIGNATORY_PERSON_PATIENT), null, array('field' => 4)) ?>
        <fieldset class="row field-row" id="<?=$modelName?>_signature_capture_validate">
            <div class="large-2 column">
                <label><?=$element->getAttributeLabel("protected_file_id")?>:</label>
            </div>
            <div class="large-10 column end">
                <?php $element->protected_file_id || $this->widget('SignatureCapture', [
                    'buttonText' => 'Capture signature',
                    'submitURL'=> "/ProtectedFile/uploadBase64Image?name=signature.jpg",
                    'onSubmit' => $onSubmitJS,
                    'showMessage' => false,
                ]); ?>
                <?php // TODO remove "false &&" when this button is handled properly
                    if (false && !$element->isNewRecord && !$element->protected_file_id) : ?>
                    <button id="<?=$modelName?>_requestSignature"
                            type="button"
                            data-element_type_id="<?=$element->elementType->id?>"
                            data-event_id="<?=$element->event_id?>"
                            class="button small success">Request signature</button>
                <?php endif; ?>
            </div>
        </fieldset>
        <fieldset class="row field-row">
            <div class="large-2 column">&nbsp;</div>
            <div class="large-5 column end">
                <img id="<?=$modelName?>_signature_image" src="<?=$element->protected_file_id ? "/ProtectedFile/view?id=".$element->protected_file_id."&name=signature.jpg" : ""?>" style="<?php if(!$element->protected_file_id){ echo "display:none;"; }?> max-height: 170px;" alt="Signature"/>
                <?php if($element->can_be_signed_in_view_mode && is_null($element->protected_file_id)): ?>
                    <div class="alert-box info" id="<?=$modelName?>_sign_later_note">
                        Note: you can save this form now and capture the signature later.
                    </div>
                <?php endif; ?>
            </div>
        </fieldset>
        <?php echo $form->hiddenField($element, "protected_file_id") ?>
        <?php foreach ($element->getAdditionalFields() as $field) {
            echo $form->textField($element, $field, array(), null, array('field' => 4));
        } ?>
        <?php foreach ($this->additional_edit_mode_templates as $template) {
            $this->render($template, [
                "form" => $form,
                "element" => $element
            ]);
        }?>
    </div>
</div>
<script type="text/javascript">
    $(function () {

      var witness_signature_tooltip = new OpenEyes.UI.Tooltip({
        className: 'patient_general_information_tooltip tooltip',
      });

      var $icon = $("#<?=$modelName?>_witness_tooltip");

      witness_signature_tooltip.setContent($icon.attr("data-tooltip-content"));

      $icon.on('mouseover', function() {
        var offsets = $(this).offset();
        witness_signature_tooltip.show(offsets.left + 20, offsets.top);
      }).mouseout(function (e) {
        witness_signature_tooltip.hide();
      });

      $(document).on("change", "[name='<?=$modelName;?>[signatory_person]']", function (e) {
          if($(e.target).val() == "<?=Element_PatientSignature::SIGNATORY_PERSON_PATIENT?>") {
            $("#div_<?=$modelName?>_signatory_name").hide();
          }
          else {
            $("#div_<?=$modelName?>_signatory_name").show();
            $("#<?=$modelName?>_signatory_name").focus();
          }
          $("#<?=$modelName?>_signatory_name").val("");
          $("#<?=$modelName?>_protected_file_id").val("");
          $("#<?=$modelName?>_signature_image").attr("src", "//:0").hide();
        });

        $(document).on("change", "[name='<?=$modelName;?>[signatory_required]']", function (e) {
          if($(e.target).val() == 1) {
            $("#<?=$modelName?>_fields").show();
          }
          else {
            $("#<?=$modelName?>_fields").hide();
          }
        });

      $('#<?=$modelName?>_requestSignature').on('click', function() {
        $.post('/<?=$module?>/default/registerSignRequest/', {
          'event_id': $(this).data('event_id'),
          'element_type_id': $(this).data('element_type_id'),
          YII_CSRF_TOKEN: YII_CSRF_TOKEN
        }, function( data ) {

          var dlg;
          var interval;

          dlg = new OpenEyes.UI.Dialog({
            content: "The request has been sent. Waiting for the signature to be completed...",
            title: "Waiting for signature"
          });

          interval = setInterval(function(){
            $.get("/<?=$module?>/default/pollSignatureRequest?request_id=" + data,
              function (response) {
                if(response !== "0" && !isNaN(response)) {
                  $("#<?=$modelName?>_protected_file_id").val(response);
                  $("#<?=$modelName?>_signature_image").attr("src", "/ProtectedFile/view?id="+response+"&name=signature.jpg").show();
                  $("#<?=$modelName?>_sign_later_note").hide();
                  $("#<?=$modelName?>_signature_date_text").text(new Date().toLocaleDateString("en-GB",
                    { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
                  ));
                  dlg.close();
                  clearInterval(interval);
                }
              });
          }, 2000);

          dlg
            .on("dialogclose", function(){
                clearInterval(interval);
            })
            .open();
        });
      });
    });

</script>
