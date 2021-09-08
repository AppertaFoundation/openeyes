<?php
    /** @var Element_PatientSignature $element */
    $modelName = CHtml::modelName($element);
    $onSubmitJS = <<<JS
function(data) {
    $("#{$modelName}_protected_file_id").val(data);
    $("#{$modelName}_signature_image").attr("src", "/ProtectedFile/view?id="+data+"&name=signature.jpg").show();
    $("#{$modelName}_sign_later_note").hide();
    $("#{$modelName}_signature_date_text").text(new Date().toLocaleDateString("en-GB",
      { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
    ));
    $("#{$modelName}_date_row").show();
}
JS;

?>

<div class="element-fields row">
    <div style="<?=$element->required_checkbox_visible ? "": "display:none"?>">
        <?php echo $form->radioBoolean($element, "signatory_required") ?>
    </div>
    <div id="<?=CHtml::encode($modelName)?>_fields" style="<?=$element->signatory_required ? "" : "display:none"?>">
        <?php if ($element->signature_date_readonly) : ?>
                <fieldset class="row field-row <?= !$element->protected_file_id ? 'hide' : ''?>" id="<?=CHtml::encode($modelName)?>_date_row">
                    <div class="large-2 column">
                        <label><?=CHtml::encode($element->getAttributeLabel("signature_date"))?>:</label>
                    </div>
                    <div class="large-5 column end">
                        <p id="<?=CHtml::encode($modelName)?>_signature_date_text">
                            <?php
                                $time = strtotime($element->signature_date);
                            if ($time) {
                                echo date("j M Y, H:i", $time);
                            }
                            ?>
                        </p>
                        <?php echo $form->hiddenField($element, "signature_date"); ?>
                    </div>
                </fieldset>
        <?php else : ?>
            <?php echo $form->datePicker($element, 'signature_date', array('maxDate' => 'today'),
                array('style' => 'width: 110px;')) ?>
        <?php endif; ?>
        <fieldset class="row field-row" style="<?php if (count($element->getSignatoryPersonOptions()) == 1) {
            echo "display:none;";
                                               } ?>">
            <div class="large-2 column">
                <label><?=CHtml::encode($element->getAttributeLabel("signatory_person"))?>:</label>
            </div>
            <div class="large-10 column end">
                <div id="div_<?=CHtml::encode($modelName)?>_signatory_person">
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
        <?php foreach ($element->getAdditionalFields() as $field) {
            echo $form->textField($element, $field, array(), null, array('field' => 4));
        } ?>
        <fieldset class="row field-row">
            <div class="large-2 column">
                <label><?=CHtml::encode($element->getAttributeLabel("protected_file_id"))?>:</label>
            </div>
            <div class="large-10 column end">
                <div id="<?=CHtml::encode($modelName)?>_signature_capture_wrapper" style="<?=$element->protected_file_id ? 'display:none;' : ''?>">
                    <?php $this->widget('SignatureCapture', [
                        'buttonText' => 'Capture signature',
                        'submitURL'=> "/ProtectedFile/uploadBase64Image?name=signature.jpg",
                        'onSubmit' => $onSubmitJS,
                        'showMessage' => false,
                    ]); ?>
                </div>
            </div>
        </fieldset>
        <fieldset class="row field-row">
            <div class="large-2 column">&nbsp;</div>
            <div class="large-5 column end">
                <img id="<?=CHtml::encode($modelName)?>_signature_image" src="<?=$element->protected_file_id ? "/ProtectedFile/view?id=".CHtml::encode($element->protected_file_id)."&name=signature.jpg" : "//:0"?>" style="<?php if (!$element->protected_file_id) {
                    echo "display:none;";
                         }?> max-height: 170px;" alt="Signature"/>
                <?php if ($element->can_be_signed_in_view_mode && is_null($element->protected_file_id)) : ?>
                    <div class="alert-box info" id="<?=CHtml::encode($modelName)?>_sign_later_note">
                        Note: you can save this form now and capture the signature later.
                    </div>
                <?php endif; ?>
            </div>
        </fieldset>
        <?php echo $form->hiddenField($element, "protected_file_id") ?>
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
        $(document).on("change", "[name='<?=CHtml::encode($modelName);?>[signatory_person]']", function (e) {
          if($(e.target).val() == "<?=Element_PatientSignature::SIGNATORY_PERSON_PATIENT?>") {
            $("#div_<?=CHtml::encode($modelName)?>_signatory_name").hide();
          }
          else {
            $("#div_<?=CHtml::encode($modelName)?>_signatory_name").show();
            $("#<?=CHtml::encode($modelName)?>_signatory_name").focus();
          }
          $("#<?=CHtml::encode($modelName)?>_signatory_name").val("");
          $("#<?=CHtml::encode($modelName)?>_protected_file_id").val("");
          $("#<?=CHtml::encode($modelName)?>_signature_image").attr("src", "//:0").hide();
          $("#<?=CHtml::encode($modelName)?>_signature_capture_wrapper").show();
        });

        $(document).on("change", "[name='<?=CHtml::encode($modelName);?>[signatory_required]']", function (e) {
          if($(e.target).val() == 1) {
            $("#<?=CHtml::encode($modelName)?>_fields").show();
          }
          else {
            $("#<?=CHtml::encode($modelName)?>_fields").hide();
          }
        });
    });
</script>
